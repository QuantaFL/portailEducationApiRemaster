<?php

namespace Database\Seeders;

use App\Modules\Teacher\Models\Teacher;
use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();

        // Get all teacher users (role_id = 2) instead of hard-coded IDs
        $teacherUsers = UserModel::where('role_id', 2)->get();

        if ($teacherUsers->isEmpty()) {
            $this->command->error('No teacher users found. Make sure UserModelSeeder runs first.');
            return;
        }

        if ($subjects->isEmpty()) {
            $this->command->error('No subjects found. Make sure SubjectSeeder runs first.');
            return;
        }

        foreach ($teacherUsers as $teacherUser) {
            $teacher = Teacher::create([
                'hire_date' => now()->subYears(rand(1, 10))->format('Y-m-d'),
                'user_model_id' => $teacherUser->id,
            ]);

            // Assign 2-4 random subjects to each teacher
            $teacherSubjects = $subjects->random(rand(2, min(4, $subjects->count())));
            $teacher->subjects()->attach($teacherSubjects->pluck('id'));
        }

        $this->command->info('Created ' . $teacherUsers->count() . ' teachers with subject assignments');
    }
}
