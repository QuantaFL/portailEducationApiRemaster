<?php

namespace App\Modules\ClassModel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\ClassModel\Requests\ClassModelRequest;
use App\Modules\ClassModel\Requests\AssignSubjectRequest;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use Illuminate\Support\Facades\Log;

class ClassModelController extends Controller
{
    public function index()
    {
        $classes = ClassModel::with(['subjects', 'currentAcademicYearStudentSessions'])->get();
        return response()->json(ClassModelResource::collection($classes));
    }

    public function store(ClassModelRequest $request)
    {
        return  response()->json(new  ClassModelResource(ClassModel::create($request->validated()))) ;
    }

    public function show($classId)
    {
        $classModel = ClassModel::with(['subjects', 'currentAcademicYearStudentSessions'])->findOrFail($classId);
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

    /**
     * Affecter une matière à une classe
     */
    public function assignSubject(AssignSubjectRequest $request)
    {
        try {
            $validated = $request->validated();
            $classId = $validated['class_id'];
            $subjectId = $validated['subject_id'];

            Log::info('ClassModelController: Assign subject request received', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'user_agent' => request()->header('User-Agent'),
                'ip' => request()->ip()
            ]);

            $class = ClassModel::findOrFail($classId);
            $subject = Subject::findOrFail($subjectId);

            // Vérifier si la relation existe déjà
            if ($class->subjects()->where('subject_id', $subjectId)->exists()) {
                Log::warning('ClassModelController: Subject already assigned to class', [
                    'class_id' => $classId,
                    'subject_id' => $subjectId
                ]);
                return response()->json(['message' => 'Cette matière est déjà affectée à cette classe'], 409);
            }

            // Affecter la matière à la classe
            $class->subjects()->attach($subjectId);

            Log::info('ClassModelController: Subject assigned to class successfully', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'class_name' => $class->name,
                'subject_name' => $subject->name
            ]);

            // Retourner la classe avec ses matières
            $class->load('subjects');
            return response()->json(new ClassModelResource($class));

        } catch (\Exception $e) {
            Log::error('ClassModelController: Failed to assign subject to class', [
                'error' => $e->getMessage(),
                'class_id' => request()->input('class_id'),
                'subject_id' => request()->input('subject_id')
            ]);

            if (str_contains($e->getMessage(), 'No query results')) {
                return response()->json(['message' => 'Classe ou matière non trouvée'], 404);
            }

            return response()->json(['message' => 'Erreur lors de l\'affectation de la matière'], 500);
        }
    }

    /**
     * Retirer une matière d'une classe
     */
    public function detachSubject(AssignSubjectRequest $request)
    {
        try {
            $validated = $request->validated();
            $classId = $validated['class_id'];
            $subjectId = $validated['subject_id'];

            $class = ClassModel::findOrFail($classId);
            $subject = Subject::findOrFail($subjectId);

            // Vérifier si la relation existe
            if (!$class->subjects()->where('subject_id', $subjectId)->exists()) {
                return response()->json(['message' => 'Cette matière n\'est pas affectée à cette classe'], 404);
            }

            // Retirer la matière de la classe
            $class->subjects()->detach($subjectId);

            Log::info('ClassModelController: Subject detached from class successfully', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'class_name' => $class->name,
                'subject_name' => $subject->name
            ]);

            // Retourner la classe avec ses matières
            $class->load('subjects');
            return response()->json(new ClassModelResource($class));

        } catch (\Exception $e) {
            Log::error('ClassModelController: Failed to detach subject from class', [
                'error' => $e->getMessage(),
                'class_id' => request()->input('class_id'),
                'subject_id' => request()->input('subject_id')
            ]);

            if (str_contains($e->getMessage(), 'No query results')) {
                return response()->json(['message' => 'Classe ou matière non trouvée'], 404);
            }

            return response()->json(['message' => 'Erreur lors du retrait de la matière'], 500);
        }
    }
}
