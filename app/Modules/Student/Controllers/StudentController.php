<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Requests\StudentRequest;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\User\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(StudentResource::collection(Student::all()));
    }

    public function store(StudentRequest $request)
    {
        $userData = $request->validated('user');
        $user = UserModel::create($userData);

        $studentData = $request->validated();
        $student = Student::create([
            'matricule' => $studentData['matricule'],
            'academic_records' => $studentData['academic_records'],
            'parent_model_id' => $studentData['parent_id'],
            'user_model_id' => $user->id,
        ]);

        return response()->json(new StudentResource($student));
    }

    public function show(Student $student)
    {
        return response()->json(new StudentResource($student));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return response()->json(new StudentResource($student));
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json();
    }

    public function bulk(Request $request)
    {
        $ids = $request->input('ids', []);
        $students = Student::whereIn('id', $ids)->get();
        return response()->json(StudentResource::collection($students));
    }

    public function getStudentsByUserId(string $id): JsonResponse
    {
        $students = Student::where('user_model_id', $id)->get();
        return response()->json(StudentResource::collection($students));
    }

    public function getStudentDetails(string $id): JsonResponse
    {
        $student = Student::with([
            'userModel',
            'parentModel.userModel',
            'latestStudentSession.classModel',
            'latestStudentSession.academicYear',
        ])->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found for the given user_model_id.'
            ], 404);
        }

        return response()->json(new StudentResource($student));
    }

    /**
     * Get the next classes (timetable) for a student.
     *
     * @param string $id Student ID
     * @return JsonResponse
     */
    public function getNextClasses(string $id): JsonResponse
    {
        // Fetch the student with their latest session and class
        $student = Student::with(['latestStudentSession.classModel'])->find($id);

        if (!$student || !$student->latestStudentSession || !$student->latestStudentSession->classModel) {
            return response()->json([], 200);
        }

        $classModel = $student->latestStudentSession->classModel;

        $assignments = Assignement::where('class_model_id', $classModel->id)
            ->with(['subject', 'teacher'])
            ->orderBy('start_time')
            ->take(3)
            ->get();

        $nextClasses = $assignments->map(function ($item) {
            $subjectColors = [
                'français' => '#6366f1',
                'mathématiques' => '#00bcd4',
                'histoire-géographie' => '#f59e42',
                'anglais' => '#10b981',
                'sciences de la vie et de la terre' => '#22d3ee',
                'physique-chimie' => '#9c27b0',
                'éducation civique' => '#fbbf24',
                'espagnol' => '#ef4444',
                'arabe' => '#f97316',
                'informatique' => '#0ea5e9',
                'philosophie' => '#a3e635',
            ];
            $subjectKey = strtolower($item->subject->name ?? '');
            $teacherName = 'Unknown';
            if ($item->teacher && $item->teacher->userModel) {
                $firstName = $item->teacher->userModel->first_name ?? '';
                $lastName = $item->teacher->userModel->last_name ?? '';
                $teacherName = trim($firstName . ' ' . $lastName);
                if ($teacherName === '') {
                    $teacherName = 'Unknown';
                }
            }
            Log::debug('Processing assignment', [
                'subject' => $item->subject->name ?? 'Unknown',
                'teacher' => $teacherName,
                'start_time' => $item->start_time,
            ]);
            return [
                'subject' => $item->subject->name ?? 'Unknown',
                'teacher' => $teacherName,
                'time' => $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '--:--',
                'color' => $subjectColors[$subjectKey] ?? '#6366f1',
            ];
        })->values()->toArray();

        return response()->json($nextClasses);
    }
}
