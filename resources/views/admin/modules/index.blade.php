@extends('layouts.app')
@section('title', 'Modules')
@section('section', 'Administration')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-3 items-end">
        <div><label class="label">Recherche</label><input name="q" value="{{ request('q') }}" class="input w-56"></div>
        <div><label class="label">Semestre</label>
            <select name="semester" class="select w-48">
                <option value="">Tous</option>
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" @selected(request('semester') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-outline">Filtrer</button>
    </form>
    <a href="{{ route('admin.modules.create') }}" class="btn-primary">+ Nouveau module</a>
</div>

<div class="card overflow-hidden">
    <table class="table-academic">
        <thead><tr><th>Code</th><th>Module</th><th>Semestre</th><th>Enseignant</th><th>Chapitres</th><th>Étudiants</th><th>Évaluations</th><th></th></tr></thead>
        <tbody>
            @forelse($modules as $m)
                <tr>
                    <td><code class="text-xs">{{ $m->code }}</code></td>
                    <td><a href="{{ route('admin.modules.show', $m) }}" class="font-medium hover:text-saffron-500">{{ $m->name }}</a></td>
                    <td class="text-sm">{{ $m->semester->name ?? '—' }}</td>
                    <td class="text-sm">{{ $m->teacher->name ?? '—' }}</td>
                    <td>{{ $m->chapters_count }}</td>
                    <td>{{ $m->students_count }}</td>
                    <td>{{ $m->evaluations_count }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.modules.edit', $m) }}" class="text-sm text-ink-600 hover:text-saffron-500">Éditer</a>
                        <form action="{{ route('admin.modules.destroy', $m) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-clay-500 hover:text-clay-600">Suppr.</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-ink-400 py-12">Aucun module.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $modules->links() }}</div>
@endsection
