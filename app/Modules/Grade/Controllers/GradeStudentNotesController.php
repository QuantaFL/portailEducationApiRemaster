<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Assignement\Models\Assignement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class GradeStudentNotesController
 *
 * Gère les requêtes liées aux notes des étudiants.
 */
class GradeStudentNotesController extends Controller
{
    /**
     * Récupère les notes des étudiants pour une affectation donnée.
     *
     * @param Request $request
     * @param int $classId
     * @param int $subjectId
     * @param int $assignmentId
     * @param int $teacherId
     * @return JsonResponse
     */
    public function getStudentNotes(Request $request, int $classId, int $subjectId, int $assignmentId, int $teacherId): JsonResponse
    {
        $class = \App\Modules\ClassModel\Models\ClassModel::with(['currentAcademicYearStudentSessions.student'])->findOrFail($classId);
        $students = $class->currentAcademicYearStudentSessions;
        $studentSessionIds = $students->pluck('id')->all();

        // Get the assignment and check teacher if needed
        $assignment = Assignement::where('id', $assignmentId)
            ->where('class_model_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $termId = \App\Modules\Term\Models\Term::getCurrentTerm()->id;
        $grades = Grade::whereIn('student_session_id', $studentSessionIds)
            ->where('assignement_id', $assignmentId)
            ->where('term_id', $termId)
            ->get();

        $gradesByStudent = [];
        foreach ($grades as $grade) {
            $gradesByStudent[$grade->student_session_id][$grade->type] = $grade;
        }

        $result = [];
        foreach ($students as $studentSession) {
            $studentGrades = [];
            foreach (["quiz", "exam"] as $type) {
                $grade = $gradesByStudent[$studentSession->id][$type] ?? null;
                $studentGrades[] = [
                    "type" => $type,
                    "mark" => $grade ? $grade->mark : null,
                    "assignement_id" => $assignmentId,
                    "subject_id" => $assignment->subject_id,
                    "status" => $grade ? $grade->status : 'pending',
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
