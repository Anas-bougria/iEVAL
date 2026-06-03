@php
    $u = auth()->user();
    $section = match(true) {
        request()->is('admin*')   => 'admin',
        request()->is('teacher*') => 'teacher',
        request()->is('student*') => 'student',
        default                   => 'home',
    };
@endphp

<aside class="w-64 shrink-0 bg-ink-900 text-paper-100 hidden lg:flex flex-col">
    <div class="px-6 py-6 border-b border-ink-700">
        <a href="{{ route($u->dashboardRoute()) }}" class="flex items-center gap-3">
            <span class="font-display text-2xl font-semibold text-saffron-300">i</span>
            <span class="font-display text-2xl font-semibold tracking-tight">EVAL</span>
        </a>
        <p class="small-caps text-ink-300 mt-1">Évaluations en ligne</p>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p class="small-caps text-ink-300 px-3 mt-2 mb-2">Navigation</p>

        @if($u->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>◆</span> Tableau de bord
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>◇</span> Utilisateurs
            </a>
            <a href="{{ route('admin.semesters.index') }}"
               class="nav-link {{ request()->routeIs('admin.semesters.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>▤</span> Semestres
            </a>
            <a href="{{ route('admin.modules.index') }}"
               class="nav-link {{ request()->routeIs('admin.modules.*') || request()->routeIs('admin.modules.chapters.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>▦</span> Modules
            </a>
        @endif

        @if($u->isTeacher())
            <a href="{{ route('teacher.dashboard') }}"
               class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>◆</span> Tableau de bord
            </a>
            <a href="{{ route('teacher.evaluations.index') }}"
               class="nav-link {{ request()->routeIs('teacher.evaluations.*') || request()->routeIs('teacher.questions.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>✎</span> Mes évaluations
            </a>
            <a href="{{ route('teacher.statistics.index') }}"
               class="nav-link {{ request()->routeIs('teacher.statistics.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>▲</span> Statistiques
            </a>
        @endif

        @if($u->isStudent())
            <a href="{{ route('student.dashboard') }}"
               class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>◆</span> Tableau de bord
            </a>
            <a href="{{ route('student.evaluations.index') }}"
               class="nav-link {{ request()->routeIs('student.evaluations.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>✎</span> Évaluations
            </a>
            <a href="{{ route('student.results.index') }}"
               class="nav-link {{ request()->routeIs('student.results.*') ? 'active' : 'text-paper-200 hover:bg-ink-800 hover:text-paper-50' }}">
                <span>★</span> Mes résultats
            </a>
        @endif
    </nav>

    <div class="px-4 py-4 border-t border-ink-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-saffron-300 text-ink-900 grid place-items-center font-display font-bold">
                {{ $u->initials() }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm text-paper-50 truncate">{{ $u->name }}</p>
                <p class="text-xs text-ink-300 capitalize">{{ $u->role }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button class="w-full text-left text-xs text-ink-300 hover:text-saffron-300 transition-colors">
                Se déconnecter →
            </button>
        </form>
    </div>
</aside>
