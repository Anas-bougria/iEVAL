<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        Semester::updateOrCreate(['code' => 'S1-2025'], [
            'name' => 'Semestre 1 · 2025-2026',
            'start_date' => '2025-09-15',
            'end_date'   => '2026-01-31',
            'is_current' => true,
        ]);

        Semester::updateOrCreate(['code' => 'S2-2025'], [
            'name' => 'Semestre 2 · 2025-2026',
            'start_date' => '2026-02-01',
            'end_date'   => '2026-06-30',
            'is_current' => false,
        ]);
    }
}
