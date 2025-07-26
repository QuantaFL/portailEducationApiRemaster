<?php

namespace App\Modules\ReportCard\Services;

use App\Jobs\GenerateReportCardPdfJob;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Grade\Models\Grade;
use App\Modules\ReportCard\Models\ReportCard;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Term\Models\Term;
use Illuminate\Support\Facades\DB;

class ReportCardGeneratorService
{
    public function generateReportCardsForClassAndTerm(int $classModelId, int $termId): array
    {
        $term = Term::find($termId);
        if (!$term) {
            throw new \Exception("Term not found.");
        }

        $classModel = ClassModel::find($classModelId);
        if (!$classModel) {
            throw new \Exception("Class not found.");
        }

        $currentAcademicYear = AcademicYear::where('status', 'en_cours')->first();

        if (!$currentAcademicYear) {
            throw new \Exception("No active academic year found.");
        }

        $studentSessions = StudentSession::where('class_model_id', $classModelId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->with(['student.userModel', 'student.parentModel.userModel'])
            ->get();

        $generatedReportCards = [];

        foreach ($studentSessions as $studentSession) {
            $reportCardData = $this->calculateStudentGrades($studentSession, $term);

            $reportCard = ReportCard::updateOrCreate(
                [
                    'student_session_id' => $studentSession->id,
                    'term_id' => $term->id,
                ],
                [
                    'average_grade' => $reportCardData['overall_average'],
                    'honors' => $reportCardData['overall_appreciation'],
                    'path' => '', // Le chemin du PDF sera mis à jour après la génération
                    'rank' => '', // Le rang sera calculé après la génération de tous les bulletins
                ]
            );

            $generatedReportCards[] = [
                'report_card_model' => $reportCard,
                'detailed_data' => $reportCardData,
            ];

            GenerateReportCardPdfJob::dispatch($reportCard->id, $reportCardData);
        }

        return $generatedReportCards;
    }

    private function calculateStudentGrades(StudentSession $studentSession, Term $term): array
    {
        $student = $studentSession->student;
        $user = $student->userModel;
        $parent = $student->parentModel->userModel;

        $subjects = DB::table('subjects')
            ->join('assignments', 'subjects.id', '=', 'assignments.subject_id')
            ->where('assignments.class_model_id', $studentSession->class_model_id)
            ->select('subjects.id', 'subjects.name', 'subjects.coefficient')
            ->distinct()
            ->get();

        $subjectResults = [];
        $totalWeightedMarks = 0;
        $totalCoefficients = 0;

        foreach ($subjects as $subject) {
            $grades = Grade::where('student_session_id', $studentSession->id)
                ->where('term_id', $term->id)
                ->whereHas('assignement', function ($query) use ($subject) {
                    $query->where('subject_id', $subject->id);
                })
                ->get();

            $examMark = $grades->where('type', 'exam')->avg('mark');
            $quizMark = $grades->where('type', 'quiz')->avg('mark');

            $subjectAverage = null;
            if ($examMark !== null && $quizMark !== null) {
                $subjectAverage = ($examMark + $quizMark) / 2;
            } elseif ($examMark !== null) {
                $subjectAverage = $examMark;
            } elseif ($quizMark !== null) {
                $subjectAverage = $quizMark;
            }

            $subjectResults[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'coefficient' => $subject->coefficient,
                'exam_mark' => $examMark,
                'quiz_mark' => $quizMark,
                'average' => $subjectAverage,
                'appreciation' => $this->getAppreciation($subjectAverage),
            ];

            if ($subjectAverage !== null) {
                $totalWeightedMarks += ($subjectAverage * $subject->coefficient);
                $totalCoefficients += $subject->coefficient;
            }
        }

        $overallAverage = $totalCoefficients > 0 ? $totalWeightedMarks / $totalCoefficients : 0;

        return [
            'student_info' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'matricule' => $student->matricule,
                'class_name' => $studentSession->classModel->name,
            ],
            'parent_info' => [
                'first_name' => $parent->first_name,
                'last_name' => $parent->last_name,
            ],
            'term_info' => [
                'name' => $term->name,
                'academic_year_label' => $term->academicYear->label,
            ],
            'subject_results' => $subjectResults,
            'overall_average' => round($overallAverage, 2),
            'overall_appreciation' => $this->getAppreciation($overallAverage),
        ];
    }

    private function getAppreciation(?float $average): string
    {
        if ($average === null) {
            return 'N/A';
        }
        if ($average >= 18) return 'Excellent';
        if ($average >= 16) return 'Très bien';
        if ($average >= 14) return 'Bien';
        if ($average >= 12) return 'Assez bien';
        if ($average >= 10) return 'Passable';
        return 'Insuffisant';
    }
}
