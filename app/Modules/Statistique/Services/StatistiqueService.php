<?php

namespace App\Modules\Statistique\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
use App\Modules\User\Models\UserModel;
use App\Modules\Statistique\Exceptions\StatistiqueException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatistiqueService
{
    public function getGeneralStatistics(): array
    {
        Log::info('StatistiqueService: Getting general statistics');

        return [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_subjects' => Subject::count(),
            'total_classes' => ClassModel::count(),
            'total_assignments' => Assignement::count(),
            'active_assignments' => Assignement::where('isActive', true)->count(),
            'inactive_assignments' => Assignement::where('isActive', false)->count(),
            'total_users' => UserModel::count(),
            'total_academic_years' => AcademicYear::count(),
            'total_terms' => Term::count(),
            'total_grades' => Grade::count(),
        ];
    }

    public function getStudentStatistics(): array
    {
        Log::info('StatistiqueService: Getting student statistics');

        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        
        return [
            'total_students' => Student::count(),
            'students_by_class' => $this->getStudentsByClass(),
            'students_with_grades' => $this->getStudentsWithGrades(),
            'average_grade_by_class' => $this->getAverageGradeByClass($currentAcademicYear?->id),
            'student_performance_distribution' => $this->getStudentPerformanceDistribution($currentAcademicYear?->id),
        ];
    }

    public function getTeacherStatistics(): array
    {
        Log::info('StatistiqueService: Getting teacher statistics');

        return [
            'total_teachers' => Teacher::count(),
            'teachers_by_subject_count' => $this->getTeachersBySubjectCount(),
            'teachers_assignment_distribution' => $this->getTeachersAssignmentDistribution(),
            'most_active_teachers' => $this->getMostActiveTeachers(),
        ];
    }

    public function getSubjectStatistics(): array
    {
        Log::info('StatistiqueService: Getting subject statistics');

        return [
            'total_subjects' => Subject::count(),
            'subjects_by_level' => Subject::select('level', DB::raw('count(*) as total'))
                ->groupBy('level')
                ->get()
                ->pluck('total', 'level'),
            'subjects_with_coefficient' => Subject::whereNotNull('coefficient')->count(),
            'subjects_without_coefficient' => Subject::whereNull('coefficient')->count(),
            'average_coefficient_by_level' => $this->getAverageCoefficientByLevel(),
            'subjects_assignment_count' => $this->getSubjectsAssignmentCount(),
        ];
    }

    public function getAssignmentStatistics(): array
    {
        Log::info('StatistiqueService: Getting assignment statistics');

        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();

        return [
            'total_assignments' => Assignement::count(),
            'active_assignments' => Assignement::where('isActive', true)->count(),
            'inactive_assignments' => Assignement::where('isActive', false)->count(),
            'assignments_by_day' => $this->getAssignmentsByDay(),
            'assignments_by_academic_year' => $this->getAssignmentsByAcademicYear(),
            'current_year_assignments' => $currentAcademicYear ? 
                Assignement::where('academic_year_id', $currentAcademicYear->id)->count() : 0,
            'assignments_with_coefficient' => Assignement::whereNotNull('coefficient')->count(),
        ];
    }

    public function getGradeStatistics(?int $academicYearId = null): array
    {
        Log::info('StatistiqueService: Getting grade statistics', ['academic_year_id' => $academicYearId]);

        $query = Grade::query();
        
        if ($academicYearId) {
            $query->whereHas('term.academicYear', function ($q) use ($academicYearId) {
                $q->where('id', $academicYearId);
            });
        }

        return [
            'total_grades' => $query->count(),
            'average_grade' => round($query->avg('note') ?? 0, 2),
            'highest_grade' => $query->max('note') ?? 0,
            'lowest_grade' => $query->min('note') ?? 0,
            'grade_distribution' => $this->getGradeDistribution($academicYearId),
            'grades_by_subject' => $this->getGradesBySubject($academicYearId),
        ];
    }

    private function getStudentsByClass(): array
    {
        return Student::select('class_models.name as class_name', DB::raw('count(*) as total'))
            ->join('student_sessions', 'students.id', '=', 'student_sessions.student_id')
            ->join('class_models', 'student_sessions.class_model_id', '=', 'class_models.id')
            ->groupBy('class_models.id', 'class_models.name')
            ->get()
            ->pluck('total', 'class_name')
            ->toArray();
    }

    private function getStudentsWithGrades(): int
    {
        return Student::whereHas('grades')->count();
    }

    private function getAverageGradeByClass(?int $academicYearId): array
    {
        $query = Grade::select('class_models.name as class_name', DB::raw('AVG(grades.note) as average_grade'))
            ->join('student_sessions', 'grades.student_session_id', '=', 'student_sessions.id')
            ->join('class_models', 'student_sessions.class_model_id', '=', 'class_models.id');

        if ($academicYearId) {
            $query->join('terms', 'grades.term_id', '=', 'terms.id')
                  ->where('terms.academic_year_id', $academicYearId);
        }

        return $query->groupBy('class_models.id', 'class_models.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->class_name => round($item->average_grade, 2)];
            })
            ->toArray();
    }

    private function getStudentPerformanceDistribution(?int $academicYearId): array
    {
        $query = Grade::query();
        
        if ($academicYearId) {
            $query->whereHas('term.academicYear', function ($q) use ($academicYearId) {
                $q->where('id', $academicYearId);
            });
        }

        $grades = $query->get();
        
        $distribution = [
            'excellent' => 0, // >= 16
            'bien' => 0,      // 14-15.99
            'assez_bien' => 0, // 12-13.99
            'passable' => 0,   // 10-11.99
            'insuffisant' => 0 // < 10
        ];

        foreach ($grades as $grade) {
            $note = $grade->note;
            if ($note >= 16) {
                $distribution['excellent']++;
            } elseif ($note >= 14) {
                $distribution['bien']++;
            } elseif ($note >= 12) {
                $distribution['assez_bien']++;
            } elseif ($note >= 10) {
                $distribution['passable']++;
            } else {
                $distribution['insuffisant']++;
            }
        }

        return $distribution;
    }

    private function getTeachersBySubjectCount(): array
    {
        return Teacher::select('subjects.name as subject_name', DB::raw('count(*) as total'))
            ->join('teacher_subjects', 'teachers.id', '=', 'teacher_subjects.teacher_id')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->groupBy('subjects.id', 'subjects.name')
            ->get()
            ->pluck('total', 'subject_name')
            ->toArray();
    }

    private function getTeachersAssignmentDistribution(): array
    {
        return Teacher::select('teachers.id', 'user_models.first_name', 'user_models.last_name', DB::raw('count(assignments.id) as assignment_count'))
            ->join('user_models', 'teachers.user_model_id', '=', 'user_models.id')
            ->leftJoin('assignments', 'teachers.id', '=', 'assignments.teacher_id')
            ->groupBy('teachers.id', 'user_models.first_name', 'user_models.last_name')
            ->orderByDesc('assignment_count')
            ->get()
            ->map(function ($teacher) {
                return [
                    'teacher' => $teacher->first_name . ' ' . $teacher->last_name,
                    'assignment_count' => $teacher->assignment_count
                ];
            })
            ->toArray();
    }

    private function getMostActiveTeachers(int $limit = 5): array
    {
        return Teacher::select('teachers.id', 'user_models.first_name', 'user_models.last_name', DB::raw('count(assignments.id) as assignment_count'))
            ->join('user_models', 'teachers.user_model_id', '=', 'user_models.id')
            ->join('assignments', 'teachers.id', '=', 'assignments.teacher_id')
            ->where('assignments.isActive', true)
            ->groupBy('teachers.id', 'user_models.first_name', 'user_models.last_name')
            ->orderByDesc('assignment_count')
            ->limit($limit)
            ->get()
            ->map(function ($teacher) {
                return [
                    'teacher' => $teacher->first_name . ' ' . $teacher->last_name,
                    'active_assignments' => $teacher->assignment_count
                ];
            })
            ->toArray();
    }

    private function getAverageCoefficientByLevel(): array
    {
        return Subject::select('level', DB::raw('AVG(coefficient) as avg_coefficient'))
            ->whereNotNull('coefficient')
            ->groupBy('level')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->level => round($item->avg_coefficient, 2)];
            })
            ->toArray();
    }

    private function getSubjectsAssignmentCount(): array
    {
        return Subject::select('subjects.name', DB::raw('count(assignments.id) as assignment_count'))
            ->leftJoin('assignments', 'subjects.id', '=', 'assignments.subject_id')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('assignment_count')
            ->get()
            ->pluck('assignment_count', 'name')
            ->toArray();
    }

    private function getAssignmentsByDay(): array
    {
        return Assignement::select('day_of_week', DB::raw('count(*) as total'))
            ->whereNotNull('day_of_week')
            ->groupBy('day_of_week')
            ->get()
            ->pluck('total', 'day_of_week')
            ->toArray();
    }

    private function getAssignmentsByAcademicYear(): array
    {
        return Assignement::select('academic_years.year_name', DB::raw('count(assignments.id) as total'))
            ->join('academic_years', 'assignments.academic_year_id', '=', 'academic_years.id')
            ->groupBy('academic_years.id', 'academic_years.year_name')
            ->get()
            ->pluck('total', 'year_name')
            ->toArray();
    }

    private function getGradeDistribution(?int $academicYearId): array
    {
        $query = Grade::select(
            DB::raw('
                CASE 
                    WHEN note >= 16 THEN "16-20"
                    WHEN note >= 14 THEN "14-16"
                    WHEN note >= 12 THEN "12-14"
                    WHEN note >= 10 THEN "10-12"
                    WHEN note >= 8 THEN "8-10"
                    WHEN note >= 6 THEN "6-8"
                    WHEN note >= 4 THEN "4-6"
                    WHEN note >= 2 THEN "2-4"
                    ELSE "0-2"
                END as grade_range
            '),
            DB::raw('count(*) as total')
        );

        if ($academicYearId) {
            $query->whereHas('term.academicYear', function ($q) use ($academicYearId) {
                $q->where('id', $academicYearId);
            });
        }

        return $query->groupBy('grade_range')
            ->get()
            ->pluck('total', 'grade_range')
            ->toArray();
    }

    private function getGradesBySubject(?int $academicYearId): array
    {
        $query = Grade::select('subjects.name as subject_name', DB::raw('AVG(grades.note) as average_grade'), DB::raw('count(grades.id) as grade_count'))
            ->join('assignments', 'grades.assignment_id', '=', 'assignments.id')
            ->join('subjects', 'assignments.subject_id', '=', 'subjects.id');

        if ($academicYearId) {
            $query->join('terms', 'grades.term_id', '=', 'terms.id')
                  ->where('terms.academic_year_id', $academicYearId);
        }

        return $query->groupBy('subjects.id', 'subjects.name')
            ->get()
            ->map(function ($item) {
                return [
                    'subject' => $item->subject_name,
                    'average_grade' => round($item->average_grade, 2),
                    'grade_count' => $item->grade_count
                ];
            })
            ->toArray();
    }

    public function getDashboardStatistics(): array
    {
        Log::info('StatistiqueService: Getting dashboard statistics');

        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();

        return [
            'general' => $this->getGeneralStatistics(),
            'current_academic_year' => $currentAcademicYear ? [
                'id' => $currentAcademicYear->id,
                'name' => $currentAcademicYear->year_name,
                'status' => $currentAcademicYear->status,
                'students_count' => $this->getCurrentYearStudentsCount($currentAcademicYear->id),
                'teachers_count' => $this->getCurrentYearTeachersCount($currentAcademicYear->id),
                'assignments_count' => Assignement::where('academic_year_id', $currentAcademicYear->id)->count(),
                'average_grade' => $this->getCurrentYearAverageGrade($currentAcademicYear->id),
            ] : null,
            'quick_stats' => [
                'most_popular_subject' => $this->getMostPopularSubject(),
                'best_performing_class' => $this->getBestPerformingClass($currentAcademicYear?->id),
                'teacher_with_most_assignments' => $this->getTeacherWithMostAssignments(),
            ]
        ];
    }

    private function getCurrentYearStudentsCount(int $academicYearId): int
    {
        return Student::whereHas('studentSessions.classModel.terms.academicYear', function ($query) use ($academicYearId) {
            $query->where('id', $academicYearId);
        })->count();
    }

    private function getCurrentYearTeachersCount(int $academicYearId): int
    {
        return Teacher::whereHas('assignments.academicYear', function ($query) use ($academicYearId) {
            $query->where('id', $academicYearId);
        })->distinct()->count();
    }

    private function getCurrentYearAverageGrade(int $academicYearId): float
    {
        $average = Grade::whereHas('term.academicYear', function ($query) use ($academicYearId) {
            $query->where('id', $academicYearId);
        })->avg('note');

        return round($average ?? 0, 2);
    }

    private function getMostPopularSubject(): ?array
    {
        $subject = Subject::select('subjects.name', DB::raw('count(assignments.id) as assignment_count'))
            ->leftJoin('assignments', 'subjects.id', '=', 'assignments.subject_id')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('assignment_count')
            ->first();

        return $subject ? [
            'name' => $subject->name,
            'assignment_count' => $subject->assignment_count
        ] : null;
    }

    private function getBestPerformingClass(?int $academicYearId): ?array
    {
        if (!$academicYearId) {
            return null;
        }

        $class = Grade::select('class_models.name as class_name', DB::raw('AVG(grades.note) as average_grade'))
            ->join('student_sessions', 'grades.student_session_id', '=', 'student_sessions.id')
            ->join('class_models', 'student_sessions.class_model_id', '=', 'class_models.id')
            ->join('terms', 'grades.term_id', '=', 'terms.id')
            ->where('terms.academic_year_id', $academicYearId)
            ->groupBy('class_models.id', 'class_models.name')
            ->orderByDesc('average_grade')
            ->first();

        return $class ? [
            'name' => $class->class_name,
            'average_grade' => round($class->average_grade, 2)
        ] : null;
    }

    private function getTeacherWithMostAssignments(): ?array
    {
        $teacher = Teacher::select('user_models.first_name', 'user_models.last_name', DB::raw('count(assignments.id) as assignment_count'))
            ->join('user_models', 'teachers.user_model_id', '=', 'user_models.id')
            ->join('assignments', 'teachers.id', '=', 'assignments.teacher_id')
            ->where('assignments.isActive', true)
            ->groupBy('teachers.id', 'user_models.first_name', 'user_models.last_name')
            ->orderByDesc('assignment_count')
            ->first();

        return $teacher ? [
            'name' => $teacher->first_name . ' ' . $teacher->last_name,
            'assignment_count' => $teacher->assignment_count
        ] : null;
    }

    public function getAssignmentByNumber(string $assignmentNumber): Assignement
    {
        $assignment = Assignement::where('assignment_number', $assignmentNumber)->first();
        
        if (!$assignment) {
            throw StatistiqueException::assignmentNotFound();
        }
        
        return $assignment;
    }

    public function toggleAssignmentStatusByNumber(string $assignmentNumber, bool $isActive): Assignement
    {
        $assignment = Assignement::where('assignment_number', $assignmentNumber)->first();
        
        if (!$assignment) {
            throw StatistiqueException::assignmentNotFound();
        }

        Log::info('StatistiqueService: Toggling assignment status by number', [
            'assignment_number' => $assignmentNumber,
            'new_status' => $isActive ? 'active' : 'inactive',
            'assignment_id' => $assignment->id
        ]);

        $assignment->update(['isActive' => $isActive]);
        
        return $assignment->fresh();
    }
}