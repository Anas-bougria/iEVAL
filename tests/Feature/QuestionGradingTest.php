<?php

use App\Models\AnswerOption;
use App\Models\Evaluation;
use App\Models\Module;
use App\Models\Question;
use App\Models\Semester;
use App\Models\User;

it('grades a single-answer question correctly', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $semester = Semester::create(['name' => 'S1', 'code' => 'TEST', 'start_date' => '2025-09-01', 'end_date' => '2026-01-31']);
    $module = Module::create(['code' => 'T01', 'name' => 'Test', 'semester_id' => $semester->id, 'teacher_id' => $teacher->id]);
    $eval = Evaluation::create([
        'title' => 'Test', 'module_id' => $module->id, 'teacher_id' => $teacher->id,
        'duration_minutes' => 10,
    ]);

    $q = Question::create([
        'evaluation_id' => $eval->id, 'statement' => '1+1?',
        'type' => 'single', 'points' => 1, 'position' => 1,
    ]);
    $opt1 = AnswerOption::create(['question_id' => $q->id, 'text' => '2', 'is_correct' => true,  'position' => 1]);
    $opt2 = AnswerOption::create(['question_id' => $q->id, 'text' => '3', 'is_correct' => false, 'position' => 2]);

    expect($q->evaluateAnswer([$opt1->id]))->toMatchArray(['is_correct' => true,  'earned_points' => 1.0])
        ->and($q->evaluateAnswer([$opt2->id]))->toMatchArray(['is_correct' => false, 'earned_points' => 0.0]);
});
