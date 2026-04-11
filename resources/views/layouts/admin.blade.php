<!DOCTYPE html>
<html lang="fr" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — eCompta360</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .admin-link { @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all; }
        .admin-link:hover { @apply bg-white/10; }
        .admin-link.active { @apply bg-yellow-500/20 text-yellow-300; }
        .admin-link:not(.active) { @apply text-gray-400; }
    </style>
    @stack('head')
</head>

<body class="bg-gray-100 text-gray-800" x-cloak>
<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ADMIN ══════════════════════════════════════════════ --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <div class="w-9 h-9 bg-yellow-500 rounded-xl flex items-center justify-center font-black text-lg text-gray-900 flex-shrink-0">e</div>
            <div>
                <div class="font-bold text-white text-sm leading-tight">eCompta360</div>
                <div class="text-yellow-400 text-xs font-semibold tracking-wide">ESPACE DRAWIS</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="admin-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Tableau de bord
            </a>

            {{-- Cabinets --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-gray-600 text-xs uppercase tracking-widest font-semibold">Cabinets</p>
            </div>
            <a href="{{ route('admin.tenants.index') }}"
               class="admin-link {{ request()->routeIs('admin.tenants.index') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Tous les cabinets
            </a>
            <a href="{{ route('admin.tenants.create') }}"
               class="admin-link {{ request()->routeIs('admin.tenants.create') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau cabinet
            </a>

            {{-- Abonnements --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-gray-600 text-xs uppercase tracking-widest font-semibold">Abonnements</p>
            </div>
            <a href="{{ route('admin.abonnements.index') }}"
               class="admin-link {{ request()->routeIs('admin.abonnements.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Abonnements & Paiements
            </a>
            <a href="{{ route('admin.plans.index') }}"
               class="admin-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Plans tarifaires
            </a>

            {{-- Surveillance --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-gray-600 text-xs uppercase tracking-widest font-semibold">Surveillance</p>
            </div>
            <a href="{{ route('admin.monitoring') }}"
               class="admin-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Monitoring IA & OCR
            </a>
            <a href="{{ route('admin.quotas') }}"
               class="admin-link {{ request()->routeIs('admin.quotas') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Gestion des quotas
            </a>

            {{-- Utilisateurs --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-gray-600 text-xs uppercase tracking-widest font-semibold">Accès</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="admin-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Utilisateurs globaux
            </a>

        </nav>

        {{-- Liens bas + User --}}
        <div class="border-t border-white/10 px-3 py-3 space-y-1">
            <a href="{{ route('landing') }}" target="_blank"
               class="admin-link text-gray-500 hover:text-gray-300">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Site public
            </a>

            <div x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition">
                    <div class="w-7 h-7 bg-yellow-500 rounded-full flex items-center justify-center text-xs font-black text-gray-900 flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 text-left min-w-0">
                        <div class="text-white text-xs font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-yellow-400 text-xs">Super Admin</div>
                    </div>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="mt-1 bg-gray-800 rounded-lg overflow-hidden">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-gray-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>

    {{-- ══ ZONE PRINCIPALE ════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 sm:px-6 sticky top-0 z-30 gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-gray-500 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="flex-1 text-base font-semibold text-gray-800">@yield('page-title', 'Administration')</h1>
            <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                DRAWIS
            </span>
        </header>

        {{-- Flash --}}
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if(session('succes'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)"
                     class="flex justify-between bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    <span>✅ {{ session('succes') }}</span>
                    <button @click="show=false" class="ml-4 text-green-500">✕</button>
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
                    ℹ️ {{ session('info') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif
        </div>

        {{-- Contenu --}}
        <main class="flex-1 px-4 sm:px-6 py-5 overflow-y-auto">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-100 py-3 text-center text-xs text-gray-400">
            eCompta360 Admin — DRAWIS &copy; {{ date('Y') }}
        </footer>
    </div>
</div>
@stack('scripts')
</body>
</html>
