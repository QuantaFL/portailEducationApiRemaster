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
        $teacher = Teacher::create([
            'hire_date' => '2020-09-01',
            'user_model_id' => 2,
        ]);

        // Récupérer quelques matières existantes
        $subjects = Subject::inRandomOrder()->limit(3)->get();

        // Attacher les matières à l'enseignant
        $teacher->subjects()->attach($subjects->pluck('id'));
    }
}