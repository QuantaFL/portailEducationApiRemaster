<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;

class GradeController extends Controller
{
    public function index()
    {
        return GradeResource::collection(Grade::all());
    }

    public function store(GradeRequest $request)
    {
        return new GradeResource(Grade::create($request->validated()));
    }

    public function show(Grade $grade)
    {
        return new GradeResource($grade);
    }

    public function update(GradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());

        return new GradeResource($grade);
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return response()->json();
    }
}
