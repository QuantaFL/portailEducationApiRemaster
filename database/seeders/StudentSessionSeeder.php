<?php

namespace Database\Seeders;

use App\Modules\Student\Models\StudentSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentSession::create([
            'student_id' => 1,
            'academic_year_id' => 1,
            'class_model_id' => 1,
        ]);
    }
}