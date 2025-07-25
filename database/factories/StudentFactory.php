<?php

namespace Database\Factories;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Parent;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'matricule' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'class_model_id' => ClassModel::factory(),
            'parent_id' => Parent::factory(),
        ];
    }
}
