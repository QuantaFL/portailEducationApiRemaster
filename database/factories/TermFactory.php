<?php

namespace Database\Factories;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'session_id' => AcademicYear::factory(),
        ];
    }
}
