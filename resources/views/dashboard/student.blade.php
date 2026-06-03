@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('section', 'Espace étudiant')
@section('subtitle', 'Bienvenue ' . auth()->user()->name . ' — voici les évaluations qui vous attendent.')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Mes modules',     'value' => $stats['modules'],   'sub' => 'inscrits ce semestre'],
            ['label' => 'Disponibles',     'value' => $stats['available'], 'sub' => 'évaluations à passer'],
            ['label' => 'Effectuées',      'value' => $stats['completed'], 'sub' => 'évaluations soumises'],
            ['label' => 'Moyenne',         'value' => $stats['avg_grade'] ? $stats['avg_grade'] . '/20' : '—', 'sub' => 'toutes évaluations'],
        ];
    @endphp
    @foreach($cards as $c)
        <div class="card card-pad animate-rise">
            <p class="small-caps text-ink-500">{{ $c['label'] }}</p>
            <p class="font-display text-4xl text-ink-900 mt-2">{{ $c['value'] }}</p>
            <p class="text-xs text-ink-400 mt-1">{{ $c['sub'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 card card-pad">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl">Évaluations disponibles</h2>
            <a href="{{ route('student.evaluations.index') }}" class="text-xs text-ink-500 hover:text-ink-900">Voir tout →</a>
        </div>
        @forelse($availableEvaluations as $e)
            <div class="border-b border-ink-100 last:border-0 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-medium text-ink-900 truncate">{{ $e->title }}</p>
                    <p class="text-xs text-ink-400">{{ $e->module->name }} · {{ $e->duration_minutes }} min</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if($e->closes_at)
                        <span class="text-xs text-ink-500">Ferme le {{ $e->closes_at->format('d/m H:i') }}</span>
                    @endif
                    <a href="{{ route('student.evaluations.show', $e) }}" class="btn-accent">Démarrer →</a>
                </div>
            </div>
        @empty
            <p class="text-ink-400 py-6 text-center">Aucune évaluation disponible pour le moment.</p>
        @endforelse
    </section>

    <section class="card card-pad">
        <h2 class="font-display text-xl mb-4">Mes résultats récents</h2>
        @forelse($recentAttempts as $a)
            <div class="border-b border-ink-100 last:border-0 py-3">
                <a href="{{ route('student.results.show', $a) }}" class="block hover:text-saffron-500">
                    <p class="font-medium truncate">{{ $a->evaluation->title }}</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-xs text-ink-400">{{ $a->evaluation->module->name }}</span>
                        <span class="font-display text-xl text-ink-900">{{ number_format($a->grade_20, 2) }}<span class="text-xs text-ink-400">/20</span></span>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-ink-400 text-sm">Aucun résultat encore.</p>
        @endforelse
    </section>
</div>
@endsection
