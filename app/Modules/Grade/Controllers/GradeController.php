<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Term\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function getGradesByTerm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term_id' => 'required|exists:terms,id',
            'class_model_id' => 'required|exists:class_models,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $term = Term::find($request->term_id);
        $today = now();

        if ($today->isAfter($term->end_date)) {
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

    public function updateGrades(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'grades' => 'required|array',
            'grades.*.student_session_id' => 'required|exists:student_sessions,id',
            'grades.*.term_id' => 'required|exists:terms,id',
            'grades.*.assignement_id' => 'required|exists:assignments,id',
            'grades.*.mark' => 'required|numeric|min:0|max:20',
            'grades.*.type' => 'required|in:quiz,exam',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

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
}
