<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Term\Models\Term;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use App\Modules\Grade\Requests\UpdateGradesRequest;
// add log use
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    public function getGradesByTerm(GetGradesByTermRequest $request)
    {
        $term = Term::getCurrentTerm();

        if ($term->isEnded()) {
            return response()->json(['message' => 'Term has ended'], 400);
        }

        $grades = Grade::with(['studentSession.student.userModel', 'assignement.subject'])
            ->where('term_id', $term->id)
            ->whereHas('assignement', function ($query) use ($request) {
                $query->where('class_model_id', $request->class_model_id)
                    ->where('subject_id', $request->subject_id);
            })
            ->get();

        return response()->json($grades);
    }

    /**
     * Batch update or create grades for students and assignments.
     * Accepts partial payloads and validates each grade.
     * Uses a transaction for atomicity.
     *
     * @param UpdateGradesRequest $request
     * @return JsonResponse
     *
     * Example usage:
     * POST /api/v1/grades
     * {
     *   "grades": [
     *     { "student_session_id": 1, "assignement_id": 2, "term_id": 3, "type": "exam", "mark": 15.5 },
     *     ...
     *   ]
     * }
     */
    public function updateGrades(UpdateGradesRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $grades = $validated['grades'];
        $errors = [];

        \DB::beginTransaction();
        try {
            foreach ($grades as $i => $gradeData) {
                try {
                    $where = [
                        'student_session_id' => $gradeData['student_session_id'],
                        'term_id' => Term::getCurrentTerm()->id,
                        'assignement_id' => $gradeData['assignement_id'],
                        'type' => $gradeData['type'],
                    ];
                    $update = ['mark' => $gradeData['mark']];
                    Log::info("Grade updateOrCreate called", [
                        'where' => $where,
                        'update' => $update
                    ]);
                    $existing = Grade::where($where)->first();
                    if ($existing) {
                        Log::info("Existing grade found, will update", [
                            'id' => $existing->id,
                            'old_mark' => $existing->mark,
                            'new_mark' => $gradeData['mark']
                        ]);
                    } else {
                        Log::info("No existing grade found, will create new", $where);
                    }
                    Grade::updateOrCreate($where, $update);
                } catch (\Exception $e) {
                    Log::error("Error updating/creating grade", [
                        'error' => $e->getMessage(),
                        'data' => $gradeData
                    ]);
                    $errors[$i] = $e->getMessage();
                }
            }
            if ($errors) {
                \DB::rollBack();
                Log::warning("Grade update transaction rolled back", ['errors' => $errors]);
                return response()->json([
                    'message' => 'Some grades could not be updated.',
                    'errors' => $errors
                ], 422);
            }
            \DB::commit();
            Log::info("Grade update transaction committed successfully");
            return response()->json(['message' => 'Grades updated successfully']);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("Grade update transaction failed", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to update grades.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitTermNotes(Request $request, $class_id): JsonResponse
    {
        $term = Term::getCurrentTerm();

        Grade::whereHas('assignement', function ($query) use ($class_id) {
            $query->where('class_model_id', $class_id);
        })
        ->where('term_id', $term->id)
        ->update(['status' => 'submitted']); // Assuming a 'status' column exists in the grades table

        return response()->json(['message' => 'Notes submitted successfully for the term.']);
    }

    /**
     * Returns all grades for each assignment for a student in a class and term.
     *
     * For each assignment in the class and term, this endpoint returns ALL grades (e.g., exam, quiz, etc.)
     * for the given student. If there are multiple grades for an assignment (e.g., both an exam and a quiz),
     * each will be included as a separate object in the response array.
     *
     * Each grade object contains:
     *   - id: Grade ID (null if no grade exists for this assignment)
     *   - mark: The grade mark (null if no grade exists)
     *   - type: The type of grade (e.g., 'exam', 'quiz', etc.)
     *   - assignement: The full assignment object (with subject info)
     *   - student_session: The student session object
     *   - term: The term object
     *   - academic_year: The academic year object
     *   - status: The grade status (or 'not_submitted' if no grade exists)
     *
     * If no grade exists for an assignment, a default/null entry is included for that assignment.
     *
     * Example response:
     * [
     *   {
     *     "id": 123,
     *     "mark": 15.5,
     *     "type": "exam",
     *     "assignement": { ... },
     *     "student_session": { ... },
     *     "term": { ... },
     *     "academic_year": { ... },
     *     "status": "submitted"
     *   },
     *   {
     *     "id": 124,
     *     "mark": 12.0,
     *     "type": "quiz",
     *     ...
     *   },
     *   ...
     * ]
     */
public function getStudentGradesInClassForTerm($classId, $studentId=null, $teacherId = null, $subjectId = null, $assignementId = null)
    {
        $termId = Term::getCurrentTerm()->id;
        $result = \App\Modules\Grade\Services\GradeFetchService::fetchClassGrades($classId, $teacherId, $subjectId, $assignementId, $studentId, $termId);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }
        return response()->json($result);
    }

    /**
     * Get a matrix of all students in a class and all assignments for the current term/subject,
     * with grades (or null) for each student-assignment pair.
     *
     * @param Request $request
     * @param int $classId
     * @return JsonResponse
     *
     * Example usage:
     * GET /api/v1/classes/1/grades-matrix?term_id=2&subject_id=3
     *
     * Response:
     * [
     *   {
     *     "student": { ... },
     *     "assignments": [
     *       { "assignment": { ... }, "grade": { ... } | null },
     *       ...
     *     ]
     *   },
     *   ...
     * ]
     */
    public function getGradesMatrix(Request $request, int $classId): JsonResponse
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
        ]);

        $termId = Term::getCurrentTerm()->id;
        $subjectId = $request->input('subject_id');

        $class = \App\Modules\ClassModel\Models\ClassModel::with(['currentAcademicYearStudentSessions.student'])->findOrFail($classId);
        $students = $class->currentAcademicYearStudentSessions;

        $assignments = \App\Modules\Assignement\Models\Assignement::where('class_model_id', $classId)
            ->where('term_id', $termId)
            ->where('subject_id', $subjectId)
            ->get();

        $studentSessionIds = $students->pluck('id')->all();
        $assignmentIds = $assignments->pluck('id')->all();

        // Eager load all grades for this class/term/subject
        $grades = \App\Modules\Grade\Models\Grade::whereIn('student_session_id', $studentSessionIds)
            ->whereIn('assignement_id', $assignmentIds)
            ->where('term_id', $termId)
            ->get();

        $gradesByStudentAndAssignment = [];
        foreach ($grades as $grade) {
            $gradesByStudentAndAssignment[$grade->student_session_id][$grade->assignement_id] = $grade;
        }

        $result = [];
        foreach ($students as $studentSession) {
            $studentAssignments = [];
            foreach ($assignments as $assignment) {
                $grade = $gradesByStudentAndAssignment[$studentSession->id][$assignment->id] ?? null;
                $studentAssignments[] = [
                    'assignment' => $assignment,
                    'grade' => $grade,
                ];
            }
            $result[] = [
                'student' => $studentSession->student,
                'student_session_id' => $studentSession->id,
                'assignments' => $studentAssignments,
            ];
        }
        return response()->json($result);
    }
}
