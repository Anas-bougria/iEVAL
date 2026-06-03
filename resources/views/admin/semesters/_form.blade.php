@php $editing = $semester->exists; @endphp
<form action="{{ $editing ? route('admin.semesters.update', $semester) : route('admin.semesters.store') }}" method="POST" class="space-y-5">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="label">Nom</label>
            <input name="name" required value="{{ old('name', $semester->name) }}" class="input" placeholder="S1 2025-2026">
        </div>
        <div>
            <label class="label">Code</label>
            <input name="code" required value="{{ old('code', $semester->code) }}" class="input" placeholder="S1-2025">
        </div>
        <div>
            <label class="label">Date de début</label>
            <input type="date" name="start_date" required value="{{ old('start_date', optional($semester->start_date)->format('Y-m-d')) }}" class="input">
        </div>
        <div>
            <label class="label">Date de fin</label>
            <input type="date" name="end_date" required value="{{ old('end_date', optional($semester->end_date)->format('Y-m-d')) }}" class="input">
        </div>
        <label class="sm:col-span-2 flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $semester->is_current))
                   class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
            Semestre actuel (un seul à la fois)
        </label>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('admin.semesters.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Créer' }}</button>
    </div>
</form>
