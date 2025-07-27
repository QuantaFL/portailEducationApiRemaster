<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Requests\TeacherRequest;
use App\Modules\Teacher\Ressources\TeacherResource;
use App\Modules\User\Models\UserModel;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Modules\AcademicYear\Controllers\AcademicYearController;
use App\Modules\Term\Controllers\TermController;

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

    public function getTeacherSubjects(Request $request, $teacherId)
    {
        $academicYear = ACademicYear::getCurrentAcademicYear();
        if (!$academicYear) {
            return response()->json(['message' => 'academic_year_id is required'], 400);
        }

        $subjects = Assignement::where('teacher_id', $teacherId)
            ->join('terms', 'assignments.term_id', '=', 'terms.id')
            ->join('academic_years', 'terms.academic_year_id', '=', 'academic_years.id')
            ->where('academic_years.id', $academicYear->id)
            ->with('subject')
            ->get()
            ->pluck('subject');

        Log::info('Subjects found for teacher ' . $teacherId . ': ' . $subjects->count());
        return response()->json($subjects);
    }
    public function getClasses(Request $request, $teacherId)
    {
        $academicYear = AcademicYear::getCurrentAcademicYear();
        if (!$academicYear) {
            return response()->json(['message' => 'academic_year_id is required'], 400);
        }

        $classIds = Assignement::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYear->id)
            ->pluck('class_model_id')
            ->unique()
            ->toArray();
        Log::info('Class IDs for teacher ' . $teacherId);

        $classes = ClassModel::whereIn('id', $classIds)->get();
        Log::info('Classes found for teacher ' . $teacherId . ': ' . $classes->count());
        return response()->json(ClassModelResource::collection($classes));
    }

    public function getTeacherProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $teacher = Teacher::where('user_model_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found.'], 404);
        }

        $assignedSubjects = Assignement::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->pluck(function ($assignement) {
                return optional($assignement->subject)->name;
            })
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'id' => $teacher->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'assigned_subjects' => $assignedSubjects,
        ]);
    }

    public function getTeacherByUserId($userId)
    {
        $teacher = Teacher::where('user_model_id', $userId)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found for this user ID.'], 404);
        }

        return response()->json(new TeacherResource($teacher));
    }
}
