<?php

namespace Database\Seeders;

use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::create([
            'name' => 'Mathematics',
            'level' => 'Elementary',
            'coefficient' => 5,
        ]);

        Subject::create([
            'name' => 'Physics',
            'level' => 'High School',
            'coefficient' => 4,
        ]);
    }
}