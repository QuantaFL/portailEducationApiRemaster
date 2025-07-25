<?php

namespace Database\Factories;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Session\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ClassModelFactory extends Factory
{
    protected $model = ClassModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'level' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'session_id' => AcademicYear::factory(),
        ];
    }
}
