@php $editing = $evaluation->exists; @endphp
<form action="{{ $editing ? route('teacher.evaluations.update', $evaluation) : route('teacher.evaluations.store') }}" method="POST" class="space-y-6">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
            <label class="label">Titre</label>
            <input name="title" required value="{{ old('title', $evaluation->title) }}" class="input">
        </div>
        <div>
            <label class="label">Module</label>
            <select name="module_id" required class="select">
                @foreach($modules as $m)
                    <option value="{{ $m->id }}" @selected(old('module_id', $evaluation->module_id) == $m->id)>{{ $m->code }} — {{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Durée (minutes)</label>
            <input type="number" name="duration_minutes" min="1" max="480" required
                   value="{{ old('duration_minutes', $evaluation->duration_minutes) }}" class="input">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="2" class="textarea">{{ old('description', $evaluation->description) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Instructions pour les étudiants</label>
            <textarea name="instructions" rows="3" class="textarea">{{ old('instructions', $evaluation->instructions) }}</textarea>
        </div>

        <div>
            <label class="label">Tentatives maximales</label>
            <input type="number" name="max_attempts" min="1" max="10" required
                   value="{{ old('max_attempts', $evaluation->max_attempts ?? 1) }}" class="input">
        </div>
        <div>
            <label class="label">Status</label>
            <select name="status" class="select">
                @foreach(['draft' => 'Brouillon', 'published' => 'Publiée', 'closed' => 'Clôturée'] as $k => $l)
                    <option value="{{ $k }}" @selected(old('status', $evaluation->status ?? 'draft') === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Ouverture</label>
            <input type="datetime-local" name="opens_at" class="input"
                   value="{{ old('opens_at', optional($evaluation->opens_at)->format('Y-m-d\TH:i')) }}">
        </div>
        <div>
            <label class="label">Fermeture</label>
            <input type="datetime-local" name="closes_at" class="input"
                   value="{{ old('closes_at', optional($evaluation->closes_at)->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="sm:col-span-2 space-y-2 pt-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="shuffle_questions" value="1"
                       @checked(old('shuffle_questions', $evaluation->shuffle_questions ?? true))
                       class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                Mélanger l'ordre des questions
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="shuffle_options" value="1"
                       @checked(old('shuffle_options', $evaluation->shuffle_options ?? true))
                       class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                Mélanger l'ordre des options
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="show_results_immediately" value="1"
                       @checked(old('show_results_immediately', $evaluation->show_results_immediately ?? true))
                       class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                Afficher la correction à l'étudiant immédiatement après soumission
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2 border-t border-ink-100">
        <a href="{{ route('teacher.evaluations.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Créer & ajouter des questions' }}</button>
    </div>
</form>
