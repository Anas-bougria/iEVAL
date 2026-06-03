@extends('layouts.app')
@section('title', 'Statistiques — ' . $module->name)
@section('section', 'Enseignant · Module')

@section('content')
<div class="card card-pad mb-6">
    <p class="small-caps text-ink-500">{{ $module->code }}</p>
    <h2 class="font-display text-2xl mt-1">{{ $module->name }}</h2>
    <p class="text-sm text-ink-500 mt-1">{{ $attempts->count() }} tentatives soumises · {{ $perStudent->count() }} étudiants ayant participé</p>
</div>

<div class="card card-pad">
    <h3 class="font-display text-lg mb-4">Classement des étudiants</h3>
    <table class="table-academic">
        <thead><tr><th>#</th><th>Étudiant</th><th>Tentatives</th><th>Moyenne</th><th>Meilleure note</th></tr></thead>
        <tbody>
            @forelse($perStudent as $row)
                <tr>
                    <td class="text-ink-400">{{ $loop->iteration }}</td>
                    <td>
                        <p class="font-medium">{{ $row['student']->name }}</p>
                        <p class="text-xs text-ink-400">{{ $row['student']->matricule ?? $row['student']->email }}</p>
                    </td>
                    <td>{{ $row['count'] }}</td>
                    <td><span class="font-display text-xl">{{ $row['avg'] }}</span><span class="text-xs text-ink-400">/20</span></td>
                    <td><span class="badge-saffron">{{ $row['best'] }}/20</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-ink-400 py-8">Aucune donnée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
