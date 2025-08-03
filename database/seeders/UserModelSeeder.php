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
        $otherNationalities = [
            'Mauritanien', 'Malien', 'Guinéen', 'Congolais', 'Ivoirien',
            'Burkinabé', 'Togolais', 'Béninois', 'Nigérien', 'Gambien'
        ];

        $randomNationality = function () use ($otherNationalities) {
            return rand(1, 100) <= 80 ? 'Sénégalais' : $otherNationalities[array_rand($otherNationalities)];
        };

        // Create Admin User
        UserModel::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'birthday' => '1980-01-01',
            'adress' => 'Dakar, Senegal',
            'phone' => '771234567',
            'gender' => 'M',
            'nationality' => $randomNationality(),
        ]);

        // Create Teachers (role_id = 2) - They are NOT parents
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

        foreach ($teacherData as $i => $teacher) {
            UserModel::create([
                'first_name' => $teacher['first_name'],
                'last_name' => $teacher['last_name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role_id' => 2,
                'birthday' => '198' . ($i % 9) . '-0' . (($i % 9) + 1) . '-1' . (($i % 9) + 1),
                'adress' => 'Dakar, Senegal',
                'phone' => '78' . str_pad((1000000 + $i), 7, '0', STR_PAD_LEFT),
                'gender' => $teacher['gender'],
                'nationality' => $randomNationality(),
            ]);
        }

        // Create Family Units - Each family has 1-2 parents and 1-4 children
        $familyLastNames = [
            'Diallo', 'Sarr', 'Mbaye', 'Ndour', 'Diedhiou', 'Dramé', 'Keita', 'Touré',
            'Sy', 'Traoré', 'Diakhate', 'Gassama', 'Kouyaté', 'Balde', 'Sylla',
            'Senghor', 'Fofana', 'Kane', 'Diouf', 'Wade', 'Samb', 'Lo', 'Seye',
            'Niang', 'Coly', 'Manga', 'Diagne', 'Tine', 'Ly', 'Mbodj'
        ];

        $parentFirstNames = [
            'male' => ['Mamadou', 'Ousmane', 'Ibrahima', 'Demba', 'Lamine', 'Aliou', 'Youssou', 'Samba', 'Abdou', 'Babacar'],
            'female' => ['Aissatou', 'Fatou', 'Khady', 'Aminata', 'Mariama', 'Ndeye', 'Sokhna', 'Coumba', 'Maimouna', 'Binta']
        ];

        $studentFirstNames = [
            'male' => ['Omar', 'Pape', 'Modou', 'Cheikh', 'Oumar', 'Moussa', 'Alassane', 'Momar', 'Serigne', 'Abdoulaye'],
            'female' => ['Fatoumata', 'Adama', 'Awa', 'Mame', 'Astou', 'Yacine', 'Rokhaya', 'Mbissine', 'Fatima', 'Amina']
        ];

        $familyIndex = 0;

        // Create 30 families with proper relationships
        foreach ($familyLastNames as $familyName) {
            if ($familyIndex >= 30) break;

            $numParents = rand(1, 2); // 1 or 2 parents per family
            $numChildren = rand(1, 4); // 1-4 children per family

            // Create parents for this family
            for ($p = 0; $p < $numParents; $p++) {
                $gender = ($p === 0) ? (rand(0, 1) ? 'M' : 'F') : ($p === 1 ? 'F' : 'M'); // Ensure diversity
                $firstName = $parentFirstNames[$gender === 'M' ? 'male' : 'female'][array_rand($parentFirstNames[$gender === 'M' ? 'male' : 'female'])];

                UserModel::create([
                    'first_name' => $firstName,
                    'last_name' => $familyName,
                    'email' => 'parent_' . strtolower($familyName) . '_' . ($p + 1) . '@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => 4, // Parent role
                    'birthday' => '197' . rand(0, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'adress' => 'Dakar, Senegal',
                    'phone' => '76' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                    'gender' => $gender,
                    'nationality' => $randomNationality(),
                ]);
            }

            // Create children for this family (same last name as parents)
            for ($c = 0; $c < $numChildren; $c++) {
                $gender = rand(0, 1) ? 'M' : 'F';
                $firstName = $studentFirstNames[$gender === 'M' ? 'male' : 'female'][array_rand($studentFirstNames[$gender === 'M' ? 'male' : 'female'])];

                UserModel::create([
                    'first_name' => $firstName,
                    'last_name' => $familyName, // Same family name as parents
                    'email' => 'student_' . strtolower($familyName) . '_' . ($c + 1) . '@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => 3, // Student role
                    'birthday' => '200' . rand(5, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'adress' => 'Dakar, Senegal',
                    'phone' => '77' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                    'gender' => $gender,
                    'nationality' => $randomNationality(),
                ]);
            }

            $familyIndex++;
        }

        $this->command->info('Created proper family relationships with ' . $familyIndex . ' families');
    }
}
