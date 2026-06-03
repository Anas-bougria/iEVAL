@php $editing = $chapter->exists; @endphp
<form action="{{ $editing ? route('admin.chapters.update', $chapter) : route('admin.modules.chapters.store', $module) }}" method="POST" class="space-y-5">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
            <label class="label">Titre du chapitre</label>
            <input name="title" required value="{{ old('title', $chapter->title) }}" class="input" placeholder="Ex: Les structures de contrôle">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="3" class="textarea">{{ old('description', $chapter->description) }}</textarea>
        </div>
        <div>
            <label class="label">Position</label>
            <input type="number" min="0" name="position" value="{{ old('position', $chapter->position) }}" class="input">
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('admin.modules.show', $module) }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Créer le chapitre' }}</button>
    </div>
</form>
