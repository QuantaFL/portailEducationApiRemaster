<?php

namespace App\Modules\ClassModel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\ClassModel\Requests\ClassModelRequest;
use App\Modules\ClassModel\Requests\AssignSubjectRequest;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use App\Modules\Subject\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class ClassModelController
 *
 * Gère les requêtes liées aux classes.
 */
class ClassModelController extends Controller
{
    /**
     * Affiche une liste des classes.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $classes = ClassModel::with(['subjects', 'currentAcademicYearStudentSessions'])->get();
        return response()->json(ClassModelResource::collection($classes));
    }

    /**
     * Enregistre une nouvelle classe.
     *
     * @param ClassModelRequest $request
     * @return JsonResponse
     */
    public function store(ClassModelRequest $request): JsonResponse
    {
        return response()->json(new ClassModelResource(ClassModel::create($request->validated())));
    }

    /**
     * Affiche une classe spécifique.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function show(int $classId): JsonResponse
    {
        $classModel = ClassModel::with(['subjects', 'currentAcademicYearStudentSessions'])->findOrFail($classId);
        return response()->json(new ClassModelResource($classModel));
    }

    /**
     * Met à jour une classe spécifique.
     *
     * @param ClassModelRequest $request
     * @param ClassModel $classModel
     * @return JsonResponse
     */
    public function update(ClassModelRequest $request, ClassModel $classModel): JsonResponse
    {
        $classModel->update($request->validated());

        return response()->json(new ClassModelResource($classModel));
    }

    /**
     * Supprime une classe spécifique.
     *
     * @param ClassModel $classModel
     * @return JsonResponse
     */
    public function destroy(ClassModel $classModel): JsonResponse
    {
        $classModel->delete();

        return response()->json();
    }

    /**
     * Récupère les étudiants d'une classe.
     *
     * @param int $classId
     * @return JsonResponse
     */
    public function getStudentsByClass(int $classId): JsonResponse
    {
        $currentAcademicYear = \App\Modules\AcademicYear\Models\AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return response()->json(['message' => 'Aucune année académique en cours trouvée.'], 404);
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
     * Affecte une matière à une classe.
     *
     * @param AssignSubjectRequest $request
     * @return JsonResponse
     */
    public function assignSubject(AssignSubjectRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $classId = $validated['class_id'];
            $subjectId = $validated['subject_id'];

            Log::info('ClassModelController: Demande d\'affectation de matière reçue', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'user_agent' => request()->header('User-Agent'),
                'ip' => request()->ip()
            ]);

            $class = ClassModel::findOrFail($classId);
            $subject = Subject::findOrFail($subjectId);

            // Vérifier si la relation existe déjà
            if ($class->subjects()->where('subject_id', $subjectId)->exists()) {
                Log::warning('ClassModelController: Matière déjà affectée à la classe', [
                    'class_id' => $classId,
                    'subject_id' => $subjectId
                ]);
                return response()->json(['message' => 'Cette matière est déjà affectée à cette classe'], 409);
            }

            // Affecter la matière à la classe
            $class->subjects()->attach($subjectId);

            Log::info('ClassModelController: Matière affectée à la classe avec succès', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'class_name' => $class->name,
                'subject_name' => $subject->name
            ]);

            // Retourner la classe avec ses matières
            $class->load('subjects');
            return response()->json(new ClassModelResource($class));

        } catch (\Exception $e) {
            Log::error('ClassModelController: Échec de l\'affectation de la matière à la classe', [
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
     * Retire une matière d'une classe.
     *
     * @param AssignSubjectRequest $request
     * @return JsonResponse
     */
    public function detachSubject(AssignSubjectRequest $request): JsonResponse
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

            Log::info('ClassModelController: Matière retirée de la classe avec succès', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'class_name' => $class->name,
                'subject_name' => $subject->name
            ]);

            // Retourner la classe avec ses matières
            $class->load('subjects');
            return response()->json(new ClassModelResource($class));

        } catch (\Exception $e) {
            Log::error('ClassModelController: Échec du retrait de la matière de la classe', [
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
