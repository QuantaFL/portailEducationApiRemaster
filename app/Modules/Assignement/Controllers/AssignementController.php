<?php

namespace App\Modules\Assignement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Assignement\Requests\AssignementRequest;
use App\Modules\Assignement\Ressources\AssignementResource;

class AssignementController extends Controller
{
    public function index()
    {
        return response()->json(AssignementResource::collection(Assignement::all()));
    }

    public function store(AssignementRequest $request)
    {
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
}
