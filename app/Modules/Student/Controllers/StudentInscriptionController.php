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
        // Création ou récupération du compte utilisateur pour le parent
        $parentUser = UserModel::firstOrCreate([
            'email' => $request->parent_email
        ], [
            'first_name' => $request->parent_first_name,
            'last_name' => $request->parent_last_name,
            'birthday' => $request->parent_birthday,
            'gender' => $request->parent_gender,
            'password' => Hash::make($request->parent_password),
            'phone' => $request->parent_phone,
            'adress' => $request->parent_adress ?? null,
            'role_id' => $request->parent_role_id ?? null,
        ]);
        // Création ou récupération du modèle parent
        $parentModel = ParentModel::firstOrCreate([
            'user_model_id' => $parentUser->id,
        ]);

        // Création ou récupération du compte utilisateur pour l'élève
        $studentUser = UserModel::firstOrCreate([
            'email' => $request->student_email
        ], [
            'first_name' => $request->student_first_name,
            'last_name' => $request->student_last_name,
            'birthday' => $request->student_birthday,
            'gender' => $request->student_gender,
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
        ]);

        // Création de la session d'inscription
        $studentSession = StudentSession::create([
            'student_id' => $student->id,
            'class_model_id' => $request->class_model_id,
            'academic_year_id' => $request->academic_year_id,
        ]);

        // Upload du justificatif (academic-records) et stockage dans Student
        if ($request->hasFile('academic-records')) {
            $path = $request->file('academic-records')->store('justificatifs', 'public');
            $student->academic_records = $path;
            $student->save();
        }

        $student->load(['parentModel', 'userModel', 'latestStudentSession']);
        $studentSession->load('student');

        return response()->json([
            'student' => new StudentResource($student),
            'student_session' => new StudentSessionResource($studentSession),
            'academic_records_url' => $student->academic_records_url,
        ], 201);
    }
}
