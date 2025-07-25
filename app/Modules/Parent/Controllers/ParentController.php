<?php

namespace App\Modules\Parent\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parent\Models\ParentModel;
use App\Modules\Parent\Requests\ParentRequest;
use App\Modules\Parent\Ressources\ParentResource;

class ParentController extends Controller
{
    public function index()
    {
        return response()->json(ParentResource::collection(ParentModel::all()));
    }

    public function store(ParentRequest $request)
    {
        return response()->json(new ParentResource(ParentModel::create($request->validated())));
    }

    public function show(ParentModel $parent)
    {
        return response()->json(new ParentResource($parent));
    }

    public function update(ParentRequest $request, ParentModel $parent)
    {
        $parent->update($request->validated());

        return response()->json(new ParentResource($parent));
    }

    public function destroy(ParentModel $parent)
    {
        $parent->delete();

        return response()->json();
    }
}
