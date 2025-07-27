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

        $teacherData = [
            ['first_name' => 'Fatou', 'last_name' => 'Diop', 'email' => 'teacher@example.com', 'gender' => 'F'],
            ['first_name' => 'Moussa', 'last_name' => 'Ba', 'email' => 'teacher2@example.com', 'gender' => 'M'],
            ['first_name' => 'Aissatou', 'last_name' => 'Sow', 'email' => 'teacher3@example.com', 'gender' => 'F'],
            ['first_name' => 'Oumar', 'last_name' => 'Ndiaye', 'email' => 'teacher4@example.com', 'gender' => 'M'],
            ['first_name' => 'Khady', 'last_name' => 'Faye', 'email' => 'teacher5@example.com', 'gender' => 'F'],
            ['first_name' => 'Pape', 'last_name' => 'Gueye', 'email' => 'teacher6@example.com', 'gender' => 'M'],
            ['first_name' => 'Aminata', 'last_name' => 'Thiam', 'email' => 'teacher7@example.com', 'gender' => 'F'],
            ['first_name' => 'Modou', 'last_name' => 'Fall', 'email' => 'teacher8@example.com', 'gender' => 'M'],
            ['first_name' => 'Cheikh', 'last_name' => 'Cisse', 'email' => 'teacher9@example.com', 'gender' => 'M'],
            ['first_name' => 'Mariama', 'last_name' => 'Camara', 'email' => 'teacher10@example.com', 'gender' => 'F'],
        ];
        for ($i = 0; $i < 10; $i++) {
            UserModel::create([
                'first_name' => $teacherData[$i]['first_name'],
                'last_name' => $teacherData[$i]['last_name'],
                'email' => $teacherData[$i]['email'],
                'password' => Hash::make('password'),
                'role_id' => 2,
                'birthday' => '198' . ($i % 10) . '-0' . (($i % 9) + 1) . '-1' . (($i % 9) + 1),
                'adress' => 'Dakar, Senegal',
                'phone' => '77' . str_pad((1000000 + $i), 7, '0', STR_PAD_LEFT),
                'gender' => $teacherData[$i]['gender'],
            ]);
        }

        $parentFirstNames = ['Aissatou', 'Moussa', 'Fatou', 'Oumar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba', 'Lamine', 'Adama', 'Maimouna', 'Aliou', 'Youssou', 'Binta', 'Samba', 'Aissatou', 'Moussa', 'Fatoumata', 'Oumar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba'];
        $parentLastNames = ['Diallo', 'Ba', 'Faye', 'Gueye', 'Ndiaye', 'Diop', 'Sow', 'Thiam', 'Fall', 'Cisse', 'Diagne', 'Mbaye', 'Sarr', 'Ndour', 'Diedhiou', 'Camara', 'Dramé', 'Keita', 'Touré', 'Sy', 'Traoré', 'Diakhate', 'Sow', 'Gassama', 'Kouyaté', 'Balde', 'Sylla', 'Toure', 'Senghor', 'Fofana', 'Kane'];

        for ($i = 0; $i < 80; $i++) {
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

        $studentFirstNames = ['Mamadou', 'Aissatou', 'Omar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Fatou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba', 'Lamine', 'Adama', 'Maimouna', 'Aliou', 'Youssou', 'Binta', 'Samba', 'Aissatou', 'Moussa', 'Fatoumata', 'Oumar', 'Khady', 'Pape', 'Aminata', 'Modou', 'Cheikh', 'Mariama', 'Ousmane', 'Ndeye', 'Ibrahima', 'Sokhna', 'Demba', 'Coumba', 'Lamine', 'Adama'];
        $studentLastNames = ['Diallo', 'Ba', 'Faye', 'Gueye', 'Ndiaye', 'Diop', 'Sow', 'Thiam', 'Fall', 'Cisse', 'Diagne', 'Mbaye', 'Sarr', 'Ndour', 'Diedhiou', 'Camara', 'Dramé', 'Keita', 'Touré', 'Sy', 'Traoré', 'Diakhate', 'Sow', 'Gassama', 'Kouyaté', 'Balde', 'Sylla', 'Toure', 'Senghor', 'Fofana', 'Kane'];

        for ($i = 0; $i < 160; $i++) {
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
