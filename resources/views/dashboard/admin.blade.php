@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('section', 'Administration')
@section('subtitle', 'Vue d\'ensemble de la plateforme iEVAL.')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Utilisateurs',  'value' => $stats['users_total'],  'sub' => $stats['admins'].' admin · '.$stats['teachers'].' prof · '.$stats['students'].' étud.'],
            ['label' => 'Modules',       'value' => $stats['modules'],      'sub' => 'matières actives'],
            ['label' => 'Évaluations',   'value' => $stats['evaluations'],  'sub' => 'QCM créés'],
            ['label' => 'Tentatives',    'value' => $stats['attempts'],     'sub' => 'évaluations passées'],
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

<div class="grid lg:grid-cols-2 gap-6">
    <section class="card card-pad">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl">Derniers utilisateurs</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-ink-500 hover:text-ink-900">Voir tout →</a>
        </div>
        <table class="table-academic">
            <thead><tr><th>Nom</th><th>Rôle</th><th>Créé</th></tr></thead>
            <tbody>
                @forelse($recentUsers as $u)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $u->name }}</p>
                            <p class="text-xs text-ink-400">{{ $u->email }}</p>
                        </td>
                        <td><span class="badge-ink capitalize">{{ $u->role }}</span></td>
                        <td class="text-xs text-ink-500">{{ $u->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-ink-400 py-6">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card card-pad">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl">Évaluations récentes</h2>
        </div>
        <table class="table-academic">
            <thead><tr><th>Titre</th><th>Module</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recentEvaluations as $e)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $e->title }}</p>
                            <p class="text-xs text-ink-400">par {{ $e->teacher->name ?? '—' }}</p>
                        </td>
                        <td class="text-sm">{{ $e->module->name ?? '—' }}</td>
                        <td>
                            <span class="badge-{{ $e->status === 'published' ? 'saffron' : ($e->status === 'closed' ? 'clay' : 'ink') }}">
                                {{ $e->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-ink-400 py-6">Aucune évaluation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
