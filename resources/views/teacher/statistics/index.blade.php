@extends('layouts.app')
@section('title', 'Statistiques')
@section('section', 'Enseignant')

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <section class="card card-pad">
        <h2 class="font-display text-xl mb-4">Par évaluation</h2>
        <table class="table-academic">
            <thead><tr><th>Évaluation</th><th>Module</th><th>Tentatives</th><th></th></tr></thead>
            <tbody>
                @forelse($evaluations as $e)
                    <tr>
                        <td><a href="{{ route('teacher.statistics.show', $e) }}" class="font-medium hover:text-saffron-500">{{ $e->title }}</a></td>
                        <td class="text-sm">{{ $e->module->name ?? '—' }}</td>
                        <td>{{ $e->submitted_count }}</td>
                        <td class="text-right">
                            <a href="{{ route('teacher.statistics.show', $e) }}" class="text-xs text-saffron-500 hover:underline">Analyser →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-ink-400 py-8">Aucune évaluation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card card-pad">
        <h2 class="font-display text-xl mb-4">Par module</h2>
        @forelse($modules as $m)
            <a href="{{ route('teacher.statistics.module', $m) }}"
               class="block border-b border-ink-100 last:border-0 py-3 hover:text-saffron-500">
                <p class="font-medium">{{ $m->name }}</p>
                <p class="text-xs text-ink-400">{{ $m->code }} · {{ $m->semester->name ?? '—' }}</p>
            </a>
        @empty
            <p class="text-ink-400 py-6 text-center">Aucun module.</p>
        @endforelse
    </section>
</div>
@endsection
