<?php

namespace Database\Seeders;

use App\Modules\Term\Models\Term;
use App\Modules\AcademicYear\Models\AcademicYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentAcademicYear = AcademicYear::where('status', 'en_cours')->first();

        if (!$currentAcademicYear) {
            $this->command->info('Skipping TermSeeder: No active academic year found.');
            return;
        }

        Term::create([
            'name' => 'Semestre 1',
            'academic_year_id' => $currentAcademicYear->id,
            'start_date' => $currentAcademicYear->start_date,
            'end_date' => date('Y-m-d', strtotime($currentAcademicYear->start_date . ' +4 months')),
        ]);

        Term::create([
            'name' => 'Semestre 2',
            'academic_year_id' => $currentAcademicYear->id,
            'start_date' => date('Y-m-d', strtotime($currentAcademicYear->start_date . ' +5 months')),
            'end_date' => $currentAcademicYear->end_date,
        ]);
    }
}
