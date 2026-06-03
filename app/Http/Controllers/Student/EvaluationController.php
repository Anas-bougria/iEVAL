<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        $moduleIds = $student->enrolledModules()->pluck('modules.id');

        $evaluations = Evaluation::with('module', 'teacher')
            ->whereIn('module_id', $moduleIds)
            ->where('status', '!=', 'draft')
            ->orderByDesc('opens_at')
            ->paginate(15);

        // Map of attempts for the student
        $attempts = $student->attempts()
            ->whereIn('evaluation_id', $evaluations->pluck('id'))
            ->get()
            ->groupBy('evaluation_id');

        return view('student.evaluations.index', compact('evaluations', 'attempts'));
    }

    public function show(Request $request, Evaluation $evaluation): View
    {
        $student = $request->user();

        // The student must be enrolled in this module
        abort_unless($student->enrolledModules()->where('modules.id', $evaluation->module_id)->exists(), 403);

        $evaluation->load(['module', 'teacher']);
        $myAttempts = $student->attempts()->where('evaluation_id', $evaluation->id)->get();

        return view('student.evaluations.show', compact('evaluation', 'myAttempts'));
    }
}
