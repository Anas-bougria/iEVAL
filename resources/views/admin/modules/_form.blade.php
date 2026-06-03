@php $editing = $module->exists; @endphp
<form action="{{ $editing ? route('admin.modules.update', $module) : route('admin.modules.store') }}" method="POST" class="space-y-5">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="label">Code</label>
            <input name="code" required value="{{ old('code', $module->code) }}" class="input" placeholder="M01">
        </div>
        <div>
            <label class="label">Nom</label>
            <input name="name" required value="{{ old('name', $module->name) }}" class="input" placeholder="Programmation Web">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="3" class="textarea">{{ old('description', $module->description) }}</textarea>
        </div>
        <div>
            <label class="label">Semestre</label>
            <select name="semester_id" required class="select">
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" @selected(old('semester_id', $module->semester_id) == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Enseignant responsable</label>
            <select name="teacher_id" class="select">
                <option value="">— Non assigné —</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" @selected(old('teacher_id', $module->teacher_id) == $t->id)>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('admin.modules.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Créer le module' }}</button>
    </div>
</form>
