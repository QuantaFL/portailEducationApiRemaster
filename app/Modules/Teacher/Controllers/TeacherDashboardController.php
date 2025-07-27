<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    /**
     * Get best and worst performing students across multiple classes and subjects.
     *
     * Expects payload:
     * {
     *   "classSubjects": [
     *     { "classId": 1, "subjectId": 2 },
     *     { "classId": 3, "subjectId": 4 }
     *   ]
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMultiClassPerformanceSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classSubjects' => 'required|array|min:1',
            'classSubjects.*.classId' => 'required|integer|exists:class_models,id',
            'classSubjects.*.subjectId' => 'required|integer|exists:subjects,id',
        ]);

        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            return response()->json(['message' => 'No current term found.'], 404);
        }

        $teacher = Teacher::where('teachers.id', 1)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found.'], 403);
        }

        $allPerformances = [];

        foreach ($validated['classSubjects'] as $pair) {
            $class = ClassModel::find($pair['classId']);
            if (!$class) {
                continue;
            }
            $studentsInClass = $class->currentAcademicYearStudentSessions()->pluck('student_id')->toArray();
            $students = Student::whereIn('id', $studentsInClass)->get();

            foreach ($students as $student) {
                $studentSession = $student->latestStudentSession;
                if (!$studentSession) {
                    continue;
                }
                $grades = Grade::where('student_session_id', $studentSession->id)
                    ->where('term_id', $currentTerm->id)
                    ->whereHas('assignement', function ($query) use ($pair, $teacher) {
                        $query->where('subject_id', $pair['subjectId'])
                            ->where('teacher_id', $teacher->id);
                    })
                    ->get();

                if ($grades->isNotEmpty()) {
                    $averageGrade = $grades->avg('mark');
                    $allPerformances[] = [
                        'studentId' => $student->id,
                        'firstName' => $student->userModel->first_name,
                        'lastName' => $student->userModel->last_name,
                        'classId' => $pair['classId'],
                        'subjectId' => $pair['subjectId'],
                        'averageGrade' => round($averageGrade, 2),
                    ];
                }
            }
        }

        if (empty($allPerformances)) {
            return response()->json(['message' => 'No grades found for the provided classes, subjects, and term.'], 404);
        }

        $best = collect($allPerformances)->sortByDesc('averageGrade')->first();
        $worst = collect($allPerformances)->sortBy('averageGrade')->first();

        return response()->json([
            'bestPerformingStudent' => $best,
            'worstPerformingStudent' => $worst,
        ]);
    }
}
