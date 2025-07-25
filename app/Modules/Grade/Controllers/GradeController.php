<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Models\Grade;
use App\Modules\Grade\Requests\GradeRequest;
use App\Modules\Grade\Ressources\GradeResource;

class GradeController extends Controller
{
    public function index()
    {
        return response()->json(GradeResource::collection(Grade::all()));
    }

    public function store(GradeRequest $request)
    {
        return response()->json(new GradeResource(Grade::create($request->validated())));
    }

    public function show(Grade $grade)
    {
        return response()->json(new GradeResource($grade));
    }

    public function update(GradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());

        return response()->json(new GradeResource($grade));
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return response()->json();
    }
}
