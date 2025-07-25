<?php

namespace App\Modules\Session\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Session\Models\AcademicYear;
use App\Modules\Session\Models\StatutsSessionEnum;
use App\Modules\Session\Requests\SessionRequest;
use App\Modules\Session\Ressources\SessionResource;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function index()
    {
        return response()->json(SessionResource::collection(AcademicYear::all()));
    }

    public function store(SessionRequest $request)
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


        $label = $start . '-' . $end;
        $statusSession = StatutsSessionEnum::EN_COURS;
        $createdSession =  AcademicYear::create([
            "label"=>$label,
            "start_date"=>$request->start_date,
            "end_date"=>$request->end_date,
            "status"=>$statusSession
        ]);
        return response()->json(new SessionResource($createdSession));
    }

    public function show(AcademicYear $session)
    {
        $exists = DB::table('academic_years')
            ->where('label', $session)
            ->exists();
        if (!$exists) {
            return response()->json([
                "message"=> 'aucune session n\' a ce libelle  ',
            ],404);

        }

        return response()->json(new SessionResource($exists));
    }

    public function update(SessionRequest $request, AcademicYear $session)
    {
        $session->update($request->validated());

        return response()->json(new SessionResource($session));
    }

    public function destroy(AcademicYear $session)
    {
        $session->delete();

        return response()->json();
    }
}
