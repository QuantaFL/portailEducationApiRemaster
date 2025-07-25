<?php

namespace Database\Seeders;

use App\Modules\Grade\Models\Grade;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Assignement\Models\Assignement;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentSessions = StudentSession::with('classModel')->get();
        $terms = Term::all();

        if ($studentSessions->isEmpty() || $terms->isEmpty()) {
            $this->command->info('Skipping GradeSeeder: Not enough data in StudentSessions or Terms.');
            return;
        }

        foreach ($studentSessions as $studentSession) {
            foreach ($terms as $term) {
                // Récupérer les affectations (assignments) pour la classe de cette session et ce trimestre
                $assignments = Assignement::where('class_model_id', $studentSession->class_model_id)
                    ->where('term_id', $term->id)
                    ->get();

                if ($assignments->isEmpty()) {
                    $this->command->info("No assignments found for class {$studentSession->classModel->name} and term {$term->name}. Skipping grades for this session.");
                    continue;
                }

                foreach ($assignments as $assignment) {
                    // Créer une note d'examen
                    Grade::create([
                        'mark' => rand(100, 200) / 10, // Note entre 10.0 et 20.0
                        'type' => 'exam',
                        'assignement_id' => $assignment->id,
                        'student_session_id' => $studentSession->id,
                        'term_id' => $term->id,
                    ]);

                    // Créer une note de quiz
                    Grade::create([
                        'mark' => rand(50, 150) / 10, // Note entre 5.0 et 15.0
                        'type' => 'quiz',
                        'assignement_id' => $assignment->id,
                        'student_session_id' => $studentSession->id,
                        'term_id' => $term->id,
                    ]);
                }
            }
        }

        $this->command->info('Grades seeded successfully.');
    }
}
