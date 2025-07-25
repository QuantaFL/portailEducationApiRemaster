<?php

namespace App\Modules\ClassModel\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassModelRequest;
use App\Http\Resources\ClassModelResource;
use App\Modules\ClassModel\Models\ClassModel;

class ClassModelController extends Controller
{
    public function index()
    {
        return response()->json(ClassModelResource::collection(ClassModel::all()));
    }

    public function store(ClassModelRequest $request)
    {
        return  response()->json(new  ClassModelResource(ClassModel::create($request->validated()))) ;
    }

    public function show(ClassModel $classModel)
    {
        return response()->json(new ClassModelResource($classModel)) ;
    }

    public function update(ClassModelRequest $request, ClassModel $classModel)
    {
        $classModel->update($request->validated());

        return response()->json(new ClassModelResource($classModel));
    }

    public function destroy(ClassModel $classModel)
    {
        $classModel->delete();

        return response()->json();
    }
}
