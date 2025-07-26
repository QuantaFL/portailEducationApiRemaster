<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\RoleModel;

class RoleModelSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'teacher','student','parent', ];

        foreach ($roles as $role) {
            RoleModel::firstOrCreate(['name' => $role]);
        }
    }
}

