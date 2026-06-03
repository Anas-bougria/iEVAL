<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\EvaluationAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $attempts = $request->user()->attempts()
            ->with('evaluation.module')
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest()
            ->paginate(15);

        return view('student.results.index', compact('attempts'));
    }

    public function show(Request $request, EvaluationAttempt $attempt): View
    {
        abort_unless($attempt->student_id === $request->user()->id, 403);

        $attempt->load(['evaluation.module', 'evaluation.questions.options', 'evaluation.questions.chapter', 'answers']);

        $showCorrection = $attempt->evaluation->show_results_immediately
                       || $attempt->evaluation->status === 'closed';

        $answersByQuestion = $attempt->answers->keyBy('question_id');

        return view('student.results.show', compact('attempt', 'showCorrection', 'answersByQuestion'));
    }
}
