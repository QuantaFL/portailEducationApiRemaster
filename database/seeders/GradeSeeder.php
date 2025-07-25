<?php

namespace Database\Seeders;

use App\Modules\Grade\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grade::create([
            'mark' => 15.5,
            'type' => 'quiz',
            'assignement_id' => 1,
            'student_session_id' => 1,
            'term_id' => 1,
        ]);

        Grade::create([
            'mark' => 18.0,
            'type' => 'exam',
            'assignement_id' => 1,
            'student_session_id' => 1,
            'term_id' => 1,
        ]);
    }
}