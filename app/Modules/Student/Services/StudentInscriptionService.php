<?php

namespace App\Modules\Student\Services;

use App\Mails\UserWelcomeMail;
use App\Modules\Parent\Models\ParentModel;
use App\Modules\Student\Exceptions\StudentInscriptionException;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentSession;
use App\Modules\User\Models\UserModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentInscriptionService
{
    public function processInscription(array $data, ?UploadedFile $academicRecordsFile = null, ?UploadedFile $photoFile = null): array
    {
        DB::beginTransaction();

        try {
            // Create or get parent user
            $parentResult = $this->handleParentCreation($data);
            $parentUser = $parentResult['user'];
            $parentModel = $parentResult['parent_model'];
            $parentJustCreated = $parentResult['just_created'];
            $parentPassword = $parentResult['password'];

            // Create or get student user
            $studentResult = $this->handleStudentCreation($data);
            $studentUser = $studentResult['user'];
            $studentJustCreated = $studentResult['just_created'];
            $studentPassword = $studentResult['password'];

            // Create student
            $student = $this->createStudent($parentModel->id, $studentUser->id);

            // Create student session
            $studentSession = $this->createStudentSession($student->id, $data);

            // Handle file upload if provided
            if ($academicRecordsFile) {
                $this->handleFileUpload($academicRecordsFile, $student, $studentSession);
            }
            // Handle student photo upload if provided
            if ($photoFile) {
                $this->handlePhotoUpload($photoFile, $student);
            }

            // Send welcome emails
            $this->sendWelcomeEmails($parentUser, $parentPassword, $parentJustCreated, $studentUser, $studentPassword, $studentJustCreated);

            // Load relationships
            $student->load(['parentModel', 'userModel', 'latestStudentSession']);
            $studentSession->load('student');

            DB::commit();

            return [
                'student' => $student,
                'student_session' => $studentSession,
                'parent_password' => $parentJustCreated ? $parentPassword : null,
                'student_password' => $studentJustCreated ? $studentPassword : null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student inscription failed: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);

            if ($e instanceof StudentInscriptionException) {
                throw $e;
            }

            throw StudentInscriptionException::inscriptionProcessFailed();
        }
    }

    public function handleParentCreation(array $data): array
    {
        $parentUser = UserModel::where('email', $data['parent_email'])->first();
        $parentPassword = $this->generatePassword();
        $parentJustCreated = false;

        if (!$parentUser) {
            $parentUser = $this->createUser([
                'first_name' => $data['parent_first_name'],
                'last_name' => $data['parent_last_name'],
                'birthday' => $data['parent_birthday'],
                'gender' => $data['parent_gender'],
                'email' => $data['parent_email'],
                'password' => Hash::make($parentPassword),
                'phone' => $data['parent_phone'],
                'adress' => $data['parent_adress'] ?? null,
                'role_id' => $data['parent_role_id'] ?? null,
            ], 'parent');

            $parentJustCreated = true;
        }

        $parentModel = $this->createOrGetParentModel($parentUser->id);

        return [
            'user' => $parentUser,
            'parent_model' => $parentModel,
            'just_created' => $parentJustCreated,
            'password' => $parentPassword,
        ];
    }

    public function handleStudentCreation(array $data): array
    {
        $studentUser = UserModel::where('email', $data['student_email'])->first();
        $studentPassword = $this->generatePassword();
        $studentJustCreated = false;

        if (!$studentUser) {
            $studentUser = $this->createUser([
                'first_name' => $data['student_first_name'],
                'last_name' => $data['student_last_name'],
                'birthday' => $data['student_birthday'],
                'gender' => $data['student_gender'],
                'email' => $data['student_email'],
                'password' => Hash::make($studentPassword),
                'phone' => $data['student_phone'],
                'adress' => $data['student_adress'] ?? null,
                'role_id' => $data['student_role_id'] ?? null,
            ], 'student');

            $studentJustCreated = true;
        }

        return [
            'user' => $studentUser,
            'just_created' => $studentJustCreated,
            'password' => $studentPassword,
        ];
    }

    private function createUser(array $userData, string $userType): UserModel
    {
        try {
            return UserModel::create($userData);
        } catch (\Exception $e) {
            Log::error("Failed to create {$userType} user", [
                'user_data' => $userData,
                'exception' => $e
            ]);
            throw StudentInscriptionException::userCreationFailed($userType);
        }
    }

    private function createOrGetParentModel(int $userId): ParentModel
    {
        try {
            return ParentModel::firstOrCreate([
                'user_model_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create parent model', [
                'user_id' => $userId,
                'exception' => $e
            ]);
            throw StudentInscriptionException::parentCreationFailed();
        }
    }

    private function createStudent(int $parentModelId, int $userModelId): Student
    {
        try {
            $matricule = Student::generateMatricule();

            return Student::create([
                'matricule' => $matricule,
                'parent_model_id' => $parentModelId,
                'user_model_id' => $userModelId,
                'academic_records' => '',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create student', [
                'parent_model_id' => $parentModelId,
                'user_model_id' => $userModelId,
                'exception' => $e
            ]);
            throw StudentInscriptionException::studentCreationFailed();
        }
    }

    private function createStudentSession(int $studentId, array $data): StudentSession
    {
        try {
            return StudentSession::create([
                'student_id' => $studentId,
                'class_model_id' => $data['class_model_id'],
                'academic_year_id' => $data['academic_year_id'],
                'justificatif_path' => '',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create student session', [
                'student_id' => $studentId,
                'class_model_id' => $data['class_model_id'],
                'academic_year_id' => $data['academic_year_id'],
                'exception' => $e
            ]);
            throw StudentInscriptionException::studentSessionCreationFailed();
        }
    }

    private function

    handleFileUpload(UploadedFile $file, Student $student, StudentSession $studentSession): void
    {
        try {
            $path = $file->store('justificatifs', 'public');

            $studentSession->justificatif_path = $path;
            $studentSession->save();

            $student->academic_records = $path;
            $student->save();
        } catch (\Exception $e) {
            Log::error('Failed to handle file upload', [
                'student_id' => $student->id,
                'student_session_id' => $studentSession->id,
                'exception' => $e
            ]);
            throw StudentInscriptionException::fileUploadFailed();
        }
    }

    private function handlePhotoUpload(UploadedFile $file, Student $student): void
    {
        try {
            $path = $file->store('photos', 'public');
            $student->photo = $path;
            $student->save();
        } catch (\Exception $e) {
            Log::error('Failed to handle student photo upload', [
                'student_id' => $student->id,
                'exception' => $e
            ]);
            throw StudentInscriptionException::fileUploadFailed();
        }
    }

    private function sendWelcomeEmails(
        UserModel $parentUser,
        string $parentPassword,
        bool $parentJustCreated,
        UserModel $studentUser,
        string $studentPassword,
        bool $studentJustCreated
    ): void {
        try {
            if ($parentJustCreated) {
                Mail::to($parentUser->email)->send(new UserWelcomeMail(
                    $parentUser->first_name . ' ' . $parentUser->last_name,
                    $parentUser->email,
                    $parentPassword,
                    'Parent'
                ));
            }

            if ($studentJustCreated) {
                Mail::to($studentUser->email)->send(new UserWelcomeMail(
                    $studentUser->first_name . ' ' . $studentUser->last_name,
                    $studentUser->email,
                    $studentPassword,
                    'Étudiant'
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send welcome emails', [
                'parent_email' => $parentUser->email,
                'student_email' => $studentUser->email,
                'exception' => $e
            ]);
            // Don't throw exception for email failures, just log it
            // The inscription should still be considered successful
        }
    }

    private function generatePassword(): string
    {
        return bin2hex(random_bytes(4));
    }
}
