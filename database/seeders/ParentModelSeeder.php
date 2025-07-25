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
        ParentModel::create([
            'user_model_id' => 1,
        ]);
    }
}