<?php

namespace Database\Seeders;

use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassModel::create([
            'name' => 'Grade 1',
            'level' => 'Elementary',
        ]);

        ClassModel::create([
            'name' => 'Grade 2',
            'level' => 'Elementary',
        ]);
    }
}