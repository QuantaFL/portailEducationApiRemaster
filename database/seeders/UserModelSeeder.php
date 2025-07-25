<?php

namespace Database\Seeders;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserModel::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Assuming role_id 1 is for admin
            'birthday' => '2000-01-01',
            'adress' => '123 Admin St',
            'phone' => '111-222-3333',
        ]);

        UserModel::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Assuming role_id 2 is for student
            'birthday' => '2005-05-05',
            'adress' => '456 Student Ave',
            'phone' => '444-555-6666',
        ]);

        UserModel::create([
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3, // Assuming role_id 3 is for teacher
            'birthday' => '1990-10-10',
            'adress' => '789 Teacher Rd',
            'phone' => '777-888-9999',
        ]);
    }
}
