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
        AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);

        AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
    }
}
