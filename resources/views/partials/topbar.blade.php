<header class="bg-paper-50 border-b border-ink-100 px-6 lg:px-10 py-4">
    <div class="flex items-center justify-between gap-6">
        <div class="min-w-0">
            <p class="small-caps text-ink-400">@yield('section', '')</p>
            <h1 class="text-2xl lg:text-3xl font-display font-semibold text-ink-900 truncate">
                @yield('title', 'Tableau de bord')
            </h1>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <span class="text-xs text-ink-400">{{ now()->translatedFormat('l j F Y') }}</span>
        </div>
    </div>
    @hasSection('subtitle')
        <p class="mt-2 text-sm text-ink-500 max-w-2xl">@yield('subtitle')</p>
    @endif
</header>
