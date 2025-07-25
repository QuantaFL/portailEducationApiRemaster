<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            ClassModelSeeder::class,
            SubjectSeeder::class,
            TermSeeder::class,
            UserModelSeeder::class,
            ParentModelSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            AssignementSeeder::class,
            StudentSessionSeeder::class,
            GradeSeeder::class,
        ]);
    }
}