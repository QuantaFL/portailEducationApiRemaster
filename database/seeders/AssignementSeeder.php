<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignementSeeder extends Seeder
{
    private $teacherSchedule = [];
    private $classSchedule = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ClassModel::all();
        $teachers = Teacher::all();
        $academicYears = AcademicYear::all();

        if ($classes->isEmpty() || $teachers->isEmpty() || $academicYears->isEmpty()) {
            $this->command->error('Skipping AssignmentSeeder: Not enough data in Classes, Teachers, or Academic Years.');
            return;
        }

        $currentAcademicYear = $academicYears->first(); // Use current academic year only
        $dayTimes = $this->generateTimeSlots();

        DB::beginTransaction();

        try {
            foreach ($classes as $class) {
                $subjects = $this->getSubjectsForClass($class);

                if ($subjects->isEmpty()) {
                    $this->command->warn('No subjects found for class: ' . $class->name);
                    continue;
                }

                foreach ($subjects as $subject) {
                    $assignment = $this->createAssignmentWithoutConflicts(
                        $class,
                        $subject,
                        $teachers,
                        $currentAcademicYear,
                        $dayTimes
                    );

                    if ($assignment) {
                        // Load the relationship explicitly to avoid null issues
                        $assignment->load('teacher.userModel');

                        if ($assignment->teacher && $assignment->teacher->userModel) {
                            $this->command->info("Created assignment: {$assignment->teacher->userModel->first_name} teaching {$subject->name} to {$class->name}");
                        } else {
                            $this->command->warn("Assignment created but teacher relationship missing for {$subject->name} in {$class->name}");
                        }
                    }
                }
            }

            DB::commit();
            $this->command->info('Assignments seeded successfully with proper conflict resolution.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create assignments: ' . $e->getMessage());
        }
    }

    /**
     * Get subjects appropriate for the class level
     */
    private function getSubjectsForClass(ClassModel $class): \Illuminate\Database\Eloquent\Collection
    {
        $classSubjectMap = [
            '6e' => ['Collège'],
            '5e' => ['Collège'],
            '4e' => ['Collège'],
            '3e' => ['Collège'],
            'Seconde L' => ['Seconde L'],
            'Seconde S' => ['Seconde S'],
            'Première L' => ['Première L'],
            'Première S' => ['Première S'],
            'Terminale L' => ['Terminale L'],
            'Terminale S1' => ['Terminale S1'],
            'Terminale S2' => ['Terminale S2'],
        ];

        if (isset($classSubjectMap[$class->name])) {
            $subjectLevels = $classSubjectMap[$class->name];
            return Subject::whereIn('level', $subjectLevels)->get();
        }

        return collect();
    }

    /**
     * Generate realistic time slots for school schedule
     */
    private function generateTimeSlots(): array
    {
        return [
            ['start' => '08:00', 'end' => '08:50'], // 1st period
            ['start' => '09:00', 'end' => '09:50'], // 2nd period
            ['start' => '10:00', 'end' => '10:50'], // 3rd period
            ['start' => '11:00', 'end' => '11:50'], // 4th period
            ['start' => '14:00', 'end' => '14:50'], // 5th period (after lunch)
            ['start' => '15:00', 'end' => '15:50'], // 6th period
            ['start' => '16:00', 'end' => '16:50'], // 7th period
        ];
    }

    /**
     * Create assignment without schedule conflicts
     */
    private function createAssignmentWithoutConflicts(
        ClassModel $class,
        Subject $subject,
        $teachers,
        AcademicYear $academicYear,
        array $dayTimes
    ): ?Assignement {
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $maxAttempts = 50;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $teacher = $teachers->random();
            $day = $daysOfWeek[array_rand($daysOfWeek)];
            $timeSlot = $dayTimes[array_rand($dayTimes)];

            // Check for conflicts
            if ($this->hasScheduleConflict($teacher->id, $class->id, $day, $timeSlot)) {
                $attempts++;
                continue;
            }

            // Create assignment with proper day_of_week as array
            $assignment = Assignement::create([
                'teacher_id' => $teacher->id,
                'class_model_id' => $class->id,
                'subject_id' => $subject->id,
                'academic_year_id' => $academicYear->id,
                'day_of_week' => [$day], // Array format as required
                'start_time' => $timeSlot['start'],
                'end_time' => $timeSlot['end'],
                'coefficient' => round(rand(15, 30) / 10, 1), // Random coefficient 1.5-3.0
                'isActive' => true,
                'assignment_number' => $this->generateAssignmentNumber(),
            ]);

            // Track the schedule
            $this->trackSchedule($teacher->id, $class->id, $day, $timeSlot);

            return $assignment;
        }

        $this->command->warn("Could not find conflict-free slot for {$subject->name} in {$class->name} after {$maxAttempts} attempts");
        return null;
    }

    /**
     * Check if there's a schedule conflict
     */
    private function hasScheduleConflict(int $teacherId, int $classId, string $day, array $timeSlot): bool
    {
        // Check teacher schedule conflict
        $teacherKey = "{$teacherId}_{$day}_{$timeSlot['start']}_{$timeSlot['end']}";
        if (isset($this->teacherSchedule[$teacherKey])) {
            return true;
        }

        // Check class schedule conflict
        $classKey = "{$classId}_{$day}_{$timeSlot['start']}_{$timeSlot['end']}";
        if (isset($this->classSchedule[$classKey])) {
            return true;
        }

        // Check for overlapping times in database
        $conflicts = Assignement::where(function ($query) use ($teacherId, $classId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhere('class_model_id', $classId);
            })
            ->whereJsonContains('day_of_week', $day)
            ->where(function ($query) use ($timeSlot) {
                $query->where(function ($q) use ($timeSlot) {
                    $q->where('start_time', '<=', $timeSlot['start'])
                      ->where('end_time', '>', $timeSlot['start']);
                })->orWhere(function ($q) use ($timeSlot) {
                    $q->where('start_time', '<', $timeSlot['end'])
                      ->where('end_time', '>=', $timeSlot['end']);
                })->orWhere(function ($q) use ($timeSlot) {
                    $q->where('start_time', '>=', $timeSlot['start'])
                      ->where('end_time', '<=', $timeSlot['end']);
                });
            })
            ->exists();

        return $conflicts;
    }

    /**
     * Track schedule to prevent conflicts within this seeding session
     */
    private function trackSchedule(int $teacherId, int $classId, string $day, array $timeSlot): void
    {
        $teacherKey = "{$teacherId}_{$day}_{$timeSlot['start']}_{$timeSlot['end']}";
        $classKey = "{$classId}_{$day}_{$timeSlot['start']}_{$timeSlot['end']}";

        $this->teacherSchedule[$teacherKey] = true;
        $this->classSchedule[$classKey] = true;
    }

    /**
     * Generate unique assignment number
     */
    private function generateAssignmentNumber(): string
    {
        $year = date('Y');
        $monthDay = date('md');
        $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $assignmentNumber = "ASS-{$year}-{$monthDay}-{$randomNumber}";

        // Ensure uniqueness
        while (Assignement::where('assignment_number', $assignmentNumber)->exists()) {
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $assignmentNumber = "ASS-{$year}-{$monthDay}-{$randomNumber}";
        }

        return $assignmentNumber;
    }
}
