<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Student\Requests\StudentInscriptionRequest;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\Student\Resources\StudentSessionResource;
use App\Modules\Parent\Models\ParentModel;
use App\Modules\User\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Mails\UserWelcomeMail;

class StudentInscriptionController extends Controller
{
    public function store(StudentInscriptionRequest $request)
    {
        $parentPassword = bin2hex(random_bytes(4));
        $studentPassword = bin2hex(random_bytes(4));
        $parentUser = UserModel::where('email', $request->parent_email)->first();
        $parentJustCreated = false;
        if (!$parentUser) {
            $parentUser = UserModel::create([
                'first_name' => $request->parent_first_name,
                'last_name' => $request->parent_last_name,
                'birthday' => $request->parent_birthday,
                'gender' => $request->parent_gender,
                'email' => $request->parent_email,
                'password' => Hash::make($parentPassword),
                'phone' => $request->parent_phone,
                'adress' => $request->parent_adress ?? null,
                'role_id' => $request->parent_role_id ?? null,
            ]);
            $parentJustCreated = true;
        }
        $parentModel = ParentModel::firstOrCreate([
            'user_model_id' => $parentUser->id,
        ]);
        $studentUser = UserModel::where('email', $request->student_email)->first();
        $studentJustCreated = false;
        if (!$studentUser) {
            $studentUser = UserModel::create([
                'first_name' => $request->student_first_name,
                'last_name' => $request->student_last_name,
                'birthday' => $request->student_birthday,
                'gender' => $request->student_gender,
                'email' => $request->student_email,
                'password' => Hash::make($studentPassword),
                'phone' => $request->student_phone,
                'adress' => $request->student_adress ?? null,
                'role_id' => $request->student_role_id ?? null,
            ]);
            $studentJustCreated = true;
        }
        $matricule = Student::generateMatricule();
        $student = Student::create([
            'matricule' => $matricule,
            'parent_model_id' => $parentModel->id,
            'user_model_id' => $studentUser->id,
            'academic_records' => '', // not used for file storage
        ]);
        $studentSession = StudentSession::create([
            'student_id' => $student->id,
            'class_model_id' => $request->class_model_id,
            'academic_year_id' => $request->academic_year_id,
            'justificatif_path' => '', // initialize justificatif_path to empty string
        ]);
        // Accept only academic_records as file field name
        $file = $request->file('academic_records');
        if ($file) {
            $path = $file->store('justificatifs', 'public');
            $studentSession->justificatif_path = $path;
            $studentSession->save();
            // Also save path in Student model for academic_records_url
            $student->academic_records = $path;
            $student->save();
        }
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

        $student->load(['parentModel', 'userModel', 'latestStudentSession']);
        $studentSession->load('student');

        return response()->json([
            'student' => new StudentResource($student),
            'student_session' => new StudentSessionResource($studentSession),
            'academic_records_url' => $student->academic_records_url,
            'parent_password' => $parentJustCreated ? $parentPassword : null,
            'student_password' => $studentJustCreated ? $studentPassword : null,
        ], 201);
    }
}
