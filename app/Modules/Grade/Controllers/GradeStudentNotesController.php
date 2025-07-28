<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeStudentNotesController extends Controller
{
    /**
     * Fetch notes (grades) for students in a class for a given subject.
     *
     * Example usage:
     * GET /api/v1/classes/{classId}/subjects/{subjectId}/student-notes
     *
     * Response:
     * [
     *   {
     *     "student": { ... },
     *     "student_session_id": 1,
     *     "grades": [
     *       { "type": "quiz", "mark": 15.5 },
     *       { "type": "exam", "mark": 12.0 }
     *     ]
     *   },
     *   ...
     * ]
     */
    public function getStudentNotes(Request $request, int $classId, int $subjectId): JsonResponse
    {
        // Get all student sessions for the class
        $class = \App\Modules\ClassModel\Models\ClassModel::with(['currentAcademicYearStudentSessions.student'])->findOrFail($classId);
        $students = $class->currentAcademicYearStudentSessions;

        // Get all assignments for the class and subject (term/academic year managed by backend)
        $assignments = Assignement::where('class_model_id', $classId)
            ->where('subject_id', $subjectId)
            ->get();
        $assignmentIds = $assignments->pluck('id')->all();
        $studentSessionIds = $students->pluck('id')->all();

        // Eager load all grades for these students and assignments
        $grades = Grade::whereIn('student_session_id', $studentSessionIds)
            ->whereIn('assignement_id', $assignmentIds)
            ->get();

        // Group grades by student session and type
        $gradesByStudent = [];
        foreach ($grades as $grade) {
            $gradesByStudent[$grade->student_session_id][$grade->type] = $grade->mark;
        }

        $result = [];
        foreach ($students as $studentSession) {
            $studentGrades = [];
            foreach (["quiz", "exam"] as $type) {
                $studentGrades[] = [
                    "type" => $type,
                    "mark" => $gradesByStudent[$studentSession->id][$type] ?? null
                ];
            }
            $result[] = [
                "student" => $studentSession->student,
                "student_session_id" => $studentSession->id,
                "grades" => $studentGrades
            ];
        }
        return response()->json($result);
    }
}
