<?php

namespace Database\Seeders;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
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
            $subjects = collect();
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
                $subjects = Subject::whereIn('level', $subjectLevels)->get();
            }

            if ($subjects->isEmpty()) {
                $this->command->info('No subjects found for class level: ' . $class->level);
                continue;
            }

            foreach ($terms as $term) {
                foreach ($subjects as $subject) {
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
