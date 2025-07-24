<?php

namespace Database\Factories;

use App\Models\UserModel;
use App\modules\teacher\models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'hire_date' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_model_id' => UserModel::factory(),
        ];
    }
}
