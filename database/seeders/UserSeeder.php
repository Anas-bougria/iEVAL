<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Admin ----------
        User::updateOrCreate(['email' => 'admin@ieval.local'], [
            'name'      => 'Mohamed El Amrani',
            'matricule' => 'ADM-001',
            'role'      => 'admin',
            'password'  => Hash::make('password'),
            'is_active' => true,
        ]);

        // ---------- Teachers ----------
        $teachers = [
            ['name' => 'Hamid Alhaiane',     'email' => 'h.alhaiane@ieval.local',   'matricule' => 'PROF-001'],
            ['name' => 'Salma Benali',       'email' => 'prof@ieval.local',         'matricule' => 'PROF-002'],
            ['name' => 'Karim Tazi',         'email' => 'k.tazi@ieval.local',       'matricule' => 'PROF-003'],
            ['name' => 'Nadia El Fassi',     'email' => 'n.elfassi@ieval.local',    'matricule' => 'PROF-004'],
        ];
        foreach ($teachers as $t) {
            User::updateOrCreate(['email' => $t['email']], [
                'name'      => $t['name'],
                'matricule' => $t['matricule'],
                'role'      => 'teacher',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // ---------- Students ----------
        $students = [
            ['Yassine Berrada',     'etudiant@ieval.local'],
            ['Sara Lahlou',         's.lahlou@ieval.local'],
            ['Omar Ait Bella',      'o.aitbella@ieval.local'],
            ['Imane Cherkaoui',     'i.cherkaoui@ieval.local'],
            ['Mehdi Slaoui',        'm.slaoui@ieval.local'],
            ['Aya Benjelloun',      'a.benjelloun@ieval.local'],
            ['Hamza Idrissi',       'h.idrissi@ieval.local'],
            ['Lina Ouazzani',       'l.ouazzani@ieval.local'],
            ['Adam Bouzoubaa',      'a.bouzoubaa@ieval.local'],
            ['Houda Naciri',        'h.naciri@ieval.local'],
            ['Reda Chraibi',        'r.chraibi@ieval.local'],
            ['Salma Drissi',        's.drissi@ieval.local'],
            ['Youssef Hassani',     'y.hassani@ieval.local'],
            ['Maryam Squalli',      'm.squalli@ieval.local'],
            ['Anas El Mansouri',    'a.elmansouri@ieval.local'],
        ];
        foreach ($students as $i => [$name, $email]) {
            User::updateOrCreate(['email' => $email], [
                'name'      => $name,
                'matricule' => 'BTS-2026-' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT),
                'role'      => 'student',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);
        }
    }
}
