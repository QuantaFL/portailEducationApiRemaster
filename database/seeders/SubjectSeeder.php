<?php

namespace Database\Seeders;

use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Collège subjects (6e to 3e)
        $collegeSubjects = [
            ['name' => 'Français', 'coefficient' => 4],
            ['name' => 'Mathématiques', 'coefficient' => 5],
            ['name' => 'Histoire-Géographie', 'coefficient' => 3],
            ['name' => 'Anglais', 'coefficient' => 2],
            ['name' => 'Sciences de la Vie et de la Terre', 'coefficient' => 3],
            ['name' => 'Physique-Chimie', 'coefficient' => 2],
            ['name' => 'Éducation Civique', 'coefficient' => 1],
            ['name' => 'Espagnol', 'coefficient' => 1],
            ['name' => 'Arabe', 'coefficient' => 1],
            ['name' => 'Informatique', 'coefficient' => 1],
        ];
        foreach ($collegeSubjects as $subject) {
            Subject::create([
                'name' => $subject['name'],
                'level' => 'Collège',
                'coefficient' => $subject['coefficient'],
            ]);
        }

        // Lycée S (S1, S2, S3, S4)
        $lyceeSSubjects = [
            ['name' => 'Mathématiques', 'coefficient' => 7],
            ['name' => 'Physique-Chimie', 'coefficient' => 6],
            ['name' => 'Sciences de la Vie et de la Terre', 'coefficient' => 5],
            ['name' => 'Philosophie', 'coefficient' => 2],
            ['name' => 'Français', 'coefficient' => 3],
            ['name' => 'Anglais', 'coefficient' => 2],
            ['name' => 'Histoire-Géographie', 'coefficient' => 2],
            ['name' => 'Espagnol', 'coefficient' => 1],
            ['name' => 'Arabe', 'coefficient' => 1],
            ['name' => 'Informatique', 'coefficient' => 2],
        ];
        $lyceeSVariants = ['Seconde S', 'Première S1', 'Première S2', 'Première S3', 'Première S4', 'Terminale S1', 'Terminale S2', 'Terminale S3', 'Terminale S4'];
        foreach ($lyceeSVariants as $variant) {
            foreach ($lyceeSSubjects as $subject) {
                Subject::create([
                    'name' => $subject['name'],
                    'level' => 'Lycée S',
                    'coefficient' => $subject['coefficient'],
                ]);
            }
        }

        $lyceeLSubjects = [
            ['name' => 'Philosophie', 'coefficient' => 7],
            ['name' => 'Français', 'coefficient' => 5],
            ['name' => 'Histoire-Géographie', 'coefficient' => 4],
            ['name' => 'Anglais', 'coefficient' => 3],
            ['name' => 'Espagnol', 'coefficient' => 2],
            ['name' => 'Arabe', 'coefficient' => 2],
            ['name' => 'Mathématiques', 'coefficient' => 2],
            ['name' => 'Informatique', 'coefficient' => 1],
        ];
        $lyceeLVariants = ['Seconde L', 'Première L1', 'Première L2', 'Terminale L1', 'Terminale L2'];
        foreach ($lyceeLVariants as $variant) {
            foreach ($lyceeLSubjects as $subject) {
                Subject::create([
                    'name' => $subject['name'],
                    'level' => 'Lycée L',
                    'coefficient' => $subject['coefficient'],
                ]);
            }
        }
    }
}
