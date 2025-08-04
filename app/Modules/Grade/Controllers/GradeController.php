<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Term\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Modules\Grade\Requests\UpdateGradesRequest;
use Illuminate\Support\Facades\Log;

/**
 * Class GradeController
 *
 * Gère les requêtes liées aux notes.
 */
class GradeController extends Controller
{
    /**
     * Met à jour ou crée des notes en masse.
     *
     * @param UpdateGradesRequest $request
     * @return JsonResponse
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
                    Log::info("Appel de Grade::updateOrCreate", [
                        'where' => $where,
                        'update' => $update
                    ]);
                    $existing = Grade::where($where)->first();
                    if ($existing) {
                        Log::info("Note existante trouvée, mise à jour", [
                            'id' => $existing->id,
                            'old_mark' => $existing->mark,
                            'new_mark' => $gradeData['mark']
                        ]);
                    } else {
                        Log::info("Aucune note existante trouvée, création", $where);
                    }
                    Grade::updateOrCreate($where, $update);
                } catch (\Exception $e) {
                    Log::error("Erreur lors de la mise à jour/création de la note", [
                        'error' => $e->getMessage(),
                        'data' => $gradeData
                    ]);
                    $errors[$i] = $e->getMessage();
                }
            }
            if ($errors) {
                \DB::rollBack();
                Log::warning("Transaction de mise à jour des notes annulée", ['errors' => $errors]);
                return response()->json([
                    'message' => 'Certaines notes n\'ont pas pu être mises à jour.',
                    'errors' => $errors
                ], 422);
            }
            \DB::commit();
            Log::info("Transaction de mise à jour des notes validée avec succès");
            return response()->json(['message' => 'Notes mises à jour avec succès']);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("La transaction de mise à jour des notes a échoué", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Échec de la mise à jour des notes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soumet les notes d'un semestre pour une classe.
     *
     * @param Request $request
     * @param int $class_id
     * @return JsonResponse
     */
    public function submitTermNotes(Request $request, int $class_id): JsonResponse
    {
        $term = Term::getCurrentTerm();

        Grade::whereHas('assignement', function ($query) use ($class_id) {
            $query->where('class_model_id', $class_id);
        })
        ->where('term_id', $term->id)
        ->update(['status' => 'submitted']);

        return response()->json(['message' => 'Notes soumises avec succès pour le semestre.']);
    }

    /**
     * Récupère les notes d'un étudiant pour un semestre donné.
     *
     * @param int $classId
     * @param int|null $studentId
     * @param int|null $teacherId
     * @param int|null $subjectId
     * @param int|null $assignementId
     * @return JsonResponse
     */
    public function getStudentGradesInClassForTerm(int $classId, int $studentId = null, int $teacherId = null, int $subjectId = null, int $assignementId = null): JsonResponse
    {
        $termId = Term::getCurrentTerm()->id;
        $result = \App\Modules\Grade\Services\GradeFetchService::fetchClassGrades($classId, $teacherId, $subjectId, $assignementId, $studentId, $termId);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }
        return response()->json($result);
    }

    /**
     * Récupère une matrice de notes pour une classe.
     *
     * @param Request $request
     * @param int $classId
     * @return JsonResponse
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
