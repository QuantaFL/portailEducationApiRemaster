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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentInscriptionController extends Controller
{
    public function store(StudentInscriptionRequest $request)
    {
        // Vérifier si l'utilisateur parent existe déjà
        $parentUser = UserModel::where('email', $request->parent_email)->first();
        if (!$parentUser) {
            // Création du compte utilisateur pour le parent
            $parentUser = UserModel::create([
                'first_name' => $request->parent_first_name,
                'last_name' => $request->parent_last_name,
                'birthday' => $request->parent_birthday,
                'gender' => $request->parent_gender,
                'email' => $request->parent_email,
                'password' => Hash::make($request->parent_password),
                'phone' => $request->parent_phone,
                'adress' => $request->parent_adress ?? null,
                'role_id' => $request->parent_role_id ?? null,
            ]);
        }
        $parentModel = ParentModel::create([
            'user_model_id' => $parentUser->id,
        ]);

        // Création du compte utilisateur pour l'élève
        $studentUser = UserModel::create([
            'first_name' => $request->student_first_name,
            'last_name' => $request->student_last_name,
            'birthday' => $request->student_birthday,
            'gender' => $request->student_gender,
            'email' => $request->student_email,
            'password' => Hash::make($request->student_password),
            'phone' => $request->student_phone,
            'adress' => $request->student_adress ?? null,
            'role_id' => $request->student_role_id ?? null,
        ]);

        // Création de l'élève
        $student = Student::create([
            'matricule' => $request->student_matricule,
            'parent_model_id' => $parentModel->id,
            'user_model_id' => $studentUser->id,
            'academic_records' => $request->input('academic-records', ''),
            'class_model_id' => $request->class_model_id,
        ]);

        // Création de la session d'inscription (ajout class_model_id ici)
        $studentSession = StudentSession::create([
            'student_id' => $student->id,
            'class_model_id' => $request->class_model_id,
            'academic_year_id' => $request->academic_year_id,
        ]);

        // Upload du justificatif et stockage dans Student
        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $student->academic_records = $path;
            $student->save();
        }

        $student->load(['parentModel', 'userModel', 'latestStudentSession']);
        $studentSession->load('student');

        return response()->json([
            'student' => new StudentResource($student),
            'student_session' => new StudentSessionResource($studentSession),
            'justificatif_url' => $student->academic_records_url,
        ], 201);
    }
}
