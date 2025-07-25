<?php

namespace Database\Factories;

use App\Modules\ReportCard\Models\ReportCard;
use App\Modules\Student\Models\Student;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReportCardFactory extends Factory
{
    protected $model = ReportCard::class;

    public function definition(): array
    {
        return [
            'average_grade' => $this->faker->word(),
            'honors' => $this->faker->word(),
            'path' => $this->faker->word(),
            'rank' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'student_id' => Student::factory(),
            'term_id' => Term::factory(),
        ];
    }
}
