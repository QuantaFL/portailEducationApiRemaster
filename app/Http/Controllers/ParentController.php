<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentRequest;
use App\Http\Resources\ParentResource;
use Parent;

class ParentController extends Controller
{
    public function index()
    {
        return ParentResource::collection(Parent::all());
    }

    public function store(ParentRequest $request)
    {
        return new ParentResource(Parent::create($request->validated()));
    }

    public function show(Parent $parent)
    {
        return new ParentResource($parent);
    }

    public function update(ParentRequest $request, Parent $parent)
    {
        $parent->update($request->validated());

        return new ParentResource($parent);
    }

    public function destroy(Parent $parent)
    {
        $parent->delete();

        return response()->json();
    }
}
