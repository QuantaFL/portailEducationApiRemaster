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
        $studentUsers = \App\Modules\User\Models\UserModel::where('role_id', 3)->get();
        $parentModels = \App\Modules\Parent\Models\ParentModel::all();

        $studentIndex = 0;
        foreach ($classModels as $classModel) {
            for ($i = 0; $i < 5; $i++) {
                $studentUser = $studentUsers[$studentIndex % $studentUsers->count()];
                $parentModel = $parentModels[$studentIndex % $parentModels->count()];
                Student::create([
                    'matricule' => 'STU' . str_pad($studentUser->id, 3, '0', STR_PAD_LEFT),
                    'academic_records' => 'Good',
                    'parent_model_id' => $parentModel->id,
                    'user_model_id' => $studentUser->id,
                ]);
                $studentIndex++;
            }
        }
    }
}
