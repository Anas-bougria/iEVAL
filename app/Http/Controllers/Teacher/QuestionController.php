<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AnswerOption;
use App\Models\Evaluation;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Evaluation $evaluation): View
    {
        $this->authorizeOwn($evaluation);
        $evaluation->load(['module.chapters', 'questions.options', 'questions.chapter']);
        return view('teacher.questions.index', compact('evaluation'));
    }

    public function create(Evaluation $evaluation): View
    {
        $this->authorizeOwn($evaluation);
        $question = new Question([
            'evaluation_id' => $evaluation->id,
            'type'          => 'single',
            'points'        => 1,
            'position'      => ($evaluation->questions()->max('position') ?? 0) + 1,
        ]);

        return view('teacher.questions.create', [
            'evaluation' => $evaluation,
            'question'   => $question,
            'chapters'   => $evaluation->module->chapters,
            'options'    => [
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
            ],
        ]);
    }

    public function store(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $this->authorizeOwn($evaluation);
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $evaluation) {
            $question = $evaluation->questions()->create([
                'statement'  => $data['statement'],
                'type'       => $data['type'],
                'points'     => $data['points'],
                'position'   => $data['position'] ?? ($evaluation->questions()->max('position') + 1),
                'chapter_id' => $data['chapter_id'] ?? null,
            ]);

            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'text'       => $opt['text'],
                    'is_correct' => !empty($opt['is_correct']),
                    'position'   => $i + 1,
                ]);
            }
        });

        return redirect()->route('teacher.evaluations.questions.index', $evaluation)
                         ->with('status', 'Question créée.');
    }

    public function edit(Question $question): View
    {
        $evaluation = $question->evaluation;
        $this->authorizeOwn($evaluation);
        $question->load('options');

        return view('teacher.questions.edit', [
            'evaluation' => $evaluation,
            'question'   => $question,
            'chapters'   => $evaluation->module->chapters,
            'options'    => $question->options->map(fn ($o) => [
                'id'         => $o->id,
                'text'       => $o->text,
                'is_correct' => $o->is_correct,
            ])->toArray(),
        ]);
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $evaluation = $question->evaluation;
        $this->authorizeOwn($evaluation);
        $data = $this->validated($request);

        DB::transaction(function () use ($question, $data) {
            $question->update([
                'statement'  => $data['statement'],
                'type'       => $data['type'],
                'points'     => $data['points'],
                'position'   => $data['position'] ?? $question->position,
                'chapter_id' => $data['chapter_id'] ?? null,
            ]);

            // Replace options
            $question->options()->delete();
            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'text'       => $opt['text'],
                    'is_correct' => !empty($opt['is_correct']),
                    'position'   => $i + 1,
                ]);
            }
        });

        return redirect()->route('teacher.evaluations.questions.index', $evaluation)
                         ->with('status', 'Question mise à jour.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $evaluation = $question->evaluation;
        $this->authorizeOwn($evaluation);
        $question->delete();
        return back()->with('status', 'Question supprimée.');
    }

    public function show(Question $question): View
    {
        return $this->edit($question);
    }

    protected function authorizeOwn(Evaluation $evaluation): void
    {
        abort_unless($evaluation->teacher_id === auth()->id(), 403);
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'statement'  => ['required', 'string'],
            'type'       => ['required', Rule::in(['single', 'multiple'])],
            'points'     => ['required', 'numeric', 'min:0.25', 'max:100'],
            'chapter_id' => ['nullable', 'exists:chapters,id'],
            'position'   => ['nullable', 'integer', 'min:0'],
            'options'              => ['required', 'array', 'min:2', 'max:8'],
            'options.*.text'       => ['required', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
        ]);

        $correctCount = collect($data['options'])->filter(fn ($o) => !empty($o['is_correct']))->count();
        if ($correctCount < 1) {
            abort(422, 'Au moins une option correcte est requise.');
        }
        if ($data['type'] === 'single' && $correctCount !== 1) {
            abort(422, 'Une question QCU doit avoir exactement une seule bonne réponse.');
        }

        return $data;
    }
}
