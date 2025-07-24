<?php

namespace Database\Factories;

use App\Models\Assignement;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'mark' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'assignement_id' => Assignement::factory(),
            'student_id' => Student::factory(),
        ];
    }
}
