<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'iEVAL') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    @auth
    <div class="min-h-screen flex">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('partials.topbar')

            <main class="flex-1 p-6 lg:p-10">
                @include('partials.flash')
                @yield('content')
            </main>

            <footer class="px-6 lg:px-10 py-6 text-xs text-ink-400 border-t border-ink-100">
                <div class="flex justify-between items-center">
                    <span>iEVAL · BTS Ibn Sina – Kénitra · Projet PFE</span>
                    <span>Encadré par M. Hamid Alhaiane</span>
                </div>
            </footer>
        </div>
    </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')
</body>
</html>
