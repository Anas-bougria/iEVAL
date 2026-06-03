@extends('layouts.app')
@section('title', 'Mes résultats')
@section('section', 'Espace étudiant')

@section('content')
<div class="card overflow-hidden">
    <table class="table-academic">
        <thead><tr><th>Évaluation</th><th>Module</th><th>Note</th><th>Status</th><th>Soumise</th><th></th></tr></thead>
        <tbody>
            @forelse($attempts as $a)
                <tr>
                    <td>
                        <p class="font-medium">{{ $a->evaluation->title }}</p>
                    </td>
                    <td class="text-sm">{{ $a->evaluation->module->name }}</td>
                    <td>
                        <span class="font-display text-2xl text-ink-900">{{ number_format($a->grade_20, 2) }}</span>
                        <span class="text-xs text-ink-400">/20</span>
                    </td>
                    <td><span class="badge-ink">{{ $a->statusLabel() }}</span></td>
                    <td class="text-xs text-ink-500">{{ optional($a->submitted_at)->format('d/m/Y H:i') }}</td>
                    <td class="text-right"><a href="{{ route('student.results.show', $a) }}" class="text-sm text-saffron-500 hover:underline">Détail →</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-ink-400 py-12">Aucun résultat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $attempts->links() }}</div>
@endsection
