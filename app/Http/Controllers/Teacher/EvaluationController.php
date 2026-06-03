<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $q = $teacher->authoredEvaluations()->with('module')->withCount('questions', 'attempts');

        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        if ($search = $request->get('q')) {
            $q->where('title', 'like', "%$search%");
        }

        $evaluations = $q->latest()->paginate(15)->withQueryString();
        return view('teacher.evaluations.index', compact('evaluations'));
    }

    public function create(Request $request): View
    {
        return view('teacher.evaluations.create', [
            'evaluation' => new Evaluation([
                'duration_minutes' => 30,
                'max_attempts'     => 1,
                'shuffle_questions' => true,
                'shuffle_options'   => true,
                'show_results_immediately' => true,
                'status'           => 'draft',
            ]),
            'modules'    => $this->teacherModules($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['teacher_id'] = $request->user()->id;
        $evaluation = Evaluation::create($data);
        return redirect()->route('teacher.evaluations.questions.index', $evaluation)
                         ->with('status', 'Évaluation créée. Ajoutez maintenant des questions.');
    }

    public function show(Evaluation $evaluation): View
    {
        $this->authorizeOwn($evaluation);
        $evaluation->load(['module', 'questions.options', 'questions.chapter']);
        return view('teacher.evaluations.show', compact('evaluation'));
    }

    public function edit(Request $request, Evaluation $evaluation): View
    {
        $this->authorizeOwn($evaluation);
        return view('teacher.evaluations.edit', [
            'evaluation' => $evaluation,
            'modules'    => $this->teacherModules($request),
        ]);
    }

    public function update(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);
        $evaluation->update($this->validated($request));
        return redirect()->route('teacher.evaluations.show', $evaluation)->with('status', 'Évaluation mise à jour.');
    }

    public function destroy(Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);
        $evaluation->delete();
        return redirect()->route('teacher.evaluations.index')->with('status', 'Évaluation supprimée.');
    }

    public function publish(Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);
        if ($evaluation->questions()->count() === 0) {
            return back()->with('status', 'Impossible de publier : aucune question.');
        }
        $evaluation->update(['status' => Evaluation::STATUS_PUBLISHED]);
        return back()->with('status', 'Évaluation publiée.');
    }

    public function close(Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);
        $evaluation->update(['status' => Evaluation::STATUS_CLOSED]);
        return back()->with('status', 'Évaluation clôturée.');
    }

    public function duplicate(Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);

        $copy = DB::transaction(function () use ($evaluation) {
            $copy = $evaluation->replicate();
            $copy->title  = $evaluation->title . ' (copie)';
            $copy->status = Evaluation::STATUS_DRAFT;
            $copy->save();

            foreach ($evaluation->questions as $question) {
                $newQ = $question->replicate(['evaluation_id']);
                $newQ->evaluation_id = $copy->id;
                $newQ->save();

                foreach ($question->options as $opt) {
                    $newOpt = $opt->replicate(['question_id']);
                    $newOpt->question_id = $newQ->id;
                    $newOpt->save();
                }
            }
            return $copy;
        });

        return redirect()->route('teacher.evaluations.edit', $copy)->with('status', 'Évaluation dupliquée.');
    }

    protected function authorizeOwn(Evaluation $evaluation): void
    {
        abort_unless($evaluation->teacher_id === auth()->id(), 403);
    }

    protected function teacherModules(Request $request)
    {
        return $request->user()->taughtModules()->orderBy('name')->get();
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'module_id'    => ['required', 'exists:modules,id'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'max_attempts'     => ['required', 'integer', 'min:1', 'max:10'],
            'shuffle_questions' => ['sometimes', 'boolean'],
            'shuffle_options'   => ['sometimes', 'boolean'],
            'show_results_immediately' => ['sometimes', 'boolean'],
            'opens_at'   => ['nullable', 'date'],
            'closes_at'  => ['nullable', 'date', 'after_or_equal:opens_at'],
            'status'     => ['sometimes', Rule::in(['draft', 'published', 'closed'])],
        ]);
    }
}
