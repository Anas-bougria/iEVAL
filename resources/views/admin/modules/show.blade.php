@extends('layouts.app')
@section('title', $module->name)
@section('section', 'Administration · Module')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-6">
        <div class="card card-pad">
            <div class="flex items-start justify-between">
                <div>
                    <p class="small-caps text-ink-500">{{ $module->code }}</p>
                    <h2 class="font-display text-3xl mt-1">{{ $module->name }}</h2>
                    <p class="text-ink-600 mt-2">{{ $module->description ?? '—' }}</p>
                </div>
                <a href="{{ route('admin.modules.edit', $module) }}" class="btn-outline">Éditer</a>
            </div>
            <div class="rule"></div>
            <div class="grid grid-cols-3 gap-6 text-sm">
                <div><p class="small-caps text-ink-400">Semestre</p><p>{{ $module->semester->name ?? '—' }}</p></div>
                <div><p class="small-caps text-ink-400">Enseignant</p><p>{{ $module->teacher->name ?? '—' }}</p></div>
                <div><p class="small-caps text-ink-400">Étudiants</p><p>{{ $module->students->count() }}</p></div>
            </div>
        </div>

        <div class="card card-pad">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-xl">Chapitres</h3>
                <a href="{{ route('admin.modules.chapters.create', $module) }}" class="btn-accent">+ Ajouter</a>
            </div>
            @forelse($module->chapters as $c)
                <div class="border-b border-ink-100 last:border-0 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $c->position }}. {{ $c->title }}</p>
                        @if($c->description)<p class="text-xs text-ink-400">{{ $c->description }}</p>@endif
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.chapters.edit', $c) }}" class="text-sm text-ink-600 hover:text-saffron-500">Éditer</a>
                        <form action="{{ route('admin.chapters.destroy', $c) }}" method="POST" onsubmit="return confirm('Supprimer ce chapitre ?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-clay-500">Suppr.</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-ink-400 py-4 text-center">Aucun chapitre.</p>
            @endforelse
        </div>

        <div class="card card-pad">
            <h3 class="font-display text-xl mb-4">Évaluations</h3>
            @forelse($module->evaluations as $e)
                <div class="border-b border-ink-100 last:border-0 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $e->title }}</p>
                        <p class="text-xs text-ink-400">{{ $e->duration_minutes }} min</p>
                    </div>
                    <span class="badge-{{ $e->status === 'published' ? 'saffron' : ($e->status === 'closed' ? 'clay' : 'ink') }}">
                        {{ $e->statusLabel() }}
                    </span>
                </div>
            @empty
                <p class="text-ink-400 py-4 text-center">Aucune évaluation.</p>
            @endforelse
        </div>
    </section>

    <aside class="card card-pad">
        <h3 class="font-display text-xl mb-4">Inscriptions</h3>

        <form action="{{ route('admin.modules.enroll', $module) }}" method="POST" class="space-y-3 mb-6">
            @csrf
            <label class="label">Ajouter des étudiants</label>
            <select name="student_ids[]" multiple class="select h-32">
                @foreach($allStudents as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} — {{ $s->matricule ?? $s->email }}</option>
                @endforeach
            </select>
            <button class="btn-primary w-full">Inscrire</button>
        </form>

        <p class="small-caps text-ink-500 mb-2">Inscrits ({{ $module->students->count() }})</p>
        <ul class="space-y-1 max-h-80 overflow-auto">
            @forelse($module->students as $st)
                <li class="flex items-center justify-between text-sm py-1.5 border-b border-ink-100 last:border-0">
                    <span>{{ $st->name }}</span>
                    <form action="{{ route('admin.modules.unenroll', $module) }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $st->id }}">
                        <button class="text-xs text-clay-500 hover:text-clay-600">retirer</button>
                    </form>
                </li>
            @empty
                <li class="text-ink-400 text-sm">Aucun inscrit.</li>
            @endforelse
        </ul>
    </aside>
</div>
@endsection
