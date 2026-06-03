<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Public registration is *restricted to students*.
     * Admin and teacher accounts are created by an administrator
     * from the back-office (admin/users). This is a deliberate
     * security design — never let a visitor self-elect to admin.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'email'      => ['required', 'email', 'max:160', 'unique:users,email'],
            'matricule'  => ['nullable', 'string', 'max:50', 'unique:users,matricule'],
            'class'      => ['required', 'string', 'max:60'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'terms'      => ['accepted'],
        ], [
            'terms.accepted' => 'Vous devez accepter les conditions pour créer un compte.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'name'       => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'      => $data['email'],
            'matricule'  => $data['matricule'] ?? null,
            'class'      => $data['class'],
            'phone'      => $data['phone'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'role'       => User::ROLE_STUDENT, // ← forcé côté serveur, non négociable
            'password'   => Hash::make($data['password']),
            'is_active'  => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')
            ->with('status', 'Bienvenue ' . $user->first_name . ' — votre compte étudiant est créé.');
    }
}
