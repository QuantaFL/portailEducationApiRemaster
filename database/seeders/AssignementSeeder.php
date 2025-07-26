<?php

namespace Database\Seeders;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
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
        foreach ($classes as $class) {
            $subjectQuery = Subject::query();
            // For Lycee S and L, match both level and variant in class name
            if (str_starts_with($class->level, 'Lycée')) {
                if (str_contains($class->name, 'S')) {
                    $subjectQuery->where('level', 'Lycée S');
                } elseif (str_contains($class->name, 'L')) {
                    $subjectQuery->where('level', 'Lycée L');
                } else {
                    $subjectQuery->where('level', $class->level);
                }
            } else {
                // For Collège and others, just match level
                $subjectQuery->where('level', $class->level);
            }
            $subject = $subjectQuery->inRandomOrder()->first();
            if ($subject) {
                Assignement::create([
                    'teacher_id' => 1,
                    'class_model_id' => $class->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => 1,
                ]);
            }
        }
    }
}
