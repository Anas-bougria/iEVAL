<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Module;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $s1 = Semester::where('code', 'S1-2025')->first();
        $s2 = Semester::where('code', 'S2-2025')->first();

        $teachers = User::where('role', 'teacher')->get()->keyBy('matricule');
        $students = User::where('role', 'student')->get();

        $modules = [
            [
                'code' => 'M01', 'name' => 'Programmation Web', 'semester' => $s1,
                'teacher' => $teachers['PROF-002'],
                'description' => 'Initiation au développement web : HTML, CSS, JavaScript et bases de PHP.',
                'chapters' => [
                    'Les bases du HTML5',
                    'Mise en forme avec CSS3',
                    'JavaScript : variables et fonctions',
                    'DOM et événements',
                    'Introduction à PHP',
                ],
            ],
            [
                'code' => 'M02', 'name' => 'Bases de données', 'semester' => $s1,
                'teacher' => $teachers['PROF-003'],
                'description' => 'Conception relationnelle, modèle entité-association, SQL.',
                'chapters' => [
                    'Modèle relationnel',
                    'Algèbre relationnelle',
                    'SQL : DDL et DML',
                    'Jointures et sous-requêtes',
                    'Normalisation',
                ],
            ],
            [
                'code' => 'M03', 'name' => 'Génie Logiciel', 'semester' => $s1,
                'teacher' => $teachers['PROF-001'],
                'description' => 'Méthodes de développement, UML, cycle de vie, qualité logicielle.',
                'chapters' => [
                    'Cycle de vie du logiciel',
                    'Diagrammes UML',
                    'Méthodes agiles vs RUP',
                    'Tests et qualité',
                ],
            ],
            [
                'code' => 'M04', 'name' => 'Algorithmique', 'semester' => $s1,
                'teacher' => $teachers['PROF-004'],
                'description' => 'Structures de données et complexité algorithmique.',
                'chapters' => [
                    'Récursivité',
                    'Tableaux et listes',
                    'Tri et recherche',
                    'Complexité algorithmique',
                ],
            ],
            [
                'code' => 'M05', 'name' => 'Framework Laravel', 'semester' => $s2,
                'teacher' => $teachers['PROF-002'],
                'description' => 'Développement avec Laravel : routes, contrôleurs, Eloquent, Blade.',
                'chapters' => [
                    'MVC et installation Laravel',
                    'Routes et contrôleurs',
                    'Eloquent ORM',
                    'Blade et formulaires',
                    'Tests et déploiement',
                ],
            ],
        ];

        foreach ($modules as $m) {
            $module = Module::updateOrCreate(
                ['code' => $m['code']],
                [
                    'name'        => $m['name'],
                    'description' => $m['description'],
                    'semester_id' => $m['semester']->id,
                    'teacher_id'  => $m['teacher']->id,
                ]
            );

            // Chapters
            foreach ($m['chapters'] as $i => $title) {
                Chapter::updateOrCreate(
                    ['module_id' => $module->id, 'title' => $title],
                    ['position' => $i + 1]
                );
            }

            // Enroll every student in every module
            $module->students()->syncWithoutDetaching($students->pluck('id'));
        }
    }
}
