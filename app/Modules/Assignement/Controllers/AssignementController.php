<?php

namespace App\Modules\Assignement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\StatusAcademicYearEnum;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Requests\AssignementRequest;
use App\Modules\Assignement\Ressources\AssignementResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignementController extends Controller
{
    public function index()
    {
        return response()->json(AssignementResource::collection(Assignement::all()));
    }

    public function store(AssignementRequest $request)
    {
        /*
         *  'teacher_id' => ['required', 'exists:teachers'],
            'class_model_id' => ['required', 'exists:class_models'],
            'subject_id' => ['required', 'exists:subjects'],
         * */
        //$currentYear = DB::table("academic_years")
           // ->where("status",StatusAcademicYearEnum::EN_COURS);
        return response()->json(new AssignementResource(Assignement::create($request->validated())));


    }

    public function show(Assignement $assignement)
    {
        return response()->json(new AssignementResource($assignement));
    }

    public function update(AssignementRequest $request, Assignement $assignement)
    {
        $assignement->update($request->validated());

        return response()->json(new AssignementResource($assignement));
    }

    public function destroy(Assignement $assignement)
    {
        $assignement->delete();

        return response()->json();
    }

    public function getByTermAndClass(Request $request)
    {
        $termId = $request->input('term_id');
        $classModelId = $request->input('class_model_id');

        $assignements = Assignement::where('term_id', $termId)
            ->where('class_model_id', $classModelId)
            ->get();

        return response()->json(AssignementResource::collection($assignements));
    }
}
