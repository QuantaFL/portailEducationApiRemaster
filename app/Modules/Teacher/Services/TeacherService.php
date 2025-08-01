<?php

namespace App\Modules\Teacher\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
use App\Modules\User\Models\UserModel;
use App\Modules\Assignement\Models\Assignement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class TeacherService
{
    /**
     * Get all teachers
     */
    public function getAllTeachers(): Collection
    {
        Log::info('TeacherService: Fetching all teachers');
        
        $teachers = Teacher::all();
        
        Log::info('TeacherService: Retrieved ' . $teachers->count() . ' teachers');
        
        return $teachers;
    }

    /**
     * Create a new teacher
     */
    public function createTeacher(array $validatedData): Teacher
    {
        Log::info('TeacherService: Creating new teacher', [
            'hire_date' => $validatedData['hire_date'],
            'role_id' => $validatedData['role_id'] ?? null,
            'nationality' => $validatedData['nationality'] ?? null,
            'user_email' => $validatedData['user']['email'] ?? null
        ]);

        try {
            // Prepare user data with role_id and nationality
            $userData = $validatedData['user'];
            $userData['role_id'] = $validatedData['role_id'];
            
            if (isset($validatedData['nationality'])) {
                $userData['nationality'] = $validatedData['nationality'];
            }

            // Create user
            $user = UserModel::create($userData);
            Log::info('TeacherService: User created successfully', ['user_id' => $user->id]);

            // Create teacher
            $teacher = Teacher::create([
                'hire_date' => $validatedData['hire_date'],
                'user_model_id' => $user->id,
            ]);

            Log::info('TeacherService: Teacher created successfully', [
                'teacher_id' => $teacher->id,
                'user_id' => $user->id
            ]);

            return $teacher;

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to create teacher', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update teacher information
     */
    public function updateTeacher(Teacher $teacher, array $validatedData): Teacher
    {
        Log::info('TeacherService: Updating teacher', ['teacher_id' => $teacher->id]);

        try {
            $refUser = UserModel::findOrFail($teacher->user_model_id);
            
            Log::info('TeacherService: Found user to update', ['user_id' => $refUser->id]);

            $refUser->update($validatedData['user']);

            Log::info('TeacherService: Teacher updated successfully', ['teacher_id' => $teacher->id]);

            return $teacher->fresh();

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to update teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a teacher
     */
    public function deleteTeacher(Teacher $teacher): bool
    {
        Log::info('TeacherService: Deleting teacher', ['teacher_id' => $teacher->id]);

        try {
            $teacher->delete();
            
            Log::info('TeacherService: Teacher deleted successfully', ['teacher_id' => $teacher->id]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('TeacherService: Failed to delete teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get teacher subjects for current academic year
     */
    public function getTeacherSubjects(int $teacherId): Collection
    {
        Log::info('TeacherService: Getting subjects for teacher', ['teacher_id' => $teacherId]);

        $academicYear = AcademicYear::getCurrentAcademicYear();
        
        if (!$academicYear) {
            Log::warning('TeacherService: No current academic year found');
            throw new \Exception('No current academic year found');
        }

        Log::info('TeacherService: Using academic year', ['academic_year_id' => $academicYear->id]);

        $subjects = Assignement::where('teacher_id', $teacherId)
            ->join('terms', 'assignments.term_id', '=', 'terms.id')
            ->join('academic_years', 'terms.academic_year_id', '=', 'academic_years.id')
            ->where('academic_years.id', $academicYear->id)
            ->with('subject')
            ->get()
            ->pluck('subject');

        Log::info('TeacherService: Found subjects for teacher', [
            'teacher_id' => $teacherId,
            'subjects_count' => $subjects->count()
        ]);

        return $subjects;
    }

    /**
     * Get teacher classes for current academic year
     */
    public function getTeacherClasses(int $teacherId): Collection
    {
        Log::info('TeacherService: Getting classes for teacher', ['teacher_id' => $teacherId]);

        $academicYear = AcademicYear::getCurrentAcademicYear();
        
        if (!$academicYear) {
            Log::warning('TeacherService: No current academic year found');
            throw new \Exception('No current academic year found');
        }

        $classIds = Assignement::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYear->id)
            ->pluck('class_model_id')
            ->unique()
            ->toArray();

        Log::info('TeacherService: Found class IDs for teacher', [
            'teacher_id' => $teacherId,
            'class_ids' => $classIds
        ]);

        $classes = ClassModel::whereIn('id', $classIds)->get();

        Log::info('TeacherService: Retrieved classes for teacher', [
            'teacher_id' => $teacherId,
            'classes_count' => $classes->count()
        ]);

        return $classes;
    }

    /**
     * Get teacher by user ID
     */
    public function getTeacherByUserId(int $userId): ?Teacher
    {
        Log::info('TeacherService: Getting teacher by user ID', ['user_id' => $userId]);

        $teacher = Teacher::where('user_model_id', $userId)->first();

        if ($teacher) {
            Log::info('TeacherService: Teacher found', ['teacher_id' => $teacher->id, 'user_id' => $userId]);
        } else {
            Log::warning('TeacherService: No teacher found for user ID', ['user_id' => $userId]);
        }

        return $teacher;
    }

    /**
     * Get teacher profile with assigned subjects
     */
    public function getTeacherProfile(UserModel $user): array
    {
        Log::info('TeacherService: Getting teacher profile', ['user_id' => $user->id]);

        $teacher = Teacher::where('user_model_id', $user->id)->first();

        if (!$teacher) {
            Log::warning('TeacherService: Teacher profile not found', ['user_id' => $user->id]);
            throw new \Exception('Teacher profile not found');
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

        Log::info('TeacherService: Teacher profile retrieved', [
            'teacher_id' => $teacher->id,
            'assigned_subjects_count' => count($assignedSubjects)
        ]);

        return [
            'id' => $teacher->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'assigned_subjects' => $assignedSubjects,
        ];
    }

    /**
     * Get multi-class performance summary
     */
    public function getMultiClassPerformanceSummary(array $classSubjects): array
    {
        Log::info('TeacherService: Getting multi-class performance summary', [
            'class_subjects_count' => count($classSubjects)
        ]);

        $currentTerm = Term::getCurrentTerm();
        if (!$currentTerm) {
            Log::warning('TeacherService: No current term found');
            throw new \Exception('No current term found');
        }

        // For now, using teacher ID 1 as in original code
        // This should be improved to use authenticated teacher
        $teacher = Teacher::where('teachers.id', 1)->first();
        if (!$teacher) {
            Log::error('TeacherService: Teacher profile not found for performance summary');
            throw new \Exception('Teacher profile not found');
        }

        Log::info('TeacherService: Using teacher and term for performance summary', [
            'teacher_id' => $teacher->id,
            'term_id' => $currentTerm->id
        ]);

        $allPerformances = [];

        foreach ($classSubjects as $pair) {
            $class = ClassModel::find($pair['classId']);
            if (!$class) {
                Log::warning('TeacherService: Class not found', ['class_id' => $pair['classId']]);
                continue;
            }

            $studentsInClass = $class->currentAcademicYearStudentSessions()->pluck('student_id')->toArray();
            $students = Student::whereIn('id', $studentsInClass)->get();

            Log::info('TeacherService: Processing class for performance', [
                'class_id' => $pair['classId'],
                'subject_id' => $pair['subjectId'],
                'students_count' => $students->count()
            ]);

            foreach ($students as $student) {
                $studentSession = $student->latestStudentSession;
                if (!$studentSession) {
                    continue;
                }

                $grades = Grade::where('student_session_id', $studentSession->id)
                    ->where('term_id', $currentTerm->id)
                    ->whereHas('assignement', function ($query) use ($pair, $teacher) {
                        $query->where('subject_id', $pair['subjectId'])
                            ->where('teacher_id', $teacher->id);
                    })
                    ->get();

                if ($grades->isNotEmpty()) {
                    $averageGrade = $grades->avg('mark');
                    $allPerformances[] = [
                        'studentId' => $student->id,
                        'firstName' => $student->userModel->first_name,
                        'lastName' => $student->userModel->last_name,
                        'profilePictureUrl' => $student->userModel->profile_picture_url,
                        'classId' => $pair['classId'],
                        'subjectId' => $pair['subjectId'],
                        'averageGrade' => round($averageGrade, 2),
                    ];
                }
            }
        }

        if (empty($allPerformances)) {
            Log::warning('TeacherService: No grades found for performance summary');
            throw new \Exception('No grades found for the provided classes, subjects, and term');
        }

        $best = collect($allPerformances)->sortByDesc('averageGrade')->first();
        $worst = collect($allPerformances)->sortBy('averageGrade')->first();

        Log::info('TeacherService: Performance summary completed', [
            'total_performances' => count($allPerformances),
            'best_student_id' => $best['studentId'],
            'worst_student_id' => $worst['studentId']
        ]);

        return [
            'bestPerformingStudent' => $best,
            'worstPerformingStudent' => $worst,
        ];
    }
}