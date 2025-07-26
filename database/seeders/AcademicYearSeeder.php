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
            'label' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2026-06-30',
            'status' => 'en_cours',
        ]);

        AcademicYear::create([
            'label' => '2023-2024',
            'start_date' => '2023-09-01',
            'end_date' => '2024-06-30',
            'status' => 'termine',
        ]);
    }
}
