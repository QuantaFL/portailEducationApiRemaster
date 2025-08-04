<?php

namespace App\Modules\Teacher\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
use App\Modules\User\Models\UserModel;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Services\AssignmentService;
use App\Modules\Teacher\Services\TeacherContractService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

class TeacherService
{
    protected AssignmentService $assignmentService;
    protected TeacherContractService $contractService;

    public function __construct(AssignmentService $assignmentService, TeacherContractService $contractService)
    {
        $this->assignmentService = $assignmentService;
        $this->contractService = $contractService;
    }
    /**
     * Get all teachers
     */
    public function getAllTeachers(): Collection
    {
        Log::info('TeacherService: Fetching all teachers');

        $teachers = Teacher::with(['userModel', 'subjects', 'assignments'])->get();

        Log::info('TeacherService: Retrieved ' . $teachers->count() . ' teachers');

        return $teachers;
    }

    /**
     * Create a new teacher with automatic assignment and contract email
     * This is the main method that handles the complete teacher creation process
     */
    public function createTeacher(array $validatedData, ?UploadedFile $photoFile = null, ?UploadedFile $cvFile = null, ?UploadedFile $diplomasFile = null): Teacher
    {
        Log::info('TeacherService: Starting complete teacher creation process', [
            'hire_date' => $validatedData['hire_date'],
            'role_id' => $validatedData['role_id'] ?? null,
            'nationality' => $validatedData['nationality'] ?? null,
            'user_email' => $validatedData['user']['email'] ?? null,
            'assignment_data' => $validatedData['assignment'] ?? null,
            'has_photo' => $photoFile !== null,
            'has_cv' => $cvFile !== null,
            'has_diplomas' => $diplomasFile !== null
        ]);

        DB::beginTransaction();

        try {
            // Step 1: Create the teacher
            $teacher = $this->createTeacherOnly($validatedData);

            // Step 2: Handle file uploads
            if ($photoFile) {
                $this->handlePhotoUpload($photoFile, $teacher);
            }
            if ($cvFile) {
                $this->handleCvUpload($cvFile, $teacher);
            }
            if ($diplomasFile) {
                $this->handleDiplomasUpload($diplomasFile, $teacher);
            }

            // Step 3: Create assignment if assignment data is provided
            $assignment = null;
            if (isset($validatedData['assignment'])) {
                $assignment = $this->createTeacherAssignment($teacher, $validatedData['assignment']);
            }

            // Step 4: Generate and send contract email
            $this->sendTeacherContract($teacher);

            DB::commit();

            Log::info('TeacherService: Complete teacher creation process finished successfully', [
                'teacher_id' => $teacher->id,
                'user_id' => $teacher->user_model_id,
                'assignment_id' => $assignment?->id,
                'assignment_number' => $assignment?->assignment_number,
                'contract_sent' => true,
                'files_uploaded' => [
                    'photo' => $photoFile !== null,
                    'cv' => $cvFile !== null,
                    'diplomas' => $diplomasFile !== null
                ]
            ]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TeacherService: Complete teacher creation process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData
            ]);

            throw $e;
        }
    }

    /**
     * Create teacher only (without assignment and email)
     */
    public function createTeacherOnly(array $validatedData): Teacher
    {
        Log::info('TeacherService: Creating teacher record only', [
            'user_email' => $validatedData['user']['email'] ?? null
        ]);

        try {
            // Prepare user data with role_id and nationality
            $userData = $validatedData['user'];
            $userData['role_id'] = $validatedData['role_id'];

            if (isset($validatedData['nationality'])) {
                $userData['nationality'] = $validatedData['nationality'];
            }

            // Create user
            $user = UserModel::create($userData);
            Log::info('TeacherService: User created successfully', ['user_id' => $user->id]);

            // Create teacher
            $teacher = Teacher::create([
                'hire_date' => $validatedData['hire_date'],
                'user_model_id' => $user->id,
                'status'=>1
            ]);

            Log::info('TeacherService: Teacher record created successfully', [
                'teacher_id' => $teacher->id,
                'user_id' => $user->id
            ]);

            return $teacher;

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to create teacher record', [
                'error' => $e->getMessage(),
                'user_email' => $validatedData['user']['email'] ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Create assignment for teacher
     */
    protected function createTeacherAssignment(Teacher $teacher, array $assignmentData): Assignement
    {
        Log::info('TeacherService: Creating assignment for teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $assignmentData['subject_id'] ?? null,
            'class_model_id' => $assignmentData['class_model_id'] ?? null
        ]);

        try {
            $assignment = $this->assignmentService->createAssignmentForTeacher(
                $teacher->id,
                $assignmentData['subject_id'],
                $assignmentData['class_model_id'],
                array_filter([
                    'day_of_week' => $assignmentData['day_of_week'] ?? null,
                    'start_time' => $assignmentData['start_time'] ?? null,
                    'end_time' => $assignmentData['end_time'] ?? null,
                    'coefficient' => $assignmentData['coefficient'] ?? null,
                ])
            );

            Log::info('TeacherService: Assignment created for teacher', [
                'teacher_id' => $teacher->id,
                'assignment_id' => $assignment->id,
                'assignment_number' => $assignment->assignment_number
            ]);

            return $assignment;

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to create assignment for teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage(),
                'assignment_data' => $assignmentData
            ]);
            throw $e;
        }
    }

    /**
     * Send contract email to teacher
     */
    protected function sendTeacherContract(Teacher $teacher): void
    {
        Log::info('TeacherService: Sending contract to teacher', [
            'teacher_id' => $teacher->id,
            'user_email' => $teacher->userModel->email
        ]);

        try {
            $this->contractService->generateAndSendContract($teacher);

            Log::info('TeacherService: Contract sent successfully to teacher', [
                'teacher_id' => $teacher->id,
                'user_email' => $teacher->userModel->email
            ]);

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to send contract to teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);

            // Don't throw the exception here to avoid rolling back the entire transaction
            // The teacher and assignment are created successfully, only email failed
            Log::warning('TeacherService: Teacher creation completed but email sending failed');
        }
    }

    /**
     * Update teacher information
     */
    public function updateTeacher(Teacher $teacher, array $validatedData): Teacher
    {
        Log::info('TeacherService: Updating teacher', ['teacher_id' => $teacher->id]);

        try {
            $refUser = UserModel::findOrFail($teacher->user_model_id);

            Log::info('TeacherService: Found user to update', ['user_id' => $refUser->id]);

            $refUser->update($validatedData['user']);

            Log::info('TeacherService: Teacher updated successfully', ['teacher_id' => $teacher->id]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to update teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a teacher
     */
    public function deleteTeacher(Teacher $teacher): bool
    {
        Log::info('TeacherService: Deleting teacher', ['teacher_id' => $teacher->id]);

        try {
            $teacher->delete();

            Log::info('TeacherService: Teacher deleted successfully', ['teacher_id' => $teacher->id]);

            return true;

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to delete teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get teacher subjects for current academic year
     */
    public function getTeacherSubjects(int $teacherId): Collection
    {
        Log::info('TeacherService: Getting subjects for teacher', ['teacher_id' => $teacherId]);

        $academicYear = AcademicYear::getCurrentAcademicYear();

        if (!$academicYear) {
            Log::warning('TeacherService: No current academic year found');
            throw new \Exception('No current academic year found');
        }

        Log::info('TeacherService: Using academic year', ['academic_year_id' => $academicYear->id]);

        $subjects = Assignement::where('teacher_id', $teacherId)
            ->join('terms', 'assignments.term_id', '=', 'terms.id')
            ->join('academic_years', 'terms.academic_year_id', '=', 'academic_years.id')
            ->where('academic_years.id', $academicYear->id)
            ->with('subject')
            ->get()
            ->pluck('subject');

        Log::info('TeacherService: Found subjects for teacher', [
            'teacher_id' => $teacherId,
            'subjects_count' => $subjects->count()
        ]);

        return $subjects;
    }

    /**
     * Get teacher classes for current academic year
     */
    public function getTeacherClasses(int $teacherId): Collection
    {
        Log::info('TeacherService: Getting classes for teacher', ['teacher_id' => $teacherId]);

        $academicYear = AcademicYear::getCurrentAcademicYear();

        if (!$academicYear) {
            Log::warning('TeacherService: No current academic year found');
            throw new \Exception('No current academic year found');
        }

        $classIds = Assignement::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYear->id)
            ->pluck('class_model_id')
            ->unique()
            ->toArray();

        Log::info('TeacherService: Found class IDs for teacher', [
            'teacher_id' => $teacherId,
            'class_ids' => $classIds
        ]);

        $classes = ClassModel::whereIn('id', $classIds)->get();

        Log::info('TeacherService: Retrieved classes for teacher', [
            'teacher_id' => $teacherId,
            'classes_count' => $classes->count()
        ]);

        return $classes;
    }

    /**
     * Get teacher by user ID
     */
    public function getTeacherByUserId(int $userId): ?Teacher
    {
        Log::info('TeacherService: Getting teacher by user ID', ['user_id' => $userId]);

        $teacher = Teacher::where('user_model_id', $userId)->first();

        if ($teacher) {
            Log::info('TeacherService: Teacher found', ['teacher_id' => $teacher->id, 'user_id' => $userId]);
        } else {
            Log::warning('TeacherService: No teacher found for user ID', ['user_id' => $userId]);
        }

        return $teacher;
    }

    /**
     * Get teacher profile with assigned subjects
     */
    public function getTeacherProfile(UserModel $user): array
    {
        Log::info('TeacherService: Getting teacher profile', ['user_id' => $user->id]);

        $teacher = Teacher::where('user_model_id', $user->id)->first();

        if (!$teacher) {
            Log::warning('TeacherService: Teacher profile not found', ['user_id' => $user->id]);
            throw new \Exception('Teacher profile not found');
        }

        $assignedSubjects = Assignement::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->pluck(function ($assignement) {
                return optional($assignement->subject)->name;
            })
            ->unique()
            ->values()
            ->toArray();

        Log::info('TeacherService: Teacher profile retrieved', [
            'teacher_id' => $teacher->id,
            'assigned_subjects_count' => count($assignedSubjects)
        ]);

        return [
            'id' => $teacher->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'assigned_subjects' => $assignedSubjects,
        ];
    }

    /**
     * Get multi-class performance summary
     */
    public function getMultiClassPerformanceSummary(array $classSubjects): array
    {
        Log::info('TeacherService: Getting multi-class performance summary', [
            'class_subjects_count' => count($classSubjects)
        ]);

        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            Log::warning('TeacherService: No current term found');
            throw new \Exception('No current term found');
        }

        // For now, using teacher ID 1 as in original code
        // This should be improved to use authenticated teacher
        $teacher = Teacher::where('teachers.id', 1)->first();
        if (!$teacher) {
            Log::error('TeacherService: Teacher profile not found for performance summary');
            throw new \Exception('Teacher profile not found');
        }

        Log::info('TeacherService: Using teacher and term for performance summary', [
            'teacher_id' => $teacher->id,
            'term_id' => $currentTerm->id
        ]);

        $allPerformances = [];

        foreach ($classSubjects as $pair) {
            $class = ClassModel::find($pair['classId']);
            if (!$class) {
                Log::warning('TeacherService: Class not found', ['class_id' => $pair['classId']]);
                continue;
            }

            $studentsInClass = $class->currentAcademicYearStudentSessions()->pluck('student_id')->toArray();
            $students = Student::whereIn('id', $studentsInClass)->get();

            Log::info('TeacherService: Processing class for performance', [
                'class_id' => $pair['classId'],
                'subject_id' => $pair['subjectId'],
                'students_count' => $students->count()
            ]);

            foreach ($students as $student) {
                $studentSession = $student->latestStudentSession;
                if (!$studentSession) {
                    continue;
                }

                $grades = Grade::where('student_session_id', $studentSession->id)
                    ->where('term_id', $currentTerm->id)
                    ->whereHas('assignement', function ($query) use ($pair, $teacher) {
                        $query->where('subject_id', $pair['subjectId'])
                            ->where('teacher_id', $teacher->id);
                    })
                    ->get();

                if ($grades->isNotEmpty()) {
                    $averageGrade = $grades->avg('mark');
                    $allPerformances[] = [
                        'studentId' => $student->id,
                        'firstName' => $student->userModel->first_name,
                        'lastName' => $student->userModel->last_name,
                        'profilePictureUrl' => $student->userModel->profile_picture_url,
                        'classId' => $pair['classId'],
                        'subjectId' => $pair['subjectId'],
                        'averageGrade' => round($averageGrade, 2),
                    ];
                }
            }
        }

        if (empty($allPerformances)) {
            Log::warning('TeacherService: No grades found for performance summary');
            throw new \Exception('No grades found for the provided classes, subjects, and term');
        }

        $best = collect($allPerformances)->sortByDesc('averageGrade')->first();
        $worst = collect($allPerformances)->sortBy('averageGrade')->first();

        Log::info('TeacherService: Performance summary completed', [
            'total_performances' => count($allPerformances),
            'best_student_id' => $best['studentId'],
            'worst_student_id' => $worst['studentId']
        ]);

        return [
            'bestPerformingStudent' => $best,
            'worstPerformingStudent' => $worst,
        ];
    }

    /**
     * Toggle teacher status by ID
     */
    public function toggleTeacherStatus(int $teacherId): Teacher
    {
        Log::info('TeacherService: Toggling teacher status', [
            'teacher_id' => $teacherId
        ]);

        try {
            $teacher = Teacher::findOrFail($teacherId);

            // Si status est null, on le considère comme 1 (actif)
            $currentStatus = $teacher->status ?? true;
            $newStatus = !$currentStatus;

            Log::info('TeacherService: Current teacher status', [
                'teacher_id' => $teacherId,
                'current_status' => $currentStatus,
                'new_status' => $newStatus
            ]);

            $teacher->update(['status' => $newStatus]);

            Log::info('TeacherService: Teacher status toggled successfully', [
                'teacher_id' => $teacherId,
                'old_status' => $currentStatus,
                'new_status' => $newStatus
            ]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to toggle teacher status', [
                'teacher_id' => $teacherId,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Activate teacher status
     */
    public function activateTeacher(int $teacherId): Teacher
    {
        Log::info('TeacherService: Activating teacher', ['teacher_id' => $teacherId]);

        try {
            $teacher = Teacher::findOrFail($teacherId);
            $teacher->update(['status' => true]);

            Log::info('TeacherService: Teacher activated successfully', ['teacher_id' => $teacherId]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to activate teacher', [
                'teacher_id' => $teacherId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Deactivate teacher status
     */
    public function deactivateTeacher(int $teacherId): Teacher
    {
        Log::info('TeacherService: Deactivating teacher', ['teacher_id' => $teacherId]);

        try {
            $teacher = Teacher::findOrFail($teacherId);
            $teacher->update(['status' => false]);

            Log::info('TeacherService: Teacher deactivated successfully', ['teacher_id' => $teacherId]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to deactivate teacher', [
                'teacher_id' => $teacherId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle photo upload for teacher - similar to Student logic
     */
    private function handlePhotoUpload(UploadedFile $file, Teacher $teacher): void
    {
        try {
            $path = $file->store('teacher_photos', 'public');
            $teacher->photo = $path;
            $teacher->save();

            Log::info('TeacherService: Photo uploaded successfully', [
                'teacher_id' => $teacher->id,
                'photo_path' => $path
            ]);
        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to handle teacher photo upload', [
                'teacher_id' => $teacher->id,
                'exception' => $e
            ]);
            throw new \Exception('Failed to upload teacher photo');
        }
    }

    /**
     * Handle CV upload for teacher
     */
    private function handleCvUpload(UploadedFile $file, Teacher $teacher): void
    {
        try {
            $path = $file->store('teacher_cvs', 'public');
            $teacher->cv = $path;
            $teacher->save();

            Log::info('TeacherService: CV uploaded successfully', [
                'teacher_id' => $teacher->id,
                'cv_path' => $path
            ]);
        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to handle teacher CV upload', [
                'teacher_id' => $teacher->id,
                'exception' => $e
            ]);
            throw new \Exception('Failed to upload teacher CV');
        }
    }

    /**
     * Handle diplomas upload for teacher
     */
    private function handleDiplomasUpload(UploadedFile $file, Teacher $teacher): void
    {
        try {
            $path = $file->store('teacher_diplomas', 'public');
            $teacher->diplomas = $path;
            $teacher->save();

            Log::info('TeacherService: Diplomas uploaded successfully', [
                'teacher_id' => $teacher->id,
                'diplomas_path' => $path
            ]);
        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to handle teacher diplomas upload', [
                'teacher_id' => $teacher->id,
                'exception' => $e
            ]);
            throw new \Exception('Failed to upload teacher diplomas');
        }
    }
}
