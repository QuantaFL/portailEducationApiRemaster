<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\AcademicYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assurez-vous qu'une seule année est 'en_cours'
        AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-05-30',
            'status' => 'en_cours',
        ]);

        AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-05-30',
            'status' => 'termine',
        ]);
    }
}
