<?php

namespace App\Modules\Parent\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parent\Models\Parent;
use App\Modules\Parent\Requests\ParentRequest;
use App\Modules\Parent\Ressources\ParentResource;

class ParentController extends Controller
{
    public function index()
    {
        return response()->json(ParentResource::collection(Parent::all()));
    }

    public function store(ParentRequest $request)
    {
        return response()->json(new ParentResource(Parent::create($request->validated())));
    }

    public function show(Parent $parent)
    {
        return response()->json(new ParentResource($parent));
    }

    public function update(ParentRequest $request, Parent $parent)
    {
        $parent->update($request->validated());

        return response()->json(new ParentResource($parent));
    }

    public function destroy(Parent $parent)
    {
        $parent->delete();

        return response()->json();
    }
}
