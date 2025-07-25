<?php

namespace Database\Seeders;

use App\Modules\Term\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Term::create([
            'name' => 'Term 1',
            'academic_year_id' => 1,
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-31',
        ]);

        Term::create([
            'name' => 'Term 2',
            'academic_year_id' => 1,
            'start_date' => '2026-02-01',
            'end_date' => '2026-06-30',
        ]);
    }
}
