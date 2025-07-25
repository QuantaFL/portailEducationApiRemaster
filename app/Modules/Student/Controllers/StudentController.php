<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Requests\StudentRequest;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(StudentResource::collection(Student::all()));
    }

    public function store(StudentRequest $request)
    {
        return response()->json(new StudentResource(Student::create($request->validated())));
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
