<?php

namespace App\Modules\Assignement\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Assignement\Exceptions\AssignmentException;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentService
{
    public function getAllAssignments(): Collection
    {
        return Assignement::all();
    }

    public function getAssignmentById(int $id): Assignement
    {
        $assignment = Assignement::find($id);
        
        if (!$assignment) {
            throw AssignmentException::assignmentNotFound();
        }
        
        return $assignment;
    }

    public function getAssignmentsForTeacher(int $teacherId): Collection
    {
        $teacher = Teacher::find($teacherId);
        if (!$teacher) {
            throw AssignmentException::teacherNotFound();
        }

        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentAcademicYear) {
            throw AssignmentException::academicYearNotFound();
        }

        return Assignement::where('teacher_id', $teacherId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->get();
    }

    public function getAssignmentsByTermAndClass(int $termId, int $classId): Collection
    {
        $class = ClassModel::find($classId);
        if (!$class) {
            throw AssignmentException::classNotFound();
        }

        return Assignement::where('class_model_id', $classId)
            ->whereHas('academicYear.terms', function ($query) use ($termId) {
                $query->where('terms.id', $termId);
            })
            ->get();
    }

    public function createAssignment(array $data): Assignement
    {
        $this->validateAssignmentData($data);
        
        DB::beginTransaction();
        
        try {
            // Set current academic year if not provided
            if (!isset($data['academic_year_id'])) {
                $currentAcademicYear = AcademicYear::getCurrentAcademicYear();
                if ($currentAcademicYear) {
                    $data['academic_year_id'] = $currentAcademicYear->id;
                }
            }

            $assignment = Assignement::create($data);
            
            DB::commit();
            
            return $assignment;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create assignment: ' . $e->getMessage());
            throw AssignmentException::creationFailed();
        }
    }

    public function updateAssignment(Assignement $assignment, array $data): Assignement
    {
        $this->validateAssignmentData($data, $assignment->id);
        
        DB::beginTransaction();
        
        try {
            $assignment->update($data);
            
            DB::commit();
            
            return $assignment;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update assignment: ' . $e->getMessage());
            throw AssignmentException::updateFailed();
        }
    }

    public function deleteAssignment(Assignement $assignment): bool
    {
        try {
            return $assignment->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete assignment: ' . $e->getMessage());
            throw AssignmentException::deletionFailed();
        }
    }

    private function validateAssignmentData(array $data, ?int $excludeId = null): void
    {
        // Validate teacher exists
        if (isset($data['teacher_id'])) {
            $teacher = Teacher::find($data['teacher_id']);
            if (!$teacher) {
                throw AssignmentException::teacherNotFound();
            }
        }

        // Validate class exists
        if (isset($data['class_model_id'])) {
            $class = ClassModel::find($data['class_model_id']);
            if (!$class) {
                throw AssignmentException::classNotFound();
            }
        }

        // Validate subject exists
        if (isset($data['subject_id'])) {
            $subject = Subject::find($data['subject_id']);
            if (!$subject) {
                throw AssignmentException::subjectNotFound();
            }
        }

        // Validate academic year exists
        if (isset($data['academic_year_id'])) {
            $academicYear = AcademicYear::find($data['academic_year_id']);
            if (!$academicYear) {
                throw AssignmentException::academicYearNotFound();
            }
        }

        // Validate time slot if provided
        if (isset($data['start_time']) && isset($data['end_time'])) {
            if ($data['start_time'] >= $data['end_time']) {
                throw AssignmentException::invalidTimeSlot();
            }
        }

        // Check for duplicate assignments
        $this->checkForDuplicateAssignment($data, $excludeId);
        
        // Check for schedule conflicts
        $this->checkForScheduleConflicts($data, $excludeId);
    }

    private function checkForDuplicateAssignment(array $data, ?int $excludeId = null): void
    {
        if (!isset($data['teacher_id'], $data['class_model_id'], $data['subject_id'])) {
            return;
        }

        $query = Assignement::where('teacher_id', $data['teacher_id'])
            ->where('class_model_id', $data['class_model_id'])
            ->where('subject_id', $data['subject_id']);

        if (isset($data['academic_year_id'])) {
            $query->where('academic_year_id', $data['academic_year_id']);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw AssignmentException::duplicateAssignment();
        }
    }

    private function checkForScheduleConflicts(array $data, ?int $excludeId = null): void
    {
        if (!isset($data['day_of_week'], $data['start_time'], $data['end_time'])) {
            return;
        }

        // Check teacher conflicts
        if (isset($data['teacher_id'])) {
            $teacherConflict = Assignement::where('teacher_id', $data['teacher_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where(function ($query) use ($data) {
                    $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                          ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                          ->orWhere(function ($q) use ($data) {
                              $q->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                          });
                });

            if ($excludeId) {
                $teacherConflict->where('id', '!=', $excludeId);
            }

            if ($teacherConflict->exists()) {
                throw AssignmentException::conflictingSchedule();
            }
        }

        // Check class conflicts
        if (isset($data['class_model_id'])) {
            $classConflict = Assignement::where('class_model_id', $data['class_model_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where(function ($query) use ($data) {
                    $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                          ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                          ->orWhere(function ($q) use ($data) {
                              $q->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                          });
                });

            if ($excludeId) {
                $classConflict->where('id', '!=', $excludeId);
            }

            if ($classConflict->exists()) {
                throw AssignmentException::conflictingSchedule();
            }
        }
    }
}