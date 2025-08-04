<?php

namespace App\Modules\Statistique\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Statistique\Resources\StatistiqueResource;
use App\Modules\Statistique\Services\StatistiqueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatistiqueController extends Controller
{
    private StatistiqueService $statistiqueService;

    public function __construct(StatistiqueService $statistiqueService)
    {
        $this->statistiqueService = $statistiqueService;
    }

    public function dashboard(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting dashboard statistics');
            
            $statistics = $this->statistiqueService->getDashboardStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => new StatistiqueResource($statistics)
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting dashboard statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function general(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting general statistics');
            
            $statistics = $this->statistiqueService->getGeneralStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'General statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting general statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve general statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function students(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting student statistics');
            
            $statistics = $this->statistiqueService->getStudentStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Student statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting student statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function teachers(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting teacher statistics');
            
            $statistics = $this->statistiqueService->getTeacherStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Teacher statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting teacher statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teacher statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function subjects(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting subject statistics');
            
            $statistics = $this->statistiqueService->getSubjectStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Subject statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting subject statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subject statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignments(): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting assignment statistics');
            
            $statistics = $this->statistiqueService->getAssignmentStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Assignment statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting assignment statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignment statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function grades(Request $request): JsonResponse
    {
        try {
            Log::info('StatistiqueController: Getting grade statistics', [
                'academic_year_id' => $request->get('academic_year_id')
            ]);
            
            $academicYearId = $request->get('academic_year_id');
            $statistics = $this->statistiqueService->getGradeStatistics($academicYearId);
            
            return response()->json([
                'success' => true,
                'message' => 'Grade statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting grade statistics', [
                'error' => $e->getMessage(),
                'academic_year_id' => $request->get('academic_year_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve grade statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignmentsByNumber(Request $request): JsonResponse
    {
        try {
            $assignmentNumber = $request->get('assignment_number');
            
            if (!$assignmentNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment number is required'
                ], 400);
            }

            Log::info('StatistiqueController: Getting assignment by number', [
                'assignment_number' => $assignmentNumber
            ]);
            
            $assignment = $this->statistiqueService->getAssignmentByNumber($assignmentNumber);
            
            return response()->json([
                'success' => true,
                'message' => 'Assignment retrieved successfully',
                'data' => [
                    'assignment_number' => $assignment->assignment_number,
                    'isActive' => $assignment->isActive,
                    'teacher' => $assignment->teacher->userModel->first_name . ' ' . $assignment->teacher->userModel->last_name,
                    'subject' => $assignment->subject->name,
                    'class' => $assignment->classModel->name,
                    'academic_year' => $assignment->academicYear->year_name,
                    'day_of_week' => $assignment->day_of_week,
                    'start_time' => $assignment->start_time,
                    'end_time' => $assignment->end_time,
                    'coefficient' => $assignment->coefficient,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error getting assignment by number', [
                'error' => $e->getMessage(),
                'assignment_number' => $request->get('assignment_number')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleAssignmentStatus(Request $request): JsonResponse
    {
        try {
            $assignmentNumber = $request->get('assignment_number');
            $isActive = $request->boolean('isActive');
            
            if (!$assignmentNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment number is required'
                ], 400);
            }

            Log::info('StatistiqueController: Toggling assignment status', [
                'assignment_number' => $assignmentNumber,
                'new_status' => $isActive
            ]);
            
            $assignment = $this->statistiqueService->toggleAssignmentStatusByNumber($assignmentNumber, $isActive);
            
            return response()->json([
                'success' => true,
                'message' => 'Assignment status updated successfully',
                'data' => [
                    'assignment_number' => $assignment->assignment_number,
                    'isActive' => $assignment->isActive,
                    'status' => $assignment->isActive ? 'active' : 'inactive'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('StatistiqueController: Error toggling assignment status', [
                'error' => $e->getMessage(),
                'assignment_number' => $request->get('assignment_number'),
                'isActive' => $request->get('isActive')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}