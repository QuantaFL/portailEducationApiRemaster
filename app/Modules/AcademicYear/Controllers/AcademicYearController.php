<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\StatusAcademicYearEnum;
use App\Modules\AcademicYear\Requests\AcademicYearRequest;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;
use App\Modules\Term\Models\Term;
use App\Modules\Term\Ressources\TermResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        return response()->json(AcademicYearResource::collection(AcademicYear::all()));
    }
    //TODO:rollback if terms are not save in database
    public function store(AcademicYearRequest $request)
    {
        $start = (int) $request->start_date;
        $end = (int) $request->end_date;
        $currentYear = now()->year;

        $exists = DB::table('academic_years')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->exists();

        if ($exists) {
            return response()->json([
                "message"=> 'Une session avec cette période existe déjà.',
            ],400);

        }

        if ($start < $currentYear) {

            return response()->json([
                "message"=>  'L\'année de début doit être supérieure ou égale à l\'année en cours (' . $currentYear . ').',
            ],400);

        }

        if ($start >= $end) {

            return response()->json([
                "message" => 'L\'année de fin doit être supérieure à celle de début.',
            ],400);

        }

        if (($end - $start) !== 1) {
            return response()->json([
                "message" => 'Une année académique doit durer exactement 1 an.',
            ],400);

        }
        DB::table('academic_years')->update([
            'status' => StatusAcademicYearEnum::TERMINE
        ]);


        $label = $start . '-' . $end;
        $statusACDMY = StatusAcademicYearEnum::EN_COURS;
        $createdACDMY =  AcademicYear::create([
            "label"=>$label,
            "start_date"=>$request->start_date,
            "end_date"=>$request->end_date,
            "status"=>$statusACDMY
        ]);
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->startOfDay();
        $term1 = Term::create([
            "name"=>"Semestre 1",
            'academic_year_id'=>$createdACDMY->id,
            "start_date"=>$start_date,
            "end_date"=>$end_date
        ]);
        $term2 = Term::create([
            "name"=>"Semestre 2",
            'academic_year_id'=>$createdACDMY->id,
            "start_date"=>$start_date,
            "end_date"=>$end_date
        ]);
        return response()->json([
           "academic_year"=> new AcademicYearResource($createdACDMY),
            "terms"=>[
                "term1"=>$term1,
                "term2"=>$term2,
            ]
        ]);
    }

    public function show(AcademicYear $session)
    {
        return response()->json(new AcademicYearResource($session));
    }

    public function update(AcademicYearRequest $request, AcademicYear $session)
    {
        $session->update($request->validated());

        return response()->json(new AcademicYearResource($session));
    }

    public function destroy(AcademicYear $session)
    {
        $session->delete();

        return response()->json();
    }

    public function getCurrentAcademicYear()
    {
        $currentYear = AcademicYear::where('status', 'current')->first();

        if (!$currentYear) {
            return response()->json(['message' => 'No current academic year found'], 404);
        }

        return response()->json(new AcademicYearResource($currentYear));
    }
    public function getActiveAcademicYears()
    {
        $activeYears = AcademicYear::where('status', 'active')->get();

        if ($activeYears->isEmpty()) {
            return response()->json(['message' => 'No active academic years found'], 404);
        }

        return response()->json(AcademicYearResource::collection($activeYears));
    }

    public function getAcademicYearById($id)
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            return response()->json(['message' => 'Academic year not found'], 404);
        }

        return response()->json(new AcademicYearResource($academicYear));
    }

    public function getTermsByAcademicYear($academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId);

        if (!$academicYear) {
            return response()->json(['message' => 'Academic year not found'], 404);
        }

        $terms = $academicYear->terms;

        return response()->json(TermResource::collection($terms));
    }
}
