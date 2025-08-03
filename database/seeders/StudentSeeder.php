<?php

namespace Database\Seeders;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Student\Models\Student;
use App\Modules\User\Models\UserModel;
use App\Modules\Parent\Models\ParentModel;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classModels = ClassModel::all();
        $studentUsers = UserModel::where('role_id', 3)->get(); // Students

        if ($classModels->isEmpty() || $studentUsers->isEmpty()) {
            $this->command->error('No classes or student users found. Make sure previous seeders run first.');
            return;
        }

        $studentsPerClass = ceil($studentUsers->count() / $classModels->count());
        $studentIndex = 0;

        foreach ($classModels as $classModel) {
            $studentsInThisClass = 0;

            while ($studentsInThisClass < $studentsPerClass && $studentIndex < $studentUsers->count()) {
                $studentUser = $studentUsers[$studentIndex];

                // Find parent with same last name (family relationship)
                $parentUser = UserModel::where('role_id', 4)
                    ->where('last_name', $studentUser->last_name)
                    ->first();

                if (!$parentUser) {
                    $this->command->warn("No parent found for student {$studentUser->first_name} {$studentUser->last_name}");
                    $studentIndex++;
                    continue;
                }

                $parentModel = ParentModel::where('user_model_id', $parentUser->id)->first();

                if (!$parentModel) {
                    $this->command->warn("No parent model found for parent user {$parentUser->first_name} {$parentUser->last_name}");
                    $studentIndex++;
                    continue;
                }

                Student::create([
                    'matricule' => 'STU' . str_pad($studentUser->id, 4, '0', STR_PAD_LEFT),
                    'academic_records' => 'Good',
                    'parent_model_id' => $parentModel->id,
                    'user_model_id' => $studentUser->id,
                ]);

                $studentIndex++;
                $studentsInThisClass++;
            }
        }

        $this->command->info('Created students with proper family relationships');
    }
}
