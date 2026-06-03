@extends('layouts.app')
@section('title', $evaluation->title)
@section('section', 'Enseignant · Évaluation')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-6">
        <div class="card card-pad">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="small-caps text-ink-500">{{ $evaluation->module->name ?? '—' }}</p>
                    <h2 class="font-display text-3xl mt-1">{{ $evaluation->title }}</h2>
                    <p class="text-ink-600 mt-2">{{ $evaluation->description ?? '—' }}</p>
                </div>
                <span class="badge-{{ $evaluation->status === 'published' ? 'saffron' : ($evaluation->status === 'closed' ? 'clay' : 'ink') }} shrink-0">
                    {{ $evaluation->statusLabel() }}
                </span>
            </div>

            <div class="rule"></div>

            <div class="grid sm:grid-cols-4 gap-6 text-sm">
                <div><p class="small-caps text-ink-400">Durée</p><p>{{ $evaluation->duration_minutes }} min</p></div>
                <div><p class="small-caps text-ink-400">Tentatives</p><p>{{ $evaluation->max_attempts }} max</p></div>
                <div><p class="small-caps text-ink-400">Questions</p><p>{{ $evaluation->questions->count() }}</p></div>
                <div><p class="small-caps text-ink-400">Total points</p><p>{{ number_format($evaluation->totalPoints(), 2) }}</p></div>
                @if($evaluation->opens_at)<div><p class="small-caps text-ink-400">Ouverture</p><p>{{ $evaluation->opens_at->format('d/m/Y H:i') }}</p></div>@endif
                @if($evaluation->closes_at)<div><p class="small-caps text-ink-400">Fermeture</p><p>{{ $evaluation->closes_at->format('d/m/Y H:i') }}</p></div>@endif
            </div>

            @if($evaluation->instructions)
                <div class="rule"></div>
                <p class="small-caps text-ink-400 mb-2">Instructions</p>
                <p class="text-ink-700 whitespace-pre-line">{{ $evaluation->instructions }}</p>
            @endif
        </div>

        <div class="card card-pad">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-xl">Questions ({{ $evaluation->questions->count() }})</h3>
                <a href="{{ route('teacher.evaluations.questions.create', $evaluation) }}" class="btn-accent">+ Ajouter</a>
            </div>

            @forelse($evaluation->questions as $q)
                <div class="border border-ink-100 rounded-md p-4 mb-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="small-caps text-ink-400">Q{{ $loop->iteration }}</span>
                                <span class="badge-ink">{{ $q->type === 'single' ? 'QCU' : 'QCM' }}</span>
                                <span class="text-xs text-ink-500">{{ number_format($q->points, 2) }} pt</span>
                                @if($q->chapter)<span class="badge-saffron">{{ $q->chapter->title }}</span>@endif
                            </div>
                            <p class="text-ink-800">{{ $q->statement }}</p>
                            <ul class="mt-3 space-y-1">
                                @foreach($q->options as $o)
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="{{ $o->is_correct ? 'text-emerald-700 font-medium' : 'text-ink-600' }}">
                                            {{ $o->is_correct ? '✓' : '○' }} {{ $o->text }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 shrink-0">
                            <a href="{{ route('teacher.questions.edit', $q) }}" class="text-xs text-ink-600 hover:text-saffron-500">Éditer</a>
                            <form action="{{ route('teacher.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-clay-500">Suppr.</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-ink-400 py-6 text-center">Aucune question. <a href="{{ route('teacher.evaluations.questions.create', $evaluation) }}" class="text-saffron-500 hover:underline">En ajouter →</a></p>
            @endforelse
        </div>
    </section>

    <aside class="space-y-4">
        <div class="card card-pad">
            <h3 class="font-display text-lg mb-3">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('teacher.evaluations.edit', $evaluation) }}" class="btn-outline w-full">Modifier les paramètres</a>

                @if($evaluation->status !== 'published')
                    <form action="{{ route('teacher.evaluations.publish', $evaluation) }}" method="POST">
                        @csrf
                        <button class="btn-accent w-full">Publier l'évaluation</button>
                    </form>
                @endif

                @if($evaluation->status === 'published')
                    <form action="{{ route('teacher.evaluations.close', $evaluation) }}" method="POST">
                        @csrf
                        <button class="btn-outline w-full">Clôturer</button>
                    </form>
                @endif

                <form action="{{ route('teacher.evaluations.duplicate', $evaluation) }}" method="POST">
                    @csrf
                    <button class="btn-ghost w-full">Dupliquer</button>
                </form>

                <a href="{{ route('teacher.statistics.show', $evaluation) }}" class="btn-ghost w-full text-center block">Voir les statistiques</a>

                <form action="{{ route('teacher.evaluations.destroy', $evaluation) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')">
                    @csrf @method('DELETE')
                    <button class="btn-danger w-full">Supprimer</button>
                </form>
            </div>
        </div>
    </aside>
</div>
@endsection
