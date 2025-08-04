<?php

namespace App\Modules\Grade\Services;

use App\Modules\Grade\Models\Grade;
use App\Modules\Term\Models\Term;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;

/**
 * Class GradeFetchService
 *
 * Service pour la récupération des notes.
 */
class GradeFetchService
{
    /**
     * Récupère les notes d'un étudiant.
     *
     * @param int $classId
     * @param int $studentId
     * @param int|null $teacherId
     * @param int|null $subjectId
     * @param int|null $assignementId
     * @return array
     */
    public static function fetchStudentGrades(int $classId, int $studentId, int $teacherId = null, int $subjectId = null, int $assignementId = null): array
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentAcademicYear) {
            return ['error' => 'Aucune année académique en cours trouvée.'];
        }
        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            return ['error' => 'Aucun semestre en cours trouvé.'];
        }
        $termId = $currentTerm->id;
        // Treat 'null' string and null as no filter
        if ($studentId === 'null' || $studentId === null) {
            return ['error' => 'L\'ID de l\'étudiant est requis pour fetchStudentGrades.'];
        }
        $studentSession = StudentSession::where('student_id', $studentId)
            ->where('class_model_id', $classId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();
        if (!$studentSession) {
            return ['error' => 'Session d\'étudiant non trouvée pour l\'étudiant, la classe et l\'année académique donnés.'];
        }
        if ($assignementId !== null) {
            $assignment = Assignement::where('id', $assignementId)
                ->where('class_model_id', $classId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->with('subject')
                ->first();
            if (!$assignment) {
                return ['error' => 'Affectation non trouvée.'];
            }
            $grades = Grade::where('assignement_id', $assignment->id)
                ->where('student_session_id', $studentSession->id)
                ->where('term_id', $termId)
                ->get();
            $result = [];
            if ($grades->isEmpty()) {
                $result[] = [
                    'id' => null,
                    'mark' => null,
                    'type' => null,
                    'assignement' => $assignment,
                    'student_session' => $studentSession,
                    'term' => $currentTerm,
                    'academic_year' => $currentAcademicYear,
                    'status' => 'not_submitted',
                ];
            } else {
                foreach ($grades as $grade) {
                    $result[] = [
                        'id' => $grade->id,
                        'mark' => $grade->mark,
                        'type' => $grade->type,
                        'assignement' => $assignment,
                        'student_session' => $studentSession,
                        'term' => $currentTerm,
                        'academic_year' => $currentAcademicYear,
                        'status' => $grade->status ?? 'not_submitted',
                    ];
                }
            }
            return $result;
        } else {
            $assignmentsQuery = Assignement::where('class_model_id', $classId)
                ->where('academic_year_id', $currentAcademicYear->id);
            if ($teacherId !== null) {
                $assignmentsQuery->where('teacher_id', $teacherId);
            }
            if ($subjectId !== null) {
                $assignmentsQuery->where('subject_id', $subjectId);
            }
            $assignments = $assignmentsQuery->with('subject')->get();
            $grades = [];
            foreach ($assignments as $assignment) {
                $assignmentGrades = Grade::where('assignement_id', $assignment->id)
                    ->where('student_session_id', $studentSession->id)
                    ->where('term_id', $termId)
                    ->get();
                if ($assignmentGrades->isEmpty()) {
                    $grades[] = [
                        'id' => null,
                        'mark' => null,
                        'type' => null,
                        'assignement' => $assignment,
                        'student_session' => $studentSession,
                        'term' => $currentTerm,
                        'academic_year' => $currentAcademicYear,
                        'status' => 'not_submitted',
                    ];
                } else {
                    foreach ($assignmentGrades as $grade) {
                        $grades[] = [
                            'id' => $grade->id,
                            'mark' => $grade->mark,
                            'type' => $grade->type,
                            'assignement' => $assignment,
                            'student_session' => $studentSession,
                            'term' => $currentTerm,
                            'academic_year' => $currentAcademicYear,
                            'status' => $grade->status ?? 'not_submitted',
                        ];
                    }
                }
            }
            return $grades;
        }
    }

    /**
     * Récupère les notes d'une classe.
     *
     * @param int $classId
     * @param int|null $teacherId
     * @param int|null $subjectId
     * @param int|null $assignementId
     * @param int|null $studentId
     * @return array
     */
    public static function fetchClassGrades(int $classId, int $teacherId = null, int $subjectId = null, int $assignementId = null, int $studentId = null): array
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentAcademicYear) {
            return ['error' => 'Aucune année académique en cours trouvée.'];
        }
        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            return ['error' => 'Aucun semestre en cours trouvé.'];
        }
        $termId = $currentTerm->id;
        $studentSessionsQuery = StudentSession::where('class_model_id', $classId)
            ->where('academic_year_id', $currentAcademicYear->id);
        if ($studentId !== null && $studentId !== 'null') {
            $studentSessionsQuery->where('student_id', $studentId);
        }
        $studentSessions = $studentSessionsQuery->get();
        if ($studentSessions->isEmpty()) {
            return ['error' => 'Aucune session d\'étudiant trouvée pour cette classe et cette année académique.'];
        }
        $results = [];
        foreach ($studentSessions as $studentSession) {
            $studentGrades = self::fetchStudentGrades($classId, $studentSession->student_id, $teacherId, $subjectId, $assignementId);
            $results[] = [
                'student' => $studentSession->student,
                'student_session' => $studentSession,
                'grades' => $studentGrades
            ];
        }
        return $results;
    }
}
