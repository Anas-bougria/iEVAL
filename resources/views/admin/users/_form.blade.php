@php $editing = $user->exists; @endphp
<form action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="space-y-5">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="label">Nom complet</label>
            <input name="name" required value="{{ old('name', $user->name) }}" class="input">
        </div>
        <div>
            <label class="label">Adresse e-mail</label>
            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="input">
        </div>
        <div>
            <label class="label">Matricule</label>
            <input name="matricule" value="{{ old('matricule', $user->matricule) }}" class="input" placeholder="ex: BTS-2026-007">
        </div>
        <div>
            <label class="label">Rôle</label>
            <select name="role" class="select" required>
                @foreach(['admin' => 'Administrateur', 'teacher' => 'Professeur', 'student' => 'Étudiant'] as $k => $l)
                    <option value="{{ $k }}" @selected(old('role', $user->role) === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Mot de passe {{ $editing ? '(laisser vide pour conserver)' : '' }}</label>
            <input type="password" name="password" {{ $editing ? '' : 'required' }} class="input">
        </div>
        <label class="sm:col-span-2 flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))
                   class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
            Compte actif
        </label>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Créer l\'utilisateur' }}</button>
    </div>
</form>
