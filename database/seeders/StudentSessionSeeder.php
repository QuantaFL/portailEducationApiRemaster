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
        $students = Student::with('userModel')->get();
        $academicYears = AcademicYear::find(1);
        $profileFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('profiles');
        foreach ($students as $student) {
            $user = $student->userModel;
            if ($user && $user->gender && $user->birthday) {
                $age = null;
                try {
                    $age = \Carbon\Carbon::parse($user->birthday)->age;
                } catch (\Exception $e) {
                    $age = null;
                }
                $isYoung = $age !== null && $age < 15;
                $gender = strtolower($user->gender) === 'f' ? 'girl' : 'boy';
                $type = $isYoung ? 'young' : 'old';
                $matches = array_filter($profileFiles, function ($file) use ($gender, $type) {
                    return strpos($file, "student_{$gender}_{$type}") !== false;
                });
                $matches = array_values($matches);
            // Fallback: if no match for young/old, get any for gender
            if (count($matches) === 0) {
                $matches = array_filter($profileFiles, function ($file) use ($gender) {
                    return strpos($file, "student_{$gender}") !== false;
                });
                $matches = array_values($matches);
            }
            // Final fallback: assign any available image
            if (count($matches) === 0 && count($profileFiles) > 0) {
                $matches = $profileFiles;
            }
            if (count($matches) > 0) {
                    $index = $user->id % count($matches);
                    $file = $matches[$index];
                    $user->profile_picture_url = asset('storage/' . ltrim(str_replace('public/', '', $file), '/'));
                    $user->save();
                }
            }
            StudentSession::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYears->id,
                'class_model_id' => rand(1, 10),
            ]);
        }
    }
}
