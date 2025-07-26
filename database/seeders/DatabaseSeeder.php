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
        ]);
    }
}