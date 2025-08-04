<?php

namespace Database\Seeders;

use App\Modules\JobOffer\Models\JobOffer;
use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobOfferSeeder extends Seeder
{
    public function run(): void
    {
        // Get some subjects and users for testing
        $subjects = Subject::limit(5)->get();
        $users = UserModel::limit(3)->get();

        if ($subjects->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please run SubjectSeeder and UserModelSeeder first');
            return;
        }

        $jobOffers = [
            [
                'title' => 'Professeur de Mathématiques - Niveau Collège',
                'description' => 'Nous recherchons un professeur de mathématiques passionné pour enseigner au niveau collège. Le candidat idéal aura une solide formation en mathématiques et une expérience dans l\'enseignement aux adolescents.',
                'requirements' => '- Master en Mathématiques ou équivalent\n- Expérience d\'enseignement minimum 2 ans\n- Excellentes compétences en communication\n- Patience et pédagogie avec les élèves\n- Maîtrise des outils numériques éducatifs',
                'location' => 'Paris, 15ème arrondissement',
                'employment_type' => 'full_time',
                'salary_min' => 35000,
                'salary_max' => 45000,
                'experience_level' => 'junior',
                'application_deadline' => now()->addDays(30),
                'contact_email' => 'recrutement@education-paris.fr',
                'contact_phone' => '01 23 45 67 89',
                'benefits' => 'Mutuelle, tickets restaurant, formation continue, vacances scolaires',
                'is_active' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Enseignant de Physique-Chimie - Lycée',
                'description' => 'Rejoignez notre équipe pédagogique dynamique en tant qu\'enseignant de physique-chimie pour les classes de lycée. Vous contribuerez à la réussite des élèves dans leurs parcours scientifiques.',
                'requirements' => '- Master en Physique, Chimie ou Sciences Physiques\n- CAPES ou équivalent souhaité\n- Expérience en laboratoire appréciée\n- Capacité à motiver les élèves\n- Esprit d\'équipe',
                'location' => 'Lyon, Centre-ville',
                'employment_type' => 'full_time',
                'salary_min' => 38000,
                'salary_max' => 50000,
                'experience_level' => 'senior',
                'application_deadline' => now()->addDays(25),
                'contact_email' => 'rh@lycee-lyon.edu',
                'contact_phone' => '04 78 90 12 34',
                'benefits' => 'Mutuelle familiale, prime annuelle, matériel pédagogique fourni',
                'is_active' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Professeur d\'Anglais - Temps Partiel',
                'description' => 'Poste à temps partiel pour l\'enseignement de l\'anglais tous niveaux. Idéal pour un enseignant souhaitant concilier vie professionnelle et personnelle.',
                'requirements' => '- Licence en Anglais ou Langues\n- Niveau natif ou bilingue\n- Expérience d\'enseignement souhaitée\n- Créativité dans les méthodes pédagogiques\n- Disponibilité matins ou après-midis',
                'location' => 'Marseille, 8ème arrondissement',
                'employment_type' => 'part_time',
                'salary_min' => 20000,
                'salary_max' => 28000,
                'experience_level' => 'entry',
                'application_deadline' => now()->addDays(20),
                'contact_email' => 'contact@college-marseille.fr',
                'benefits' => 'Horaires flexibles, formation linguistique continue',
                'is_active' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Formateur en Informatique - Mission Temporaire',
                'description' => 'Mission de 6 mois pour former les enseignants aux nouveaux outils numériques et accompagner la transition digitale de l\'établissement.',
                'requirements' => '- Formation supérieure en Informatique\n- Expertise en outils pédagogiques numériques\n- Compétences en formation d\'adultes\n- Maîtrise des plateformes e-learning\n- Adaptabilité et autonomie',
                'location' => 'Toulouse, Proche métro',
                'employment_type' => 'contract',
                'salary_min' => 3500,
                'salary_max' => 4500,
                'experience_level' => 'expert',
                'application_deadline' => now()->addDays(15),
                'contact_email' => 'innovation@lycee-toulouse.org',
                'contact_phone' => '05 61 23 45 67',
                'benefits' => 'Équipement informatique fourni, formation continue, mission stimulante',
                'is_active' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Professeur de Français - Remplacement',
                'description' => 'Remplacement d\'un professeur de français en congé maladie pour une durée de 3 mois minimum. Classes de 4ème et 3ème.',
                'requirements' => '- Master en Lettres Modernes\n- Expérience en collège exigée\n- Disponibilité immédiate\n- Maîtrise du programme de 4ème/3ème\n- Sens de l\'adaptation',
                'location' => 'Bordeaux, Quartier Chartrons',
                'employment_type' => 'contract',
                'salary_min' => 2800,
                'salary_max' => 3200,
                'experience_level' => 'junior',
                'application_deadline' => now()->addDays(7),
                'contact_email' => 'direction@college-bordeaux.fr',
                'benefits' => 'Prise de poste rapide, équipe bienveillante',
                'is_active' => false, // Cette offre est inactive pour tester
                'published_at' => null, // Brouillon
            ],
        ];

        foreach ($jobOffers as $index => $offerData) {
            // Assign a subject and posted_by user
            $offerData['subject_id'] = $subjects->random()->id;
            $offerData['posted_by'] = $users->random()->id;
            
            // Generate unique offer number
            $year = date('Y');
            $monthDay = date('md');
            $randomNumber = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $offerData['offer_number'] = "JOB-{$year}-{$monthDay}-{$randomNumber}";
            
            JobOffer::create($offerData);
        }

        $this->command->info('Job offers seeded successfully!');
    }
}