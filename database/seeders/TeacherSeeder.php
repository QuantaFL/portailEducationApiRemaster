<?php

namespace Database\Seeders;

use App\Modules\Teacher\Models\Teacher;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subjects IDs only to optimize memory usage
        $subjectIds = Subject::pluck('id')->toArray();
        
        for ($i = 2; $i <= 11; $i++) {
            // Create teacher without loading relations
            $teacher = Teacher::create([
                'hire_date' => now()->subYears(rand(1, 10))->format('Y-m-d'),
                'user_model_id' => $i,
            ]);
            
            // Attach random subjects (2 to 4 subjects max)
            $randomSubjectIds = collect($subjectIds)->random(rand(2, min(4, count($subjectIds))))->toArray();
            $teacher->subjects()->attach($randomSubjectIds);
            
            // Clear memory
            unset($teacher);
        }
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}
