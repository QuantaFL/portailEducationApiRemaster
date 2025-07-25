<?php

namespace Database\Seeders;

use App\Modules\Assignement\Models\Assignement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Assignement::create([
            'teacher_id' => 1,
            'class_model_id' => 1,
            'subject_id' => 1,
            'academic_year_id' => 1,
        ]);
    }
}