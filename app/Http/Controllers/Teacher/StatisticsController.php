<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use App\Models\Module;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $evaluations = $teacher->authoredEvaluations()
            ->with('module')
            ->withCount(['attempts as submitted_count' => fn ($q) =>
                $q->whereIn('status', ['submitted', 'auto_submitted'])])
            ->latest()
            ->get();

        $modules = $teacher->taughtModules()->with('semester')->get();

        return view('teacher.statistics.index', compact('evaluations', 'modules'));
    }

    public function show(Evaluation $evaluation): View
    {
        abort_unless($evaluation->teacher_id === auth()->id(), 403);

        $attempts = $evaluation->attempts()
            ->with('student')
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->orderByDesc('grade_20')
            ->get();

        $count = $attempts->count();
        $avg   = $count ? round($attempts->avg('grade_20'), 2) : null;
        $max   = $count ? round($attempts->max('grade_20'), 2) : null;
        $min   = $count ? round($attempts->min('grade_20'), 2) : null;

        // Distribution sur 5 paliers de 0-4, 4-8, 8-12, 12-16, 16-20
        $distribution = [0, 0, 0, 0, 0];
        foreach ($attempts as $a) {
            $g = (float) $a->grade_20;
            $i = min(4, (int) floor($g / 4));
            $distribution[$i]++;
        }

        // Performance par chapitre : % de bonnes réponses par chapitre
        $perChapter = [];
        $questionsByChapter = $evaluation->questions->groupBy(fn ($q) => $q->chapter_id);
        $attemptIds = $attempts->pluck('id');

        foreach ($questionsByChapter as $chapterId => $questions) {
            $chapter = $chapterId ? optional($questions->first()->chapter)->title ?? 'Sans chapitre' : 'Sans chapitre';
            $qIds = $questions->pluck('id');

            $totalAnswers   = StudentAnswer::whereIn('attempt_id', $attemptIds)->whereIn('question_id', $qIds)->count();
            $correctAnswers = StudentAnswer::whereIn('attempt_id', $attemptIds)->whereIn('question_id', $qIds)->where('is_correct', true)->count();

            $perChapter[] = [
                'chapter'  => $chapter,
                'total'    => $totalAnswers,
                'correct'  => $correctAnswers,
                'rate'     => $totalAnswers ? round($correctAnswers / $totalAnswers * 100, 1) : 0,
            ];
        }

        return view('teacher.statistics.show', compact(
            'evaluation', 'attempts', 'count', 'avg', 'max', 'min', 'distribution', 'perChapter'
        ));
    }

    public function module(Module $module): View
    {
        abort_unless($module->teacher_id === auth()->id(), 403);

        $evaluationIds = $module->evaluations()->pluck('id');
        $attempts = EvaluationAttempt::whereIn('evaluation_id', $evaluationIds)
                    ->whereIn('status', ['submitted', 'auto_submitted'])
                    ->with('student', 'evaluation')
                    ->get();

        // Per-student average across all evaluations of this module
        $perStudent = $attempts->groupBy('student_id')->map(function ($group) {
            $student = $group->first()->student;
            return [
                'student'    => $student,
                'count'      => $group->count(),
                'avg'        => round($group->avg('grade_20'), 2),
                'best'       => round($group->max('grade_20'), 2),
            ];
        })->sortByDesc('avg')->values();

        return view('teacher.statistics.module', compact('module', 'perStudent', 'attempts'));
    }
}
