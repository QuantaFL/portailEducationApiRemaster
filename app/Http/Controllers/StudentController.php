<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        return StudentResource::collection(Student::all());
    }

    public function store(StudentRequest $request)
    {
        return new StudentResource(Student::create($request->validated()));
    }

    public function show(Student $student)
    {
        return new StudentResource($student);
    }

    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return new StudentResource($student);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json();
    }
}
