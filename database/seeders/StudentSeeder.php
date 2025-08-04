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

        if ($classModels->isEmpty()) {
            $this->command->error('No classes found. Make sure ClassModelSeeder runs first.');
            return;
        }

        // Retrieve family relationships from cache
        $familyRelationships = cache('family_relationships');

        if (!$familyRelationships) {
            $this->command->error('No family relationships found. Make sure UserModelSeeder runs first.');
            return;
        }

        $allStudentIds = [];
        foreach ($familyRelationships as $familyId => $family) {
            $allStudentIds = array_merge($allStudentIds, $family['children']);
        }

        if (empty($allStudentIds)) {
            $this->command->error('No student IDs found in family relationships.');
            return;
        }

        $studentsPerClass = ceil(count($allStudentIds) / $classModels->count());
        $studentIndex = 0;

        foreach ($classModels as $classModel) {
            $studentsInThisClass = 0;

            while ($studentsInThisClass < $studentsPerClass && $studentIndex < count($allStudentIds)) {
                $studentUserId = $allStudentIds[$studentIndex];
                $studentUser = UserModel::find($studentUserId);

                if (!$studentUser) {
                    $this->command->warn("Student user not found for ID: {$studentUserId}");
                    $studentIndex++;
                    continue;
                }

                // Find the correct parent for this student using family relationships
                $parentUserId = null;
                foreach ($familyRelationships as $familyId => $family) {
                    if (in_array($studentUserId, $family['children'])) {
                        // Get the first parent from this family
                        $parentUserId = $family['parents'][0] ?? null;
                        break;
                    }
                }

                if (!$parentUserId) {
                    $this->command->warn("No parent found for student {$studentUser->first_name} {$studentUser->last_name}");
                    $studentIndex++;
                    continue;
                }

                $parentModel = ParentModel::where('user_model_id', $parentUserId)->first();

                if (!$parentModel) {
                    $this->command->warn("No parent model found for parent user ID: {$parentUserId}");
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

        $this->command->info('Created students with proper family relationships using cached data');
    }
}
