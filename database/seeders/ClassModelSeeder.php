<?php

namespace Database\Seeders;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = Teacher::first(); // Get the first teacher

        // Collège
        ClassModel::create([
            'name' => '6e',
            'level' => 'Collège',
        ]);
        ClassModel::create([
            'name' => '5e',
            'level' => 'Collège',
        ]);
        ClassModel::create([
            'name' => '4e',
            'level' => 'Collège',
        ]);
        ClassModel::create([
            'name' => '3e',
            'level' => 'Collège',
        ]);

        // Lycée
        ClassModel::create([
            'name' => 'Seconde L',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Seconde S',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Première L',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Première S',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Terminale L',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Terminale S1',
            'level' => 'Lycée',
        ]);
        ClassModel::create([
            'name' => 'Terminale S2',
            'level' => 'Lycée'
        ]);
    }
}
