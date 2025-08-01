<?php

namespace App\Modules\Assignement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assignement\Exceptions\AssignmentException;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Requests\AssignementRequest;
use App\Modules\Assignement\Ressources\AssignementResource;
use App\Modules\Assignement\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignementController extends Controller
{
    private AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index()
    {
        $assignments = $this->assignmentService->getAllAssignments();
        return response()->json(AssignementResource::collection($assignments));
    }

    public function store(AssignementRequest $request)
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

    public function show(Assignement $assignement)
    {
        return response()->json(new AssignementResource($assignement));
    }

    public function update(AssignementRequest $request, Assignement $assignement)
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

    public function destroy(Assignement $assignement)
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

    public function getAssignmentsForTeacher($id)
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

    public function getByTermAndClass(Request $request)
    {
        try {
            $termId = $request->input('term_id');
            $classId = $request->input('class_id');
            
            if (!$termId || !$classId) {
                return response()->json([
                    'message' => 'Term ID and Class ID are required'
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
}
