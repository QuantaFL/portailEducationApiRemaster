<?php

namespace Database\Seeders;

use App\Modules\Parent\Models\ParentModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 60; $i++) { // Create 60 parent models
            ParentModel::create([
                'user_model_id' => $i + 3, // Parent user_model_ids start from 3
            ]);
        }
    }
}