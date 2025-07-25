<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Requests\TeacherRequest;
use App\Modules\Teacher\Ressources\TeacherResource;
use App\Modules\User\Models\UserModel;

class TeacherController extends Controller
{
    public function index()
    {
        return response()->json(TeacherResource::collection(Teacher::all()));
    }

    public function store(TeacherRequest $request)
    {
        $user = UserModel::create($request->user);
        $teacher = new Teacher();
        $teacher->hire_date = $request->hire_date;
        $teacher->user_model_id = $user->id;
        $teacher->save();
        return response()->json(new TeacherResource($teacher));
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
