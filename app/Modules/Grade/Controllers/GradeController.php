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
}
