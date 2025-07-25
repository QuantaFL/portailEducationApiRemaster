<?php

namespace Database\Factories;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Parent;

class ParentModelFactory extends Factory
{
    protected $model = Parent::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_model_id' => UserModel::factory(),
        ];
    }
}
