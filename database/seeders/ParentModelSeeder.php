<?php

namespace Database\Seeders;

use App\Modules\Parent\Models\ParentModel;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Seeder;

class ParentModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentUsers = UserModel::where('role_id', 4)->get();

        if ($parentUsers->isEmpty()) {
            $this->command->error('No parent users found. Make sure UserModelSeeder runs first.');
            return;
        }

        foreach ($parentUsers as $parentUser) {
            ParentModel::create([
                'user_model_id' => $parentUser->id,
            ]);
        }

        $this->command->info('Created ' . $parentUsers->count() . ' parent models linked to actual parent users');
    }
}
