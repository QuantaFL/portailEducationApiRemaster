<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassModelRequest;
use App\Http\Resources\ClassModelResource;
use App\Models\ClassModel;

class ClassModelController extends Controller
{
    public function index()
    {
        return ClassModelResource::collection(ClassModel::all());
    }

    public function store(ClassModelRequest $request)
    {
        return new ClassModelResource(ClassModel::create($request->validated()));
    }

    public function show(ClassModel $classModel)
    {
        return new ClassModelResource($classModel);
    }

    public function update(ClassModelRequest $request, ClassModel $classModel)
    {
        $classModel->update($request->validated());

        return new ClassModelResource($classModel);
    }

    public function destroy(ClassModel $classModel)
    {
        $classModel->delete();

        return response()->json();
    }
}
