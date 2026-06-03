@extends('layouts.app')
@section('title', 'Mes évaluations')
@section('section', 'Enseignant')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-3 items-end">
        <div><label class="label">Recherche</label><input name="q" value="{{ request('q') }}" class="input w-56"></div>
        <div><label class="label">Status</label>
            <select name="status" class="select w-40">
                <option value="">Tous</option>
                @foreach(['draft' => 'Brouillon', 'published' => 'Publiée', 'closed' => 'Clôturée'] as $k => $l)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-outline">Filtrer</button>
    </form>
    <a href="{{ route('teacher.evaluations.create') }}" class="btn-primary">+ Nouvelle évaluation</a>
</div>

<div class="card overflow-hidden">
    <table class="table-academic">
        <thead><tr><th>Titre</th><th>Module</th><th>Durée</th><th>Questions</th><th>Tentatives</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($evaluations as $e)
                <tr>
                    <td>
                        <a href="{{ route('teacher.evaluations.show', $e) }}" class="font-medium hover:text-saffron-500">{{ $e->title }}</a>
                    </td>
                    <td class="text-sm">{{ $e->module->name ?? '—' }}</td>
                    <td class="text-sm">{{ $e->duration_minutes }} min</td>
                    <td>{{ $e->questions_count }}</td>
                    <td>{{ $e->attempts_count }}</td>
                    <td>
                        <span class="badge-{{ $e->status === 'published' ? 'saffron' : ($e->status === 'closed' ? 'clay' : 'ink') }}">
                            {{ $e->statusLabel() }}
                        </span>
                    </td>
                    <td class="text-right space-x-2">
                        <a href="{{ route('teacher.evaluations.questions.index', $e) }}" class="text-sm text-ink-600 hover:text-saffron-500">Questions</a>
                        <a href="{{ route('teacher.evaluations.edit', $e) }}" class="text-sm text-ink-600 hover:text-saffron-500">Éditer</a>
                        <form action="{{ route('teacher.evaluations.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-clay-500 hover:text-clay-600">Suppr.</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-ink-400 py-12">Aucune évaluation.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $evaluations->links() }}</div>
@endsection
