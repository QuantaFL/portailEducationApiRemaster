<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Requests\StudentRequest;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\User\Models\UserModel;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(StudentResource::collection(Student::all()));
    }

    public function store(StudentRequest $request)
    {
        $userData = $request->validated('user');
        $user = UserModel::create($userData);

        $studentData = $request->validated();
        $student = Student::create([
            'matricule' => $studentData['matricule'],
            'academic_records' => $studentData['academic_records'],
            'parent_model_id' => $studentData['parent_id'],
            'user_model_id' => $user->id,
        ]);

        return response()->json(new StudentResource($student));
    }

    public function show(Student $student)
    {
        return response()->json(new StudentResource($student));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return response()->json(new StudentResource($student));
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json();
    }
}
