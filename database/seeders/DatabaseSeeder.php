<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleModelSeeder::class,
            AcademicYearSeeder::class,
            SubjectSeeder::class,
            TermSeeder::class,
            UserModelSeeder::class,
            ParentModelSeeder::class,
            TeacherSeeder::class,
            ClassModelSeeder::class,
            StudentSeeder::class,
            AssignementSeeder::class,
            StudentSessionSeeder::class,
            GradeSeeder::class,
        ]); }
}
