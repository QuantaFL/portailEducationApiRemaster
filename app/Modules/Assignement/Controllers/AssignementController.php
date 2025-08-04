<?php

namespace App\Modules\Assignement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assignement\Exceptions\AssignmentException;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Requests\AssignementRequest;
use App\Modules\Assignement\Requests\ToggleStatusByTeacherRequest;
use App\Modules\Assignement\Ressources\AssignementResource;
use App\Modules\Assignement\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class AssignementController
 *
 * Gère les requêtes liées aux affectations.
 */
class AssignementController extends Controller
{
    private AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Affiche une liste des affectations.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $assignments = $this->assignmentService->getAllAssignments();
        return response()->json(AssignementResource::collection($assignments));
    }

    /**
     * Enregistre une nouvelle affectation.
     *
     * @param AssignementRequest $request
     * @return JsonResponse
     */
    public function store(AssignementRequest $request): JsonResponse
    {
        try {
            $assignment = $this->assignmentService->createAssignment($request->validated());
            return response()->json(new AssignementResource($assignment), 201);
        } catch (AssignmentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Affiche une affectation spécifique.
     *
     * @param Assignement $assignement
     * @return JsonResponse
     */
    public function show(Assignement $assignement): JsonResponse
    {
        return response()->json(new AssignementResource($assignement));
    }

    /**
     * Met à jour une affectation spécifique.
     *
     * @param AssignementRequest $request
     * @param Assignement $assignement
     * @return JsonResponse
     */
    public function update(AssignementRequest $request, Assignement $assignement): JsonResponse
    {
        try {
            $updatedAssignment = $this->assignmentService->updateAssignment($assignement, $request->validated());
            return response()->json(new AssignementResource($updatedAssignment));
        } catch (AssignmentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Supprime une affectation spécifique.
     *
     * @param Assignement $assignement
     * @return JsonResponse
     */
    public function destroy(Assignement $assignement): JsonResponse
    {
        try {
            $this->assignmentService->deleteAssignment($assignement);
            return response()->json();
        } catch (AssignmentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère les affectations d'un enseignant.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getAssignmentsForTeacher(int $id): JsonResponse
    {
        try {
            $assignments = $this->assignmentService->getAssignmentsForTeacher($id);
            return response()->json(AssignementResource::collection($assignments));
        } catch (AssignmentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère les affectations par semestre et par classe.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getByTermAndClass(Request $request): JsonResponse
    {
        try {
            $termId = $request->input('term_id');
            $classId = $request->input('class_id');

            if (!$termId || !$classId) {
                return response()->json([
                    'message' => 'L\'ID du semestre et l\'ID de la classe sont requis'
                ], 400);
            }

            $assignments = $this->assignmentService->getAssignmentsByTermAndClass($termId, $classId);
            return response()->json(AssignementResource::collection($assignments));
        } catch (AssignmentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Bascule le statut d'une affectation par l'enseignant.
     *
     * @param ToggleStatusByTeacherRequest $request
     * @return JsonResponse
     */
    public function toggleStatusByTeacher(ToggleStatusByTeacherRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $teacherId = $validated['teacher_id'];

            $assignment = $this->assignmentService->toggleAssignmentStatusByTeacherId($teacherId);
            return response()->json(new AssignementResource($assignment));

        } catch (AssignmentException $e) {
            Log::error('AssignementController: Échec du basculement du statut de l\'affectation par l\'enseignant', [
                'error' => $e->getMessage(),
                'teacher_id' => request()->input('teacher_id')
            ]);
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            Log::error('AssignementController: Échec du basculement du statut de l\'affectation par l\'enseignant', [
                'error' => $e->getMessage(),
                'teacher_id' => request()->input('teacher_id')
            ]);

            if (str_contains($e->getMessage(), "Aucun assignement trouvé")) {
                return response()->json(['message' => $e->getMessage()], 404);
            }

            return response()->json(['message' => 'Échec du basculement du statut de l\'affectation'], 500);
        }
    }
}
