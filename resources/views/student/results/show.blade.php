@extends('layouts.app')
@section('title', 'Résultat — ' . $attempt->evaluation->title)
@section('section', 'Mes résultats')

@section('content')
<div class="card card-pad mb-6 grid sm:grid-cols-3 gap-6">
    <div class="sm:col-span-2">
        <p class="small-caps text-ink-500">{{ $attempt->evaluation->module->name }}</p>
        <h2 class="font-display text-3xl mt-1">{{ $attempt->evaluation->title }}</h2>
        <p class="text-sm text-ink-500 mt-2">
            Soumise le {{ optional($attempt->submitted_at)->translatedFormat('l j F Y à H:i') }} · {{ $attempt->statusLabel() }}
        </p>
    </div>
    <div class="text-right">
        <p class="small-caps text-ink-500">Note finale</p>
        <p class="font-display text-6xl text-ink-900 leading-none mt-1">
            {{ number_format($attempt->grade_20, 2) }}<span class="text-xl text-ink-400">/20</span>
        </p>
        <p class="text-xs text-ink-500 mt-1">
            {{ number_format($attempt->score, 2) }} / {{ number_format($attempt->max_score, 2) }} points
        </p>
    </div>
</div>

@if($showCorrection)
    <p class="small-caps text-ink-500 mb-3">Correction détaillée</p>

    @foreach($attempt->evaluation->questions as $i => $q)
        @php
            $sa = $answersByQuestion->get($q->id);
            $selected = $sa ? $sa->selected_option_ids : [];
            $isCorrect = $sa ? $sa->is_correct : false;
        @endphp
        <div class="card card-pad mb-4">
            <div class="flex items-baseline justify-between mb-2">
                <p class="small-caps text-ink-500">Question {{ $i + 1 }}</p>
                <div class="flex items-center gap-2">
                    @if($isCorrect)
                        <span class="badge-green">✓ Correcte · {{ number_format($q->points, 2) }} pt</span>
                    @else
                        <span class="badge-clay">✗ Incorrecte · 0 pt</span>
                    @endif
                </div>
            </div>

            <h3 class="font-display text-lg text-ink-900 mb-4">{{ $q->statement }}</h3>

            <div class="space-y-2">
                @foreach($q->options as $o)
                    @php
                        $isSelected = in_array($o->id, $selected);
                        $cssClass = $o->is_correct
                            ? 'border-emerald-300 bg-emerald-50/60'
                            : ($isSelected ? 'border-clay-400 bg-clay-400/10' : 'border-ink-200 bg-paper-50');
                    @endphp
                    <div class="flex items-start gap-3 rounded-md border px-4 py-2.5 {{ $cssClass }}">
                        <span class="mt-0.5 shrink-0">
                            @if($o->is_correct) <span class="text-emerald-700 font-bold">✓</span>
                            @elseif($isSelected) <span class="text-clay-500 font-bold">✗</span>
                            @else <span class="text-ink-300">○</span>
                            @endif
                        </span>
                        <span class="text-ink-800">{{ $o->text }}</span>
                        @if($isSelected)
                            <span class="ml-auto text-xs text-ink-500 italic shrink-0">votre réponse</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <div class="card card-pad text-center">
        <p class="text-ink-600">La correction détaillée n'est pas encore disponible.</p>
        <p class="text-xs text-ink-400 mt-1">Elle sera publiée à la clôture de l'évaluation par l'enseignant.</p>
    </div>
@endif

<div class="mt-6 flex justify-between">
    <a href="{{ route('student.results.index') }}" class="btn-outline">← Tous mes résultats</a>
    <a href="{{ route('student.evaluations.index') }}" class="btn-ghost">Autres évaluations →</a>
</div>
@endsection
