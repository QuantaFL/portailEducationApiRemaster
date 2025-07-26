<?php

namespace Database\Seeders;

use App\Modules\ClassModel\Models\ClassModel;
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
        $classModels = ClassModel::all();
        $studentUserStartId = 63; // Assuming student user_model_ids start from 63
        $parentModelStartId = 1; // Assuming parent_model_ids start from 1

        foreach ($classModels as $classModel) {
            for ($i = 0; $i < 5; $i++) {
                Student::create([
                    'matricule' => 'STU' . str_pad($studentUserStartId, 3, '0', STR_PAD_LEFT),
                    'academic_records' => 'Good',
                    'parent_model_id' => $parentModelStartId,
                    'user_model_id' => $studentUserStartId,
                ]);
                $studentUserStartId++;
                $parentModelStartId++;
            }
        }
    }
}
