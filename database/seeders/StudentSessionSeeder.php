<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentSession;
use Illuminate\Database\Seeder;

class StudentSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $academicYears = AcademicYear::find(1);
        foreach ($students as $student) {
                StudentSession::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYears->id,
                    'class_model_id' => rand(1, 10),
                ]);
        }
    }
}
