<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationAttempt;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route(Auth::user()->dashboardRoute());
    }

    public function admin(): View
    {
        $stats = [
            'users_total'      => User::count(),
            'admins'           => User::where('role', 'admin')->count(),
            'teachers'         => User::where('role', 'teacher')->count(),
            'students'         => User::where('role', 'student')->count(),
            'modules'          => Module::count(),
            'evaluations'      => Evaluation::count(),
            'attempts'         => EvaluationAttempt::whereIn('status', ['submitted', 'auto_submitted'])->count(),
        ];

        $recentUsers       = User::latest()->take(6)->get();
        $recentEvaluations = Evaluation::with(['module', 'teacher'])->latest()->take(6)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentEvaluations'));
    }

    public function teacher(): View
    {
        $teacher = Auth::user();

        $modules        = $teacher->taughtModules()->with('semester')->get();
        $evaluations    = $teacher->authoredEvaluations()->with('module')->latest()->take(8)->get();

        $myEvalIds = $teacher->authoredEvaluations()->pluck('id');
        $attemptsCount   = EvaluationAttempt::whereIn('evaluation_id', $myEvalIds)
                            ->whereIn('status', ['submitted', 'auto_submitted'])->count();
        $averageGrade    = EvaluationAttempt::whereIn('evaluation_id', $myEvalIds)
                            ->whereIn('status', ['submitted', 'auto_submitted'])
                            ->avg('grade_20');

        $stats = [
            'modules'      => $modules->count(),
            'evaluations'  => $teacher->authoredEvaluations()->count(),
            'attempts'     => $attemptsCount,
            'avg_grade'    => $averageGrade ? round($averageGrade, 2) : null,
        ];

        return view('dashboard.teacher', compact('stats', 'modules', 'evaluations'));
    }

    public function student(): View
    {
        $student = Auth::user();

        $modules = $student->enrolledModules()->with('semester', 'teacher')->get();

        $availableEvaluations = Evaluation::openNow()
            ->whereIn('module_id', $modules->pluck('id'))
            ->with('module')
            ->latest()
            ->take(8)
            ->get();

        $recentAttempts = $student->attempts()
            ->with('evaluation.module')
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest()
            ->take(6)
            ->get();

        $avg = $student->attempts()
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->avg('grade_20');

        $stats = [
            'modules'       => $modules->count(),
            'available'     => $availableEvaluations->count(),
            'completed'     => $student->attempts()->whereIn('status', ['submitted', 'auto_submitted'])->count(),
            'avg_grade'     => $avg ? round($avg, 2) : null,
        ];

        return view('dashboard.student', compact('stats', 'modules', 'availableEvaluations', 'recentAttempts'));
    }
}
