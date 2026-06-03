<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>403 — Accès refusé · iEVAL</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md px-6">
        <p class="font-display text-9xl text-saffron-300 leading-none">403</p>
        <h1 class="font-display text-3xl text-ink-900 mt-4">Accès refusé</h1>
        <p class="text-ink-600 mt-2">{{ $exception->getMessage() ?: 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.' }}</p>
        <a href="{{ url('/') }}" class="btn-primary mt-6 inline-flex">Retour à l'accueil →</a>
    </div>
</body>
</html>
