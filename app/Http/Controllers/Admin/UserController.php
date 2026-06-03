<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = User::query()->latest();

        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('matricule', 'like', "%$search%");
            });
        }
        if ($role = $request->get('role')) {
            $q->where('role', $role);
        }

        $users = $q->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User(['role' => 'student'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('admin.users.index')->with('status', 'Utilisateur créé.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user->id);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('status', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Impossible de supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('status', 'Utilisateur supprimé.');
    }

    protected function validated(Request $request, ?int $userId = null): array
    {
        return $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'matricule' => ['nullable', 'string', 'max:50', Rule::unique('users', 'matricule')->ignore($userId)],
            'role'      => ['required', Rule::in(['admin', 'teacher', 'student'])],
            'password'  => [$userId ? 'nullable' : 'required', 'string', 'min:6'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    public function show(User $user): View
    {
        return $this->edit($user);
    }
}
