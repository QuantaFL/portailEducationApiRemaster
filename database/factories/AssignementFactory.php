<?php

namespace Database\Factories;

use App\Models\Assignement;
use App\Models\ClassModel;
use App\Models\Session;
use App\Models\Subject;
use App\modules\teacher\models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssignementFactory extends Factory
{
    protected $model = Assignement::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'teacher_id' => Teacher::factory(),
            'class_model_id' => ClassModel::factory(),
            'subject_id' => Subject::factory(),
            'session_id' => Session::factory(),
        ];
    }
}
