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

/**
 * Class AssignmentService
 *
 * Service pour la logique métier des affectations.
 */
class AssignmentService
{
    /**
     * Récupère toutes les affectations.
     *
     * @return Collection
     */
    public function getAllAssignments(): Collection
    {
        return Assignement::all();
    }

    /**
     * Récupère une affectation par son ID.
     *
     * @param int $id
     * @return Assignement
     * @throws AssignmentException
     */
    public function getAssignmentById(int $id): Assignement
    {
        $assignment = Assignement::find($id);

        if (!$assignment) {
            throw AssignmentException::assignmentNotFound();
        }

        return $assignment;
    }

    /**
     * Récupère les affectations d'un enseignant.
     *
     * @param int $teacherId
     * @return Collection
     * @throws AssignmentException
     */
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

    /**
     * Récupère les affectations par semestre et par classe.
     *
     * @param int $termId
     * @param int $classId
     * @return Collection
     * @throws AssignmentException
     */
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

    /**
     * Crée une nouvelle affectation.
     *
     * @param array $data
     * @return Assignement
     * @throws AssignmentException
     */
    public function createAssignment(array $data): Assignement
    {
        Log::info('AssignmentService: Création d\'une nouvelle affectation', [
            'teacher_id' => $data['teacher_id'] ?? null,
            'class_model_id' => $data['class_model_id'] ?? null,
            'subject_id' => $data['subject_id'] ?? null
        ]);

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

            // Generate unique assignment number
            $data['assignment_number'] = $this->generateAssignmentNumber();
            Log::info('AssignmentService: Numéro d\'affectation généré', ['assignment_number' => $data['assignment_number']]);

            // Set default values
            if (!isset($data['isActive'])) {
                $data['isActive'] = true;
            }

            $assignment = Assignement::create($data);

            Log::info('AssignmentService: Affectation créée avec succès', [
                'assignment_id' => $assignment->id,
                'assignment_number' => $assignment->assignment_number,
                'teacher_id' => $assignment->teacher_id,
                'subject_id' => $assignment->subject_id,
                'class_model_id' => $assignment->class_model_id
            ]);

            DB::commit();

            return $assignment;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AssignmentService: Échec de la création de l\'affectation', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw AssignmentException::creationFailed();
        }
    }

    /**
     * Met à jour une affectation.
     *
     * @param Assignement $assignment
     * @param array $data
     * @return Assignement
     * @throws AssignmentException
     */
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
            Log::error('Échec de la mise à jour de l\'affectation: ' . $e->getMessage());
            throw AssignmentException::updateFailed();
        }
    }

    /**
     * Supprime une affectation.
     *
     * @param Assignement $assignment
     * @return bool
     * @throws AssignmentException
     */
    public function deleteAssignment(Assignement $assignment): bool
    {
        try {
            return $assignment->delete();
        } catch (\Exception $e) {
            Log::error('Échec de la suppression de l\'affectation: ' . $e->getMessage());
            throw AssignmentException::deletionFailed();
        }
    }

    /**
     * Valide les données de l\'affectation.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return void
     * @throws AssignmentException
     */
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

    /**
     * Vérifie les affectations en double.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return void
     * @throws AssignmentException
     */
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

    /**
     * Vérifie les conflits d\'horaire.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return void
     * @throws AssignmentException
     */
    private function checkForScheduleConflicts(array $data, ?int $excludeId = null): void
    {
        if (!isset($data['day_of_week'], $data['start_time'], $data['end_time'])) {
            return;
        }

        $daysOfWeek = is_array($data['day_of_week']) ? $data['day_of_week'] : [$data['day_of_week']];

        foreach ($daysOfWeek as $day) {
            // Check teacher conflicts
            if (isset($data['teacher_id'])) {
                $teacherConflict = Assignement::where('teacher_id', $data['teacher_id'])
                    ->whereJsonContains('day_of_week', $day)
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
                    ->whereJsonContains('day_of_week', $day)
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

    /**
     * Génère un numéro d\'affectation unique.
     *
     * @return string
     */
    private function generateAssignmentNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;

            // Format: ASS-YYYY-MMDD-XXXX (ASS = Assignment, YYYY = year, MMDD = month+day, XXXX = random)
            $year = date('Y');
            $monthDay = date('md');
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $assignmentNumber = "ASS-{$year}-{$monthDay}-{$randomNumber}";

            // Check if this number already exists
            $exists = Assignement::where('assignment_number', $assignmentNumber)->exists();

            if (!$exists) {
                Log::info('AssignmentService: Numéro d\'affectation unique généré', [
                    'assignment_number' => $assignmentNumber,
                    'attempts' => $attempt
                ]);
                return $assignmentNumber;
            }

            Log::debug('AssignmentService: Le numéro d\'affectation généré existe déjà, nouvelle tentative', [
                'assignment_number' => $assignmentNumber,
                'attempt' => $attempt
            ]);

        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique number after max attempts, use timestamp-based approach
        $timestamp = time();
        $assignmentNumber = "ASS-{$year}-{$monthDay}-" . substr($timestamp, -4);

        Log::warning('AssignmentService: Utilisation d\'un numéro d\'affectation basé sur l\'horodatage après le nombre maximum de tentatives', [
            'assignment_number' => $assignmentNumber,
            'max_attempts_reached' => $maxAttempts
        ]);

        return $assignmentNumber;
    }

    /**
     * Crée une affectation pour un enseignant (utilisé par TeacherService).
     *
     * @param int $teacherId
     * @param int $subjectId
     * @param int $classModelId
     * @param array|null $additionalData
     * @return Assignement
     * @throws AssignmentException
     */
    public function createAssignmentForTeacher(int $teacherId, int $subjectId, int $classModelId, ?array $additionalData = []): Assignement
    {
        Log::info('AssignmentService: Création d\'une affectation pour un enseignant', [
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'class_model_id' => $classModelId
        ]);

        $data = array_merge([
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'class_model_id' => $classModelId,
            'isActive' => true,
        ], $additionalData);

        return $this->createAssignment($data);
    }

    /**
     * Active une affectation.
     *
     * @param Assignement $assignment
     * @return Assignement
     */
    public function activateAssignment(Assignement $assignment): Assignement
    {
        Log::info('AssignmentService: Activation de l\'affectation', [
            'assignment_id' => $assignment->id,
            'assignment_number' => $assignment->assignment_number
        ]);

        $assignment->update(['isActive' => true]);

        return $assignment->fresh();
    }

    /**
     * Désactive une affectation.
     *
     * @param Assignement $assignment
     * @return Assignement
     */
    public function deactivateAssignment(Assignement $assignment): Assignement
    {
        Log::info('AssignmentService: Désactivation de l\'affectation', [
            'assignment_id' => $assignment->id,
            'assignment_number' => $assignment->assignment_number
        ]);

        $assignment->update(['isActive' => false]);

        return $assignment->fresh();
    }
}

