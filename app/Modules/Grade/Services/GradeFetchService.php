<?php

namespace App\Modules\Grade\Services;

use App\Modules\Grade\Models\Grade;
use App\Modules\Term\Models\Term;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;

class GradeFetchService
{
    /**
     * Fetch grades for a student in a class, optionally filtered by teacher, subject, or assignment.
     */
    public static function fetchStudentGrades($classId, $studentId, $teacherId = null, $subjectId = null, $assignementId = null)
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentAcademicYear) {
            return ['error' => 'No current academic year found.'];
        }
        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            return ['error' => 'No current term found.'];
        }
        $termId = $currentTerm->id;
        // Treat 'null' string and null as no filter
        if ($studentId === 'null' || $studentId === null) {
            return ['error' => 'StudentId is required for fetchStudentGrades.'];
        }
        $studentSession = StudentSession::where('student_id', $studentId)
            ->where('class_model_id', $classId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();
        if (!$studentSession) {
            return ['error' => 'Student session not found for the given student, class, and academic year.'];
        }
        if ($assignementId !== null) {
            $assignment = Assignement::where('id', $assignementId)
                ->where('class_model_id', $classId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->with('subject')
                ->first();
            if (!$assignment) {
                return ['error' => 'Assignment not found.'];
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
     * Fetch grades for all students in a class, optionally filtered by teacher, subject, or assignment.
     */
    public static function fetchClassGrades($classId, $teacherId = null, $subjectId = null, $assignementId = null, $studentId = null)
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentAcademicYear) {
            return ['error' => 'No current academic year found.'];
        }
        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            return ['error' => 'No current term found.'];
        }
        $termId = $currentTerm->id;
        $studentSessionsQuery = StudentSession::where('class_model_id', $classId)
            ->where('academic_year_id', $currentAcademicYear->id);
        if ($studentId !== null && $studentId !== 'null') {
            $studentSessionsQuery->where('student_id', $studentId);
        }
        $studentSessions = $studentSessionsQuery->get();
        if ($studentSessions->isEmpty()) {
            return ['error' => 'No student sessions found for this class and academic year.'];
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
