<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Services\GradeFetchService;
use Illuminate\Http\JsonResponse;

/**
 * Class GradeClassController
 *
 * Gère les requêtes liées aux notes d'une classe.
 */
class GradeClassController extends Controller
{
    /**
     * Récupère les notes de tous les étudiants d'une classe, avec filtres optionnels.
     *
     * @param int $classId
     * @param int|null $teacherId
     * @param int|null $subjectId
     * @param int|null $assignementId
     * @param int|null $studentId
     * @return JsonResponse
     */
    public function getClassGrades(int $classId, int $teacherId = null, int $subjectId = null, int $assignementId = null, int $studentId = null): JsonResponse
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
