<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignementRequest;
use App\Http\Resources\AssignementResource;
use App\Models\Assignement;

class AssignementController extends Controller
{
    public function index()
    {
        return AssignementResource::collection(Assignement::all());
    }

    public function store(AssignementRequest $request)
    {
        return new AssignementResource(Assignement::create($request->validated()));
    }

    public function show(Assignement $assignement)
    {
        return new AssignementResource($assignement);
    }

    public function update(AssignementRequest $request, Assignement $assignement)
    {
        $assignement->update($request->validated());

        return new AssignementResource($assignement);
    }

    public function destroy(Assignement $assignement)
    {
        $assignement->delete();

        return response()->json();
    }
}
