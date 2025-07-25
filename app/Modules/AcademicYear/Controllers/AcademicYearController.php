<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Requests\AcademicYearRequest;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;

class AcademicYearController extends Controller
{
    public function index()
    {
        return response()->json(AcademicYearResource::collection(AcademicYear::all()));
    }

    public function store(AcademicYearRequest $request)
    {
        return response()->json(new AcademicYearResource(AcademicYear::create($request->validated())));
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
}
