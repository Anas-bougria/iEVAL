<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttemptController extends Controller
{
    public function start(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $student = $request->user();

        abort_unless($student->enrolledModules()->where('modules.id', $evaluation->module_id)->exists(), 403);
        abort_unless($evaluation->isOpen(), 403, 'Cette évaluation n\'est pas ouverte.');

        // Reuse in-progress attempt if any
        $existing = $student->attempts()
            ->where('evaluation_id', $evaluation->id)
            ->where('status', EvaluationAttempt::STATUS_IN_PROGRESS)
            ->first();

        if ($existing) {
            if ($existing->isExpired()) {
                $existing->recordAnswers([]);
                $existing->submit(EvaluationAttempt::STATUS_AUTO_SUBMITTED);
                return redirect()->route('student.results.show', $existing);
            }
            return redirect()->route('student.attempts.take', $existing);
        }

        // Limit checks
        $usedAttempts = $student->attempts()
            ->where('evaluation_id', $evaluation->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->count();
        abort_if($usedAttempts >= $evaluation->max_attempts, 403, 'Nombre maximal de tentatives atteint.');

        $attempt = EvaluationAttempt::create([
            'evaluation_id' => $evaluation->id,
            'student_id'    => $student->id,
            'started_at'    => now(),
            'status'        => EvaluationAttempt::STATUS_IN_PROGRESS,
        ]);

        return redirect()->route('student.attempts.take', $attempt);
    }

    public function take(Request $request, EvaluationAttempt $attempt): View|RedirectResponse
    {
        abort_unless($attempt->student_id === $request->user()->id, 403);

        if ($attempt->status !== EvaluationAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('student.results.show', $attempt);
        }

        // Auto-submit if expired
        if ($attempt->isExpired()) {
            $attempt->recordAnswers([]);
            $attempt->submit(EvaluationAttempt::STATUS_AUTO_SUBMITTED);
            return redirect()->route('student.results.show', $attempt)
                             ->with('status', 'Temps écoulé — votre tentative a été soumise automatiquement.');
        }

        $evaluation = $attempt->evaluation;
        $evaluation->load(['questions.options', 'module']);

        // Shuffle if enabled
        if ($evaluation->shuffle_questions) {
            $evaluation->setRelation('questions', $evaluation->questions->shuffle()->values());
        }
        if ($evaluation->shuffle_options) {
            foreach ($evaluation->questions as $question) {
                $question->setRelation('options', $question->options->shuffle()->values());
            }
        }

        return view('student.attempts.take', compact('attempt', 'evaluation'));
    }

    public function submit(Request $request, EvaluationAttempt $attempt): RedirectResponse
    {
        abort_unless($attempt->student_id === $request->user()->id, 403);

        if ($attempt->status !== EvaluationAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('student.results.show', $attempt);
        }

        $answers = $request->input('answers', []);
        // Normalize: { questionId: optionId } or { questionId: [optionId, ...] }
        $normalized = [];
        foreach ($answers as $qid => $val) {
            if (is_array($val)) {
                $normalized[(int) $qid] = array_values(array_map('intval', $val));
            } else {
                $normalized[(int) $qid] = [(int) $val];
            }
        }

        $status = $attempt->isExpired()
            ? EvaluationAttempt::STATUS_AUTO_SUBMITTED
            : EvaluationAttempt::STATUS_SUBMITTED;

        $attempt->recordAnswers($normalized);
        $attempt->submit($status);

        return redirect()->route('student.results.show', $attempt)
                         ->with('status', 'Évaluation soumise. Voici votre résultat.');
    }
}
