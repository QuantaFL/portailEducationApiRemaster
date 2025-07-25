<?php

namespace Database\Seeders;

use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::create([
            'hire_date' => '2020-09-01',
            'user_model_id' => 3,
        ]);
    }
}