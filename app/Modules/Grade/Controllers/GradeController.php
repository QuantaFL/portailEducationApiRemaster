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

class GradeController extends Controller
{
    public function getGradesByTerm(\App\Modules\Grade\Requests\GetGradesByTermRequest $request)
    {
        $term = Term::find($request->term_id);

        if ($term->isEnded()) {
            return response()->json(['message' => 'Term has ended'], 400);
        }

        $grades = Grade::with(['studentSession.student.userModel', 'assignement.subject'])
            ->where('term_id', $request->term_id)
            ->whereHas('assignement', function ($query) use ($request) {
                $query->where('class_model_id', $request->class_model_id)
                    ->where('subject_id', $request->subject_id);
            })
            ->get();

        return response()->json($grades);
    }

    public function updateGrades(\App\Modules\Grade\Requests\UpdateGradesRequest $request) : JsonResponse
    {
        foreach ($request->grades as $gradeData) {
            if (isset($gradeData['id'])) {
                Grade::where('id', $gradeData['id'])->update([
                    'mark' => $gradeData['mark'],
                    'type' => $gradeData['type'],
                    'assignement_id' => $gradeData['assignement_id'],
                    'student_session_id' => $gradeData['student_session_id'],
                    'term_id' => $gradeData['term_id'],
                ]);
            } else {
                Grade::updateOrCreate(
                    [
                        'student_session_id' => $gradeData['student_session_id'],
                        'term_id' => $gradeData['term_id'],
                        'assignement_id' => $gradeData['assignement_id'],
                        'type' => $gradeData['type'],
                    ],
                    ['mark' => $gradeData['mark']]
                );
            }
        }

        return response()->json(['message' => 'Grades updated successfully']);
    }

    public function submitTermNotes(Request $request, $class_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'term_id' => 'required|exists:terms,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the status of grades for the given class and term to 'submitted'
        Grade::whereHas('assignement', function ($query) use ($class_id) {
            $query->where('class_model_id', $class_id);
        })
        ->where('term_id', $request->term_id)
        ->update(['status' => 'submitted']); // Assuming a 'status' column exists in the grades table

        return response()->json(['message' => 'Notes submitted successfully for the term.']);
    }

    public function getStudentGradesInClassForTerm($classId, $termId, $studentId)
    {
        $currentAcademicYear = \App\Modules\AcademicYear\Models\AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return response()->json(['message' => 'No current academic year found.'], 404);
        }

        $studentSession = \App\Modules\Student\Models\StudentSession::where('student_id', $studentId)
            ->where('class_model_id', $classId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();

        if (!$studentSession) {
            return response()->json(['message' => 'Student session not found for the given student, class, and academic year.'], 404);
        }

        $assignments = \App\Modules\Assignement\Models\Assignement::where('class_model_id', $classId)
            ->where('term_id', $termId)
            ->with('subject')
            ->get();

        $grades = [];
        foreach ($assignments as $assignment) {
            $grade = Grade::where('assignement_id', $assignment->id)
                ->where('student_session_id', $studentSession->id)
                ->where('term_id', $termId)
                ->first();

            $grades[] = [
                'id' => $grade ? $grade->id : null,
                'mark' => $grade ? $grade->mark : null,
                'type' => $grade ? $grade->type : null,
                'subject_name' => $assignment->subject->name,
                'assignement_id' => $assignment->id,
                'student_session_id' => $studentSession->id,
                'term_id' => (int) $termId,
            ];
        }

        return response()->json($grades);
    }
}
