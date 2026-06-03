@extends('layouts.app')
@section('title', 'Connexion')

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
            <p class="font-display italic text-paper-200 mt-2 text-lg">Plateforme de gestion des évaluations en ligne</p>
        </div>

        <div class="relative">
            <blockquote class="font-display italic text-2xl xl:text-3xl text-paper-100 leading-snug max-w-md">
                « Apprendre, c'est bâtir des passerelles entre ce que l'on sait et ce que l'on découvre. »
            </blockquote>
            <p class="small-caps text-ink-300 mt-4">Projet de fin d'études · DAI</p>
        </div>
    </aside>

    {{-- Login form --}}
    <main class="flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-8 text-center">
                <h1 class="font-display text-4xl text-ink-900"><span class="text-saffron-500">i</span>EVAL</h1>
                <p class="small-caps text-ink-500 mt-1">BTS Ibn Sina – Kénitra</p>
            </div>

            <div class="mb-8">
                <p class="small-caps text-ink-500">Espace personnel</p>
                <h2 class="font-display text-4xl text-ink-900 mt-1">Connexion</h2>
                <p class="text-ink-600 mt-2">Accédez à vos évaluations, vos résultats, ou votre espace de gestion.</p>
            </div>

            @if(session('status'))
                <div class="mb-5 rounded-md bg-saffron-50 border border-saffron-200 px-4 py-2.5 text-sm text-ink-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-md bg-clay-400/10 border border-clay-400/40 px-4 py-2.5 text-sm text-clay-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="label">Adresse e-mail</label>
                    <input type="email" name="email" id="email" required autofocus
                           value="{{ old('email') }}"
                           class="input" placeholder="vous@bts-ibnsina.ma">
                </div>
                <div>
                    <label for="password" class="label">Mot de passe</label>
                    <input type="password" name="password" id="password" required class="input">
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-600">
                    <input type="checkbox" name="remember" class="rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                    Se souvenir de moi
                </label>
                <button type="submit" class="btn-primary w-full justify-center">
                    Se connecter →
                </button>
            </form>

            <div class="rule"></div>

            <p class="text-center text-sm text-ink-600">
                Nouveau sur iEVAL ?
                <a href="{{ route('register') }}" class="text-ink-900 hover:text-saffron-500 font-medium">
                    Créer un compte étudiant →
                </a>
            </p>

            <p class="text-xs text-ink-400 text-center mt-6">
                © {{ date('Y') }} iEVAL · projet PFE encadré par <span class="text-ink-600">M. Hamid Alhaiane</span>
            </p>
        </div>
    </main>
</div>
@endsection
