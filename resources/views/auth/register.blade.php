@extends('layouts.app')
@section('title', 'Créer un compte')

@section('content')
<div class="min-h-screen grid lg:grid-cols-2">
    {{-- Editorial left panel --}}
    <aside class="hidden lg:flex flex-col justify-between bg-ink-900 text-paper-100 px-12 py-10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-grid-paper" style="background-size: 32px 32px;"></div>

        <div class="relative">
            <p class="small-caps text-saffron-300">BTS Ibn Sina · Kénitra</p>
            <h1 class="font-display text-6xl xl:text-7xl mt-3 leading-[0.95] text-paper-50">
                <span class="text-saffron-300">i</span>EVAL
            </h1>
            <p class="font-display italic text-paper-200 mt-2 text-lg">Rejoignez l'espace étudiant</p>
        </div>

        <div class="relative space-y-6">
            <blockquote class="font-display italic text-2xl xl:text-3xl text-paper-100 leading-snug max-w-md">
                « Chaque évaluation est une étape — un repère sur le chemin de ce que l'on devient. »
            </blockquote>
            <div class="border-l-2 border-saffron-300 pl-4 max-w-md">
                <p class="text-sm text-paper-200 leading-relaxed">
                    L'inscription est réservée aux <strong class="text-saffron-300">étudiants</strong>.
                    Les comptes enseignants et administrateurs sont créés par l'établissement.
                </p>
            </div>
            <p class="small-caps text-ink-300">Projet de fin d'études · DAI</p>
        </div>
    </aside>

    {{-- Register form --}}
    <main class="flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-xl">
            <div class="lg:hidden mb-8 text-center">
                <h1 class="font-display text-4xl text-ink-900"><span class="text-saffron-500">i</span>EVAL</h1>
                <p class="small-caps text-ink-500 mt-1">BTS Ibn Sina – Kénitra</p>
            </div>

            <div class="mb-8">
                <p class="small-caps text-ink-500">Espace étudiant</p>
                <h2 class="font-display text-4xl text-ink-900 mt-1">Créer un compte</h2>
                <p class="text-ink-600 mt-2">Quelques informations pour rejoindre la plateforme.</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-md bg-clay-400/10 border border-clay-400/40 px-4 py-3 text-sm text-clay-600">
                    <p class="font-semibold mb-1">Veuillez corriger :</p>
                    <ul class="list-disc ml-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Identité --}}
                <fieldset class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="label">Prénom <span class="text-clay-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" required autofocus
                               value="{{ old('first_name') }}" class="input" placeholder="Yassine">
                    </div>
                    <div>
                        <label for="last_name" class="label">Nom <span class="text-clay-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required
                               value="{{ old('last_name') }}" class="input" placeholder="Berrada">
                    </div>
                </fieldset>

                {{-- Identifiants --}}
                <div>
                    <label for="email" class="label">Adresse e-mail <span class="text-clay-500">*</span></label>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email') }}" class="input" placeholder="vous@bts-ibnsina.ma">
                </div>

                <fieldset class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="matricule" class="label">Matricule</label>
                        <input type="text" id="matricule" name="matricule"
                               value="{{ old('matricule') }}" class="input" placeholder="BTS-2026-007">
                        <p class="text-xs text-ink-400 mt-1">Optionnel — si vous l'avez déjà reçu</p>
                    </div>
                    <div>
                        <label for="class" class="label">Classe / Filière <span class="text-clay-500">*</span></label>
                        <input type="text" id="class" name="class" required
                               value="{{ old('class') }}" class="input" placeholder="DAI-2">
                    </div>
                </fieldset>

                {{-- Coordonnées --}}
                <fieldset class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="label">Téléphone</label>
                        <input type="tel" id="phone" name="phone"
                               value="{{ old('phone') }}" class="input" placeholder="+212 6 12 34 56 78">
                    </div>
                    <div>
                        <label for="birth_date" class="label">Date de naissance</label>
                        <input type="date" id="birth_date" name="birth_date"
                               value="{{ old('birth_date') }}" class="input"
                               max="{{ now()->subYears(15)->toDateString() }}">
                    </div>
                </fieldset>

                <div class="rule"></div>

                {{-- Mot de passe --}}
                <fieldset class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="label">Mot de passe <span class="text-clay-500">*</span></label>
                        <input type="password" id="password" name="password" required minlength="8" class="input">
                        <p class="text-xs text-ink-400 mt-1">8 caractères minimum</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Confirmer <span class="text-clay-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               required minlength="8" class="input">
                    </div>
                </fieldset>

                {{-- Rôle (informatif, non éditable) --}}
                <div class="rounded-md border border-saffron-200 bg-saffron-50 px-4 py-3 flex items-start gap-3">
                    <span class="text-saffron-500 mt-0.5">●</span>
                    <div class="text-sm text-ink-700">
                        <p><strong>Compte étudiant</strong></p>
                        <p class="text-ink-500 text-xs mt-0.5">
                            Les enseignants et administrateurs ne s'inscrivent pas ici — leurs comptes sont
                            créés par l'établissement et leur seront communiqués directement.
                        </p>
                    </div>
                </div>

                <label class="flex items-start gap-2 text-sm text-ink-600">
                    <input type="checkbox" name="terms" value="1" required
                           class="mt-0.5 rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                    <span>
                        J'accepte de partager mes informations avec l'établissement
                        <strong>BTS Ibn Sina Kénitra</strong> dans le cadre du suivi pédagogique.
                    </span>
                </label>

                <button type="submit" class="btn-primary w-full justify-center text-base py-3">
                    Créer mon compte →
                </button>
            </form>

            <div class="rule"></div>

            <p class="text-center text-sm text-ink-600">
                Vous avez déjà un compte ?
                <a href="{{ route('login') }}" class="text-ink-900 hover:text-saffron-500 font-medium">
                    Se connecter
                </a>
            </p>

            <p class="text-xs text-ink-400 text-center mt-6">
                © {{ date('Y') }} iEVAL · projet PFE encadré par <span class="text-ink-600">M. Hamid Alhaiane</span>
            </p>
        </div>
    </main>
</div>
@endsection
