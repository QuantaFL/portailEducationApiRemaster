<?php

namespace Database\Seeders;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ClassModel::all();
        $teachers = Teacher::all();
        $terms = Term::all();

        if ($classes->isEmpty() || $teachers->isEmpty() || $terms->isEmpty()) {
            $this->command->info('Skipping AssignementSeeder: Not enough data in Classes, Teachers, or Terms.');
            return;
        }

        foreach ($classes as $class) {
            $subjectQuery = Subject::query();
            if (str_starts_with($class->level, 'Lycée')) {
                if (str_contains($class->name, 'S')) {
                    $subjectQuery->where('level', 'Lycée S');
                } elseif (str_contains($class->name, 'L')) {
                    $subjectQuery->where('level', 'Lycée L');
                } else {
                    $subjectQuery->where('level', $class->level);
                }
            } else {
                $subjectQuery->where('level', $class->level);
            }
            $subjects = $subjectQuery->get(); // Get all subjects for the class level

            if ($subjects->isEmpty()) {
                $this->command->info('No subjects found for class level: ' . $class->level);
                continue;
            }

            foreach ($terms as $term) {
                foreach ($subjects as $subject) {
                    // Assigner un enseignant aléatoire
                    $teacher = $teachers->random();

                    Assignement::create([
                        'teacher_id' => $teacher->id,
                        'class_model_id' => $class->id,
                        'subject_id' => $subject->id,
                        'term_id' => $term->id,
                    ]);
                }
            }
        }
        $this->command->info('Assignments seeded successfully.');
    }
}
