<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Session;
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

            'session_id' => Session::factory(),
        ];
    }
}
