<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\modules\teacher\models\Teacher;
use App\modules\teacher\requests\TeacherRequest;
use App\modules\teacher\ressources\TeacherResource;

class TeacherController extends Controller
{
    public function index()
    {
        return response()->json(TeacherResource::collection(Teacher::all()));
    }

    public function store(TeacherRequest $request)
    {
        return response()->json(new TeacherResource(Teacher::create($request->validated())));
    }

    public function show(Teacher $teacher)
    {
        return response()->json(new TeacherResource($teacher));
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());

        return response()->json(new TeacherResource($teacher));
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json();
    }
}
