@extends('layouts.app')
@section('title', 'Semestres')
@section('section', 'Administration')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-ink-500">Périodes académiques de l'établissement.</p>
    <a href="{{ route('admin.semesters.create') }}" class="btn-primary">+ Nouveau semestre</a>
</div>

<div class="card overflow-hidden">
    <table class="table-academic">
        <thead><tr><th>Nom</th><th>Code</th><th>Période</th><th>Modules</th><th></th><th></th></tr></thead>
        <tbody>
            @forelse($semesters as $s)
                <tr>
                    <td class="font-medium">{{ $s->name }}</td>
                    <td><code class="text-xs">{{ $s->code }}</code></td>
                    <td class="text-sm">{{ $s->start_date->format('d/m/Y') }} → {{ $s->end_date->format('d/m/Y') }}</td>
                    <td>{{ $s->modules_count }}</td>
                    <td>@if($s->is_current)<span class="badge-saffron">● en cours</span>@endif</td>
                    <td class="text-right">
                        <a href="{{ route('admin.semesters.edit', $s) }}" class="text-sm text-ink-600 hover:text-saffron-500">Éditer</a>
                        <form action="{{ route('admin.semesters.destroy', $s) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-clay-500 hover:text-clay-600">Suppr.</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-ink-400 py-12">Aucun semestre.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $semesters->links() }}</div>
@endsection
