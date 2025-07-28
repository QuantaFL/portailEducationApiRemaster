<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Services\GradeFetchService;
use Illuminate\Http\JsonResponse;

class GradeClassController extends Controller
{
    /**
     * Fetch grades for all students in a class, optionally filtered by teacher, subject, or assignment.
     * Endpoint: GET /api/v1/grades/class/{classId}/teacher/{teacherId}/subject/{subjectId}/assignement/{assignementId?}
     */
    public function getClassGrades($classId, $teacherId = null, $subjectId = null, $assignementId = null, $studentId = null): JsonResponse
    {
        if ($studentId !== null && $studentId !== 'null') {
            $result = GradeFetchService::fetchStudentGrades($classId, $studentId, $teacherId, $subjectId, $assignementId);
            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 404);
            }
            return response()->json($result);
        } else {
            $result = GradeFetchService::fetchClassGrades($classId, $teacherId, $subjectId, $assignementId);
            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 404);
            }
            return response()->json($result);
        }
    }
}
