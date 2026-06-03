@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('section', 'Administration')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="label">Recherche</label>
            <input name="q" value="{{ request('q') }}" class="input w-64" placeholder="Nom, e-mail, matricule…">
        </div>
        <div>
            <label class="label">Rôle</label>
            <select name="role" class="select w-40">
                <option value="">Tous</option>
                @foreach(['admin' => 'Admin', 'teacher' => 'Professeur', 'student' => 'Étudiant'] as $k => $l)
                    <option value="{{ $k }}" @selected(request('role') === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-outline">Filtrer</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Nouvel utilisateur</a>
</div>

<div class="card overflow-hidden">
    <table class="table-academic">
        <thead>
            <tr><th>Utilisateur</th><th>Matricule</th><th>Rôle</th><th>Status</th><th>Créé</th><th></th></tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-paper-200 text-ink-800 grid place-items-center text-sm font-display font-semibold">
                            {{ $user->initials() }}
                        </div>
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-xs text-ink-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="text-sm">{{ $user->matricule ?? '—' }}</td>
                <td><span class="badge-{{ $user->role === 'admin' ? 'clay' : ($user->role === 'teacher' ? 'saffron' : 'ink') }} capitalize">{{ $user->role }}</span></td>
                <td>
                    @if($user->is_active)
                        <span class="badge-green">● actif</span>
                    @else
                        <span class="badge-ink">○ inactif</span>
                    @endif
                </td>
                <td class="text-xs text-ink-500">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="text-right">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-ink-600 hover:text-saffron-500">Éditer</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-clay-500 hover:text-clay-600">Suppr.</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-ink-400 py-12">Aucun utilisateur trouvé.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $users->links() }}</div>
@endsection
