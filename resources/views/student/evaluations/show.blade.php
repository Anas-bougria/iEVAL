@extends('layouts.app')
@section('title', $evaluation->title)
@section('section', 'Évaluation · ' . $evaluation->module->name)

@section('content')
@php
    $submitted  = $myAttempts->whereIn('status', ['submitted', 'auto_submitted']);
    $inProgress = $myAttempts->where('status', 'in_progress')->first();
    $remaining  = $evaluation->max_attempts - $submitted->count();
    $canStart   = $evaluation->isOpen() && $remaining > 0;
@endphp

<div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-6">
        <div class="card card-pad">
            <h2 class="font-display text-3xl">{{ $evaluation->title }}</h2>
            <p class="text-ink-600 mt-2">{{ $evaluation->description ?? '—' }}</p>

            <div class="rule"></div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-sm">
                <div><p class="small-caps text-ink-400">Durée</p><p>{{ $evaluation->duration_minutes }} min</p></div>
                <div><p class="small-caps text-ink-400">Questions</p><p>{{ $evaluation->questions()->count() }}</p></div>
                <div><p class="small-caps text-ink-400">Tentatives restantes</p><p>{{ $remaining }} / {{ $evaluation->max_attempts }}</p></div>
                <div><p class="small-caps text-ink-400">Total points</p><p>{{ number_format($evaluation->totalPoints(), 2) }}</p></div>
            </div>

            @if($evaluation->instructions)
                <div class="rule"></div>
                <p class="small-caps text-ink-400 mb-2">Instructions</p>
                <div class="rounded-md bg-paper-100 border border-paper-200 p-4 text-ink-800 whitespace-pre-line text-sm leading-relaxed">{{ $evaluation->instructions }}</div>
            @endif
        </div>

        <div class="card card-pad">
            <h3 class="font-display text-lg mb-4">Vos tentatives</h3>
            @forelse($myAttempts as $a)
                <div class="border-b border-ink-100 last:border-0 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium">
                            @if($a->grade_20 !== null)
                                {{ number_format($a->grade_20, 2) }}/20
                            @else
                                <span class="text-ink-400">En cours…</span>
                            @endif
                        </p>
                        <p class="text-xs text-ink-400">{{ $a->statusLabel() }} · {{ optional($a->submitted_at)->format('d/m/Y H:i') ?? $a->started_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($a->status === 'in_progress')
                        <a href="{{ route('student.attempts.take', $a) }}" class="btn-accent">Reprendre →</a>
                    @else
                        <a href="{{ route('student.results.show', $a) }}" class="text-sm text-saffron-500 hover:underline">Voir détail →</a>
                    @endif
                </div>
            @empty
                <p class="text-ink-400 text-sm py-4 text-center">Aucune tentative effectuée.</p>
            @endforelse
        </div>
    </section>

    <aside class="space-y-4">
        <div class="card card-pad">
            <h3 class="font-display text-lg mb-3">Démarrer</h3>

            @if($inProgress)
                <p class="text-sm text-ink-600 mb-3">Vous avez une tentative en cours.</p>
                <a href="{{ route('student.attempts.take', $inProgress) }}" class="btn-accent w-full text-center block">Reprendre la tentative →</a>
            @elseif($canStart)
                <p class="text-sm text-ink-600 mb-3">
                    Une fois démarrée, le chronomètre est lancé.
                    Vous aurez exactement <strong>{{ $evaluation->duration_minutes }} minutes</strong>.
                </p>
                <form action="{{ route('student.attempts.start', $evaluation) }}" method="POST"
                      onsubmit="return confirm('Êtes-vous prêt(e) ? Le chronomètre démarrera immédiatement.')">
                    @csrf
                    <button class="btn-primary w-full">Démarrer maintenant →</button>
                </form>
            @else
                @if(!$evaluation->isOpen())
                    <p class="text-sm text-ink-500">Cette évaluation n'est pas ouverte actuellement.</p>
                @elseif($remaining <= 0)
                    <p class="text-sm text-ink-500">Vous avez utilisé toutes vos tentatives ({{ $evaluation->max_attempts }}).</p>
                @endif
            @endif
        </div>

        <div class="card card-pad text-sm">
            <p class="small-caps text-ink-500 mb-2">Conseil</p>
            <p class="text-ink-700">Assurez-vous d'avoir une connexion stable. En cas de perte de connexion ou d'expiration, votre tentative est soumise automatiquement avec les réponses enregistrées.</p>
        </div>
    </aside>
</div>
@endsection
