<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Requests\TeacherRequest;
use App\Modules\Teacher\Ressources\TeacherResource;
use App\Modules\User\Models\UserModel;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use Illuminate\Support\Facades\Request;

class TeacherController extends Controller
{
    public function index()
    {
        return response()->json(TeacherResource::collection(Teacher::all()));
    }

    public function store(TeacherRequest $request)
    {
        $user = UserModel::create($request->validated('user'));
        $teacher = Teacher::create([
            'hire_date' => $request->validated('hire_date'),
            'user_model_id' => $user->id,
        ]);
        return response()->json(new TeacherResource($teacher));
    }

    public function show(Teacher $teacher)
    {
        return response()->json(new TeacherResource($teacher));
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $teacherRequest = $request->validated();

        $refUser = UserModel::findOrFail($teacher->user_model_id);

        $refUser->update($teacherRequest['user']);

      //  $teacher->update($teacherRequest);

        return response()->json(new TeacherResource($teacher));
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json();
    }

    public function getClasses(\Illuminate\Http\Request $request, $teacherId)
    {
        $academicYearId = $request->query('academic_year_id');
        if (!$academicYearId) {
            return response()->json(['message' => 'academic_year_id is required'], 400);
        }

        $classIds = Assignement::where('teacher_id', $teacherId)
            ->join('terms', 'assignments.term_id', '=', 'terms.id')
            ->join('academic_years', 'terms.academic_year_id', '=', 'academic_years.id')
            ->where('academic_years.id', $academicYearId)
            ->pluck('assignments.class_model_id')
            ->unique()
            ->toArray();

        $classes = \App\Modules\ClassModel\Models\ClassModel::whereIn('id', $classIds)->get();
        return response()->json(ClassModelResource::collection($classes));
    }
}
