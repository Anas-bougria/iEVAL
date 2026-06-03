<?php

namespace Database\Seeders;

use App\Models\AnswerOption;
use App\Models\Chapter;
use App\Models\Evaluation;
use App\Models\Module;
use App\Models\Question;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        // -------- Evaluation 1: HTML/CSS (Module Programmation Web) --------
        $module = Module::where('code', 'M01')->first();
        if ($module) {
            $chHtml = $module->chapters()->where('title', 'Les bases du HTML5')->first();
            $chCss  = $module->chapters()->where('title', 'Mise en forme avec CSS3')->first();

            $eval = Evaluation::updateOrCreate(
                ['title' => 'QCM Introduction HTML & CSS', 'module_id' => $module->id],
                [
                    'description'      => 'Évaluation diagnostique sur les fondamentaux HTML5 et CSS3.',
                    'instructions'     => "Vous disposez de 20 minutes pour répondre à 6 questions.\nLisez chaque énoncé attentivement avant de répondre.",
                    'teacher_id'       => $module->teacher_id,
                    'duration_minutes' => 20,
                    'max_attempts'     => 2,
                    'status'           => 'published',
                    'opens_at'         => now()->subDay(),
                    'closes_at'        => now()->addMonth(),
                ]
            );

            $this->makeQuestion($eval, $chHtml, 'single', 1, 'Quelle balise HTML définit le titre principal d\'une page ?', [
                ['<h1>', true], ['<title>', false], ['<header>', false], ['<head>', false],
            ]);
            $this->makeQuestion($eval, $chHtml, 'single', 1, 'Quel attribut HTML est utilisé pour identifier de manière unique un élément ?', [
                ['class', false], ['id', true], ['name', false], ['key', false],
            ]);
            $this->makeQuestion($eval, $chHtml, 'multiple', 2, 'Parmi les balises suivantes, lesquelles sont sémantiques en HTML5 ?', [
                ['<article>', true], ['<section>', true], ['<div>', false], ['<nav>', true], ['<span>', false],
            ]);
            $this->makeQuestion($eval, $chCss, 'single', 1, 'Quelle propriété CSS contrôle l\'espacement intérieur d\'un élément ?', [
                ['margin', false], ['padding', true], ['border', false], ['spacing', false],
            ]);
            $this->makeQuestion($eval, $chCss, 'single', 1, 'Quelle valeur de `display` rend un élément flexible ?', [
                ['block', false], ['inline', false], ['flex', true], ['none', false],
            ]);
            $this->makeQuestion($eval, $chCss, 'multiple', 2, 'Quels sélecteurs CSS sont valides ?', [
                ['.classe', true], ['#identifiant', true], ['$valeur', false], ['div > p', true], ['@balise', false],
            ]);
        }

        // -------- Evaluation 2: SQL (Module Bases de données) --------
        $module = Module::where('code', 'M02')->first();
        if ($module) {
            $chSql = $module->chapters()->where('title', 'SQL : DDL et DML')->first();
            $chJoin= $module->chapters()->where('title', 'Jointures et sous-requêtes')->first();

            $eval = Evaluation::updateOrCreate(
                ['title' => 'Contrôle SQL — Niveau 1', 'module_id' => $module->id],
                [
                    'description'      => 'Évaluation portant sur les requêtes SQL de base et les jointures.',
                    'instructions'     => 'Durée : 15 minutes. Une seule tentative autorisée.',
                    'teacher_id'       => $module->teacher_id,
                    'duration_minutes' => 15,
                    'max_attempts'     => 1,
                    'status'           => 'published',
                    'opens_at'         => now()->subDays(2),
                    'closes_at'        => now()->addWeeks(2),
                ]
            );

            $this->makeQuestion($eval, $chSql, 'single', 1, 'Quelle commande SQL permet de récupérer des données ?', [
                ['SELECT', true], ['INSERT', false], ['UPDATE', false], ['DELETE', false],
            ]);
            $this->makeQuestion($eval, $chSql, 'single', 1, 'Quelle clause filtre les lignes d\'une requête SELECT ?', [
                ['ORDER BY', false], ['GROUP BY', false], ['WHERE', true], ['HAVING', false],
            ]);
            $this->makeQuestion($eval, $chJoin, 'single', 2, 'Quel type de jointure retourne TOUTES les lignes de la table de gauche, même sans correspondance à droite ?', [
                ['INNER JOIN', false], ['LEFT JOIN', true], ['RIGHT JOIN', false], ['CROSS JOIN', false],
            ]);
            $this->makeQuestion($eval, $chSql, 'multiple', 2, 'Quelles commandes appartiennent au DDL ?', [
                ['CREATE', true], ['DROP', true], ['SELECT', false], ['ALTER', true], ['UPDATE', false],
            ]);
        }

        // -------- Evaluation 3: UML (Module Génie Logiciel) — DRAFT --------
        $module = Module::where('code', 'M03')->first();
        if ($module) {
            $ch = $module->chapters()->where('title', 'Diagrammes UML')->first();
            $eval = Evaluation::updateOrCreate(
                ['title' => 'Diagrammes UML — Brouillon', 'module_id' => $module->id],
                [
                    'description'      => 'À publier après le cours du 15 mai.',
                    'teacher_id'       => $module->teacher_id,
                    'duration_minutes' => 25,
                    'max_attempts'     => 1,
                    'status'           => 'draft',
                ]
            );
            $this->makeQuestion($eval, $ch, 'single', 1, 'Quel diagramme UML représente les interactions entre acteurs et le système ?', [
                ['Diagramme de classes', false], ['Diagramme de cas d\'utilisation', true], ['Diagramme de séquence', false], ['Diagramme d\'activité', false],
            ]);
            $this->makeQuestion($eval, $ch, 'multiple', 2, 'Quels diagrammes UML sont des diagrammes comportementaux ?', [
                ['Diagramme de séquence', true], ['Diagramme d\'activité', true], ['Diagramme de classes', false], ['Diagramme d\'états-transitions', true], ['Diagramme de composants', false],
            ]);
        }
    }

    protected function makeQuestion(Evaluation $eval, ?Chapter $chapter, string $type, float $points, string $statement, array $options): void
    {
        $q = Question::updateOrCreate(
            ['evaluation_id' => $eval->id, 'statement' => $statement],
            [
                'chapter_id' => $chapter?->id,
                'type'       => $type,
                'points'     => $points,
                'position'   => ($eval->questions()->max('position') ?? 0) + 1,
            ]
        );

        $q->options()->delete();
        foreach ($options as $i => [$text, $correct]) {
            AnswerOption::create([
                'question_id' => $q->id,
                'text'        => $text,
                'is_correct'  => $correct,
                'position'    => $i + 1,
            ]);
        }
    }
}
