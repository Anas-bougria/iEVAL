@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('section', 'Espace enseignant')
@section('subtitle', 'Bonjour ' . auth()->user()->name . ' — votre activité pédagogique en un coup d\'œil.')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Modules enseignés', 'value' => $stats['modules'],     'sub' => ''],
            ['label' => 'Évaluations',       'value' => $stats['evaluations'], 'sub' => 'créées'],
            ['label' => 'Tentatives reçues', 'value' => $stats['attempts'],    'sub' => 'évaluations passées'],
            ['label' => 'Moyenne générale',  'value' => $stats['avg_grade'] ? $stats['avg_grade'] . '/20' : '—', 'sub' => 'tous étudiants confondus'],
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
            <h2 class="font-display text-xl">Mes dernières évaluations</h2>
            <a href="{{ route('teacher.evaluations.create') }}" class="btn-accent">+ Nouvelle évaluation</a>
        </div>
        <table class="table-academic">
            <thead><tr><th>Titre</th><th>Module</th><th>Status</th><th>Tentatives</th><th></th></tr></thead>
            <tbody>
                @forelse($evaluations as $e)
                    <tr>
                        <td>
                            <a href="{{ route('teacher.evaluations.show', $e) }}" class="font-medium hover:text-saffron-500">{{ $e->title }}</a>
                            <p class="text-xs text-ink-400">{{ $e->duration_minutes }} min · {{ $e->questions()->count() }} questions</p>
                        </td>
                        <td class="text-sm">{{ $e->module->name ?? '—' }}</td>
                        <td>
                            <span class="badge-{{ $e->status === 'published' ? 'saffron' : ($e->status === 'closed' ? 'clay' : 'ink') }}">
                                {{ $e->statusLabel() }}
                            </span>
                        </td>
                        <td>{{ $e->attempts()->whereIn('status', ['submitted','auto_submitted'])->count() }}</td>
                        <td>
                            <a href="{{ route('teacher.statistics.show', $e) }}" class="text-xs text-ink-500 hover:text-saffron-500">Stats →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-400 py-8">
                        Aucune évaluation pour l'instant.
                        <a href="{{ route('teacher.evaluations.create') }}" class="text-saffron-500 hover:underline">Créez la première →</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card card-pad">
        <h2 class="font-display text-xl mb-4">Mes modules</h2>
        @forelse($modules as $m)
            <div class="border-b border-ink-100 last:border-0 py-3">
                <p class="font-medium">{{ $m->name }}</p>
                <p class="text-xs text-ink-400">{{ $m->code }} · {{ $m->semester->name ?? '—' }}</p>
                <a href="{{ route('teacher.statistics.module', $m) }}" class="inline-block text-xs text-saffron-500 mt-1 hover:underline">Voir statistiques →</a>
            </div>
        @empty
            <p class="text-ink-400 text-sm">Aucun module ne vous est assigné. Contactez l'administrateur.</p>
        @endforelse
    </section>
</div>
@endsection
