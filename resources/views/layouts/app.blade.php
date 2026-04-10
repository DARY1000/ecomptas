<!DOCTYPE html>
<html lang="fr" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — eCompta360</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .nav-link { @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all; }
        .nav-link:hover { @apply bg-white/10; }
        .nav-link.active { @apply bg-white/20 text-white; }
        .nav-link:not(.active) { @apply text-blue-200; }
    </style>
    @stack('head')
</head>

<body class="bg-gray-50 text-gray-800" x-cloak>

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ═══════════════════════════════════════════════════════ --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white flex flex-col transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <div class="w-9 h-9 bg-blue-400 rounded-xl flex items-center justify-center font-black text-lg flex-shrink-0">e</div>
            <div>
                <div class="font-bold text-white text-base leading-tight">eCompta360</div>
                @if(auth()->user()?->tenant)
                    <div class="text-blue-300 text-xs truncate max-w-[140px]">{{ auth()->user()->tenant->nom }}</div>
                @else
                    <div class="text-blue-300 text-xs">Administration</div>
                @endif
            </div>
        </div>

        {{-- Navigation principale --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Tableau de bord
            </a>

            <a href="{{ route('factures.index') }}"
               class="nav-link {{ request()->routeIs('factures.index') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Factures
            </a>

            @if(!auth()->user()->estAuditeur())
            <a href="{{ route('factures.upload') }}"
               class="nav-link {{ request()->routeIs('factures.upload') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload facture
            </a>
            @endif

            <a href="{{ route('ecritures.index') }}"
               class="nav-link {{ request()->routeIs('ecritures.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Journal des écritures
            </a>

            @if(auth()->user()?->tenant?->plan === 'starter' || auth()->user()?->tenant?->plan === 'pro' || auth()->user()?->tenant?->plan === 'cabinet')
            <a href="{{ route('export.xlsx') }}"
               class="nav-link">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter
            </a>
            @endif

            {{-- Séparateur : Admin cabinet --}}
            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1">
                <p class="text-blue-400 text-xs uppercase tracking-widest px-3 font-semibold">Cabinet</p>
            </div>

            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Utilisateurs
            </a>

            <a href="{{ route('settings.index') }}"
               class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Paramètres
            </a>
            @endif

            {{-- Abonnement --}}
            <div class="pt-3 pb-1">
                <p class="text-blue-400 text-xs uppercase tracking-widest px-3 font-semibold">Compte</p>
            </div>

            <a href="{{ route('abonnement.index') }}"
               class="nav-link {{ request()->routeIs('abonnement.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Mon abonnement
            </a>

        </nav>

        {{-- Quota bar --}}
        @php $tenant = auth()->user()?->tenant; @endphp
        @if($tenant)
            @php
                $used  = $tenant->facturesCeMois();
                $max   = $tenant->quota_factures_mensuel;
                $pct   = $max > 0 ? min(100, round($used / $max * 100)) : 0;
            @endphp
            <div class="px-4 py-3 border-t border-white/10">
                <div class="flex justify-between text-xs text-blue-300 mb-1">
                    <span>Quota ce mois</span>
                    <span class="{{ $pct >= 80 ? 'text-yellow-300 font-bold' : '' }}">{{ $used }}/{{ $max }}</span>
                </div>
                <div class="w-full bg-blue-800 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $pct >= 80 ? 'bg-yellow-400' : 'bg-blue-400' }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @endif

        {{-- User + Logout --}}
        <div class="border-t border-white/10 px-3 py-3" x-data="{ open: false }">
            <button @click="open = !open"
                    class="w-full flex items-center gap-3 hover:bg-white/10 rounded-lg px-2 py-2 transition">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 text-left min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-blue-300 text-xs">{{ auth()->user()->role_label }}</div>
                </div>
                <svg class="w-4 h-4 text-blue-400 flex-shrink-0 transition-transform" :class="{ 'rotate-180': open }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-cloak
                 class="mt-1 bg-blue-800 rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-300 hover:bg-blue-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ══ OVERLAY mobile ══════════════════════════════════════════════ --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         x-cloak></div>

    {{-- ══ ZONE PRINCIPALE ═════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64 transition-all duration-300">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 sm:px-6 sticky top-0 z-30 gap-4">
            {{-- Bouton menu mobile --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-gray-500 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Titre de la page --}}
            <div class="flex-1">
                <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h1>
            </div>

            {{-- Flash messages inline --}}
            @if(session('succes'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="hidden sm:flex items-center gap-2 bg-green-50 text-green-700 text-xs px-3 py-1.5 rounded-lg border border-green-200">
                    ✅ {{ session('succes') }}
                </div>
            @endif

            {{-- Plan badge --}}
            @if($tenant = auth()->user()?->tenant)
                <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $tenant->plan === 'trial' ? 'bg-gray-100 text-gray-600' :
                       ($tenant->plan === 'starter' ? 'bg-blue-100 text-blue-700' :
                       ($tenant->plan === 'pro' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700')) }}">
                    {{ ucfirst($tenant->plan) }}
                </span>
            @endif
        </header>

        {{-- Flash messages (mobile + errors) --}}
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if(session('info'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="flex items-center justify-between bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
                    <span>ℹ️ {{ session('info') }}</span>
                    <button @click="show = false" class="ml-4 text-blue-500 hover:text-blue-700">✕</button>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Contenu --}}
        <main class="flex-1 px-4 sm:px-6 py-5 overflow-y-auto">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-100 py-3 text-center text-xs text-gray-400">
            eCompta360 — Comptabilité Intelligente &copy; {{ date('Y') }}
        </footer>
    </div>
</div>

<script>
    function pollStatutFacture(factureId) {
        const interval = setInterval(async () => {
            try {
                const r = await fetch(`/factures/${factureId}/statut`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!r.ok) return;
                const data = await r.json();
                if (data.statut !== 'traitement_en_cours') {
                    clearInterval(interval);
                    window.location.reload();
                }
            } catch (e) {}
        }, 10000);
    }
</script>

@stack('scripts')
</body>
</html>
