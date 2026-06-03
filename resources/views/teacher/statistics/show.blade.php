@extends('layouts.app')
@section('title', 'Statistiques — ' . $evaluation->title)
@section('section', 'Enseignant · Analyse')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Tentatives', 'value' => $count, 'sub' => 'soumissions'],
            ['label' => 'Moyenne',    'value' => $avg !== null ? $avg.'/20' : '—', 'sub' => 'sur 20'],
            ['label' => 'Max',        'value' => $max !== null ? $max.'/20' : '—', 'sub' => 'meilleure note'],
            ['label' => 'Min',        'value' => $min !== null ? $min.'/20' : '—', 'sub' => 'plus basse'],
        ];
    @endphp
    @foreach($cards as $c)
        <div class="card card-pad">
            <p class="small-caps text-ink-500">{{ $c['label'] }}</p>
            <p class="font-display text-3xl text-ink-900 mt-2">{{ $c['value'] }}</p>
            <p class="text-xs text-ink-400 mt-1">{{ $c['sub'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="card card-pad">
        <h3 class="font-display text-lg mb-4">Distribution des notes</h3>
        <canvas id="chart-distribution" height="180"></canvas>
    </div>
    <div class="card card-pad">
        <h3 class="font-display text-lg mb-4">Taux d'assimilation par chapitre</h3>
        <canvas id="chart-chapters" height="180"></canvas>
    </div>
</div>

<div class="card card-pad">
    <h3 class="font-display text-lg mb-4">Détail par étudiant</h3>
    <table class="table-academic">
        <thead><tr><th>Étudiant</th><th>Note</th><th>Score</th><th>Status</th><th>Soumise</th></tr></thead>
        <tbody>
            @forelse($attempts as $a)
                <tr>
                    <td>
                        <p class="font-medium">{{ $a->student->name }}</p>
                        <p class="text-xs text-ink-400">{{ $a->student->email }}</p>
                    </td>
                    <td><span class="font-display text-xl">{{ number_format($a->grade_20, 2) }}</span><span class="text-xs text-ink-400">/20</span></td>
                    <td class="text-sm">{{ number_format($a->score, 2) }} / {{ number_format($a->max_score, 2) }}</td>
                    <td><span class="badge-ink">{{ $a->statusLabel() }}</span></td>
                    <td class="text-xs text-ink-500">{{ optional($a->submitted_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-ink-400 py-8">Aucune tentative.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const C = window.iEvalChartColors;

    new Chart(document.getElementById('chart-distribution'), {
        type: 'bar',
        data: {
            labels: ['0–4', '4–8', '8–12', '12–16', '16–20'],
            datasets: [{
                label: 'Nombre d\'étudiants',
                data: @json($distribution),
                backgroundColor: [C.danger, C.clay, C.muted, C.saffron, C.success],
                borderRadius: 4,
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } },
        }
    });

    new Chart(document.getElementById('chart-chapters'), {
        type: 'bar',
        data: {
            labels: @json(array_column($perChapter, 'chapter')),
            datasets: [{
                label: '% bonnes réponses',
                data: @json(array_column($perChapter, 'rate')),
                backgroundColor: C.saffron,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            scales: { x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } },
            plugins: { legend: { display: false } },
        }
    });
});
</script>
@endpush
@endsection
