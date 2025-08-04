<?php

namespace App\Console\Commands;

use App\Modules\Teacher\Models\Teacher;
use Illuminate\Console\Command;

class GenerateTeacherMatricules extends Command
{
    // appel :php artisan teachers:generate-matricules
    protected $signature = 'teachers:generate-matricules';

    protected $description = 'Generate matricules for existing teachers who don\'t have one';

    public function handle()
    {
        $this->info('Starting to generate matricules for existing teachers...');

        // Get all teachers without matricule (without relations for memory optimization)
        $teachersWithoutMatricule = Teacher::select('id', 'teacher_matricule')->whereNull('teacher_matricule')->get();

        if ($teachersWithoutMatricule->isEmpty()) {
            $this->info('All teachers already have matricules!');
            return 0;
        }

        $this->info("Found {$teachersWithoutMatricule->count()} teachers without matricule.");

        $bar = $this->output->createProgressBar($teachersWithoutMatricule->count());
        $bar->start();

        foreach ($teachersWithoutMatricule as $teacher) {
            $matricule = Teacher::generateMatricule();
            $teacher->teacher_matricule = $matricule;
            $teacher->save();

            $this->line("Generated matricule {$matricule} for teacher ID {$teacher->id}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Matricules generated successfully for all teachers!');

        return 0;
    }
}
