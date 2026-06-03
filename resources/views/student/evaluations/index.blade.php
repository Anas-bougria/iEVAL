@extends('layouts.app')
@section('title', 'Évaluations')
@section('section', 'Espace étudiant')

@section('content')
<div class="grid gap-4">
    @forelse($evaluations as $e)
        @php
            $myAttempts = $attempts[$e->id] ?? collect();
            $submitted  = $myAttempts->whereIn('status', ['submitted', 'auto_submitted']);
            $bestGrade  = $submitted->max('grade_20');
            $remaining  = $e->max_attempts - $submitted->count();
            $isOpen     = $e->isOpen();
        @endphp
        <div class="card card-pad flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="badge-ink">{{ $e->module->name }}</span>
                    @if($e->status === 'closed')<span class="badge-clay">Clôturée</span>@elseif($isOpen)<span class="badge-saffron">Ouverte</span>@else<span class="badge-ink">Non ouverte</span>@endif
                </div>
                <h3 class="font-display text-xl text-ink-900">{{ $e->title }}</h3>
                <p class="text-sm text-ink-500 mt-1">
                    {{ $e->duration_minutes }} min ·
                    {{ $e->questions()->count() }} questions ·
                    {{ $remaining }} tentative(s) restante(s)
                </p>
                @if($e->closes_at)
                    <p class="text-xs text-ink-400 mt-1">Ferme le {{ $e->closes_at->translatedFormat('d F Y à H:i') }}</p>
                @endif
            </div>

            <div class="flex items-center gap-4 shrink-0">
                @if($bestGrade !== null)
                    <div class="text-right">
                        <p class="small-caps text-ink-400">Meilleure note</p>
                        <p class="font-display text-2xl text-ink-900">{{ number_format($bestGrade, 2) }}<span class="text-xs text-ink-400">/20</span></p>
                    </div>
                @endif

                <a href="{{ route('student.evaluations.show', $e) }}" class="btn-primary">
                    @if($submitted->count() > 0 && $remaining <= 0) Voir résultat
                    @elseif($isOpen && $remaining > 0) Démarrer →
                    @else Détails @endif
                </a>
            </div>
        </div>
    @empty
        <div class="card card-pad text-center text-ink-400 py-12">
            Aucune évaluation disponible pour vos modules.
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $evaluations->links() }}</div>
@endsection
