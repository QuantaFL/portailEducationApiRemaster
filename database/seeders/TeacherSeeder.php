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
    $subjects = Subject::all();
        for ($i = 2; $i <= 11; $i++) {
            $teacher = Teacher::create([
                'hire_date' => now()->subYears(rand(1, 10))->format('Y-m-d'),
                'user_model_id' => $i,
            ]);
            $teacherSubjects = $subjects->random(rand(2, min(4, $subjects->count())));
            $teacher->subjects()->attach($teacherSubjects->pluck('id'));
        }
    }
}
