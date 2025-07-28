<?php

namespace App\Modules\ClassModel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\ClassModel\Requests\ClassModelRequest;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\Student;

class ClassModelController extends Controller
{
    public function index()
    {
        return response()->json(ClassModelResource::collection(ClassModel::all()));
    }

    public function store(ClassModelRequest $request)
    {
        return  response()->json(new  ClassModelResource(ClassModel::create($request->validated()))) ;
    }

    public function show($classId)
    {
        $classModel = ClassModel::findOrFail($classId);
        return response()->json(new ClassModelResource($classModel));
    }

    public function update(ClassModelRequest $request, ClassModel $classModel)
    {
        $classModel->update($request->validated());

        return response()->json(new ClassModelResource($classModel));
    }

    public function destroy(ClassModel $classModel)
    {
        $classModel->delete();

        return response()->json();
    }

    public function getStudentsByClass($classId)
    {
        $currentAcademicYear = \App\Modules\AcademicYear\Models\AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return response()->json(['message' => 'No current academic year found.'], 404);
        }

        $students = \App\Modules\Student\Models\Student::whereHas('studentSessions', function ($query) use ($classId, $currentAcademicYear) {
            $query->where('class_model_id', $classId)
                  ->where('academic_year_id', $currentAcademicYear->id);
        })
        ->with(['userModel', 'studentSessions']) // Eager load userModel and studentSessions
        ->get();

        // Transform the students to match the PRD expected response format
        $formattedStudents = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->userModel->name, // Assuming userModel has a name attribute
                'matricule' => $student->matricule,
                'user_model_id' => $student->user_model_id,
                'student_session_id' => $student->studentSessions->firstWhere('academic_year_id', \App\Modules\AcademicYear\Models\AcademicYear::getCurrentAcademicYear()->id)?->id, // Safely get student_session_id
            ];
        });

        return response()->json($formattedStudents);
    }
}
