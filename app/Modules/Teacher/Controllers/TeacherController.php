<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Requests\TeacherRequest;
use App\Modules\Teacher\Ressources\TeacherResource;
use App\Modules\Teacher\Services\TeacherService;
use App\Modules\ClassModel\Ressources\ClassModelResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function index()
    {
        try {
            $teachers = $this->teacherService->getAllTeachers();
            return response()->json(TeacherResource::collection($teachers));
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get teachers', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve teachers'], 500);
        }
    }

    public function store(TeacherRequest $request)
    {
        try {
            $teacher = $this->teacherService->createTeacher($request->validated());
            return response()->json(new TeacherResource($teacher));
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to create teacher', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create teacher'], 500);
        }
    }

    public function show(Teacher $teacher)
    {
        Log::info('TeacherController: Showing teacher', ['teacher_id' => $teacher->id]);
        return response()->json(new TeacherResource($teacher));
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        try {
            $updatedTeacher = $this->teacherService->updateTeacher($teacher, $request->validated());
            return response()->json(new TeacherResource($updatedTeacher));
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to update teacher', ['error' => $e->getMessage(), 'teacher_id' => $teacher->id]);
            return response()->json(['message' => 'Failed to update teacher'], 500);
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $this->teacherService->deleteTeacher($teacher);
            return response()->json();
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to delete teacher', ['error' => $e->getMessage(), 'teacher_id' => $teacher->id]);
            return response()->json(['message' => 'Failed to delete teacher'], 500);
        }
    }

    public function getTeacherSubjects(Request $request, $teacherId)
    {
        try {
            $subjects = $this->teacherService->getTeacherSubjects($teacherId);
            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get teacher subjects', ['error' => $e->getMessage(), 'teacher_id' => $teacherId]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function getClasses(Request $request, $teacherId)
    {
        try {
            $classes = $this->teacherService->getTeacherClasses($teacherId);
            return response()->json(ClassModelResource::collection($classes));
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get teacher classes', ['error' => $e->getMessage(), 'teacher_id' => $teacherId]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getTeacherProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            Log::warning('TeacherController: Unauthenticated user trying to access profile');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $profile = $this->teacherService->getTeacherProfile($user);
            return response()->json($profile);
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get teacher profile', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json(['message' => 'Teacher profile not found.'], 404);
        }
    }

    public function getTeacherByUserId($userId)
    {
        try {
            $teacher = $this->teacherService->getTeacherByUserId($userId);
            
            if (!$teacher) {
                return response()->json(['message' => 'Teacher not found for this user ID.'], 404);
            }

            return response()->json(new TeacherResource($teacher));
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get teacher by user ID', ['error' => $e->getMessage(), 'user_id' => $userId]);
            return response()->json(['message' => 'Failed to retrieve teacher'], 500);
        }
    }

    /**
     * Get best and worst performing students across multiple classes and subjects.
     *
     * Expects payload:
     * {
     *   "classSubjects": [
     *     { "classId": 1, "subjectId": 2 },
     *     { "classId": 3, "subjectId": 4 }
     *   ]
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMultiClassPerformanceSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classSubjects' => 'required|array|min:1',
            'classSubjects.*.classId' => 'required|integer|exists:class_models,id',
            'classSubjects.*.subjectId' => 'required|integer|exists:subjects,id',
        ]);

        try {
            $performanceSummary = $this->teacherService->getMultiClassPerformanceSummary($validated['classSubjects']);
            return response()->json($performanceSummary);
        } catch (\Exception $e) {
            Log::error('TeacherController: Failed to get performance summary', ['error' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
