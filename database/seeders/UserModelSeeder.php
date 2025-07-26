<?php

namespace Database\Seeders;

use App\Modules\User\Models\UserModel;
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
            'role_id' => 1,
            'birthday' => '2000-01-01',
            'adress' => 'Dakar, Senegal',
            'phone' => '771234567',
            'gender' => 'M',
        ]);

        UserModel::create([
            'first_name' => 'Fatou',
            'last_name' => 'Diop',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'birthday' => '1990-10-10',
            'adress' => 'Thies, Senegal',
            'phone' => '779876543',
            'gender' => 'F',
        ]);

        $parentFirstNames = ['Aissatou', 'Moussa', 'Fatou', 'Oumar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba', 'Lamine', 'Adama', 'Maimouna', 'Aliou'];
        $parentLastNames = ['Diallo', 'Ba', 'Faye', 'Gueye', 'Ndiaye', 'Diop', 'Sow', 'Thiam', 'Fall', 'Cisse', 'Diagne', 'Mbaye', 'Sarr', 'Ndour', 'Diedhiou', 'Camara', 'Dramé', 'Keita', 'Touré', 'Sy'];

        for ($i = 0; $i < 60; $i++) {
            UserModel::create([
                'first_name' => $parentFirstNames[$i % count($parentFirstNames)],
                'last_name' => $parentLastNames[$i % count($parentLastNames)],
                'email' => 'parent' . ($i + 1) . '@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3, // Assuming role_id 4 is for parent
                'birthday' => '197' . ($i % 9) . '-0' . (($i % 11) + 1) . '-1' . (($i % 9) + 1),
                'adress' => 'Dakar, Senegal',
                'phone' => '77' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'gender' => (rand(0, 1) == 0) ? 'M' : 'F',
            ]);
        }

        $studentFirstNames = ['Mamadou', 'Aissatou', 'Omar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Fatou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba', 'Lamine', 'Adama', 'Maimouna', 'Aliou'];
        $studentLastNames = ['Diallo', 'Ba', 'Faye', 'Gueye', 'Ndiaye', 'Diop', 'Sow', 'Thiam', 'Fall', 'Cisse', 'Diagne', 'Mbaye', 'Sarr', 'Ndour', 'Diedhiou', 'Camara', 'Dramé', 'Keita', 'Touré', 'Sy'];

        for ($i = 0; $i < 60; $i++) {
            UserModel::create([
                'first_name' => $studentFirstNames[$i % count($studentFirstNames)],
                'last_name' => $studentLastNames[$i % count($studentLastNames)],
                'email' => 'student' . ($i + 1) . '@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'birthday' => '200' . ($i % 9) . '-0' . (($i % 11) + 1) . '-1' . (($i % 9) + 1),
                'adress' => 'Dakar, Senegal',
                'phone' => '77' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'gender' => (rand(0, 1) == 0) ? 'M' : 'F',
            ]);
        }
    }
}
