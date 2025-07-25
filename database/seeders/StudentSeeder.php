<?php

namespace Database\Seeders;

use App\Modules\Student\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::create([
            'matricule' => 'STU001',
            'academic_records' => 'Good',
            'class_model_id' => 1,
            'parent_model_id' => 1,
            'user_model_id' => 2,
        ]);
    }
}