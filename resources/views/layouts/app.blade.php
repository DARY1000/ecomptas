<!DOCTYPE html>
<html lang="fr" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — eCompta360</title>

    {{-- Tailwind CSS via CDN Play — pas de npm/vite sur Hostinger mutualisé --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   { DEFAULT: '#1e40af', light: '#3b82f6', dark: '#1e3a8a' },
                        secondary: { DEFAULT: '#0f766e' },
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js via CDN --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold; }
    </style>

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    {{-- ══ BARRE DE NAVIGATION ══════════════════════════════════════════ --}}
    <nav class="bg-blue-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo + Nom cabinet --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-400 rounded-lg flex items-center justify-center font-bold text-sm">e</div>
                        <span class="font-bold text-lg hidden sm:block">eCompta360</span>
                    </a>
                    @if(auth()->user()?->tenant)
                        <span class="text-blue-300 text-sm hidden md:block">
                            {{ auth()->user()->tenant->nom }}
                        </span>
                    @endif
                </div>

                {{-- Navigation principale --}}
                <div class="hidden md:flex items-center gap-1 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-md hover:bg-blue-800 transition {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
                        Tableau de bord
                    </a>
                    <a href="{{ route('factures.index') }}"
                       class="px-3 py-2 rounded-md hover:bg-blue-800 transition {{ request()->routeIs('factures.*') ? 'bg-blue-800' : '' }}">
                        Factures
                    </a>
                    @if(!auth()->user()->estAuditeur())
                    <a href="{{ route('factures.upload') }}"
                       class="px-3 py-2 rounded-md hover:bg-blue-800 transition">
                        ＋ Upload
                    </a>
                    @endif
                    <a href="{{ route('ecritures.index') }}"
                       class="px-3 py-2 rounded-md hover:bg-blue-800 transition {{ request()->routeIs('ecritures.*') ? 'bg-blue-800' : '' }}">
                        Écritures
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('settings.index') }}"
                       class="px-3 py-2 rounded-md hover:bg-blue-800 transition {{ request()->routeIs('settings.*') ? 'bg-blue-800' : '' }}">
                        Paramètres
                    </a>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3 py-2 rounded-md bg-yellow-600 hover:bg-yellow-500 transition ml-2">
                        ⚙ Admin
                    </a>
                    @endif
                </div>

                {{-- Quota + Profil --}}
                <div class="flex items-center gap-3">
                    {{-- Badge quota mensuel --}}
                    @php $tenant = auth()->user()?->tenant; @endphp
                    @if($tenant)
                        @php $pct = $tenant->quotaPourcentage(); @endphp
                        <div class="hidden sm:flex items-center gap-1 bg-blue-800 rounded-full px-3 py-1 text-xs">
                            <span class="{{ $pct >= 80 ? 'text-yellow-300' : 'text-blue-200' }}">
                                {{ $tenant->facturesCeMois() }}/{{ $tenant->quota_factures_mensuel }}
                            </span>
                            <span class="text-blue-400">factures</span>
                        </div>
                    @endif

                    {{-- Dropdown utilisateur --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 text-sm hover:text-blue-200 focus:outline-none">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="hidden md:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-xl border border-gray-100 py-1 z-50">
                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                {{ auth()->user()->role_label }}
                            </div>
                            <a href="{{ route('abonnement.index') }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">
                                Mon abonnement
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    {{-- ══ FLASH MESSAGES ══════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4 space-y-2">
        @if(session('succes'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="flex items-center justify-between bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
                <span>✅ {{ session('succes') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-800 ml-4">✕</button>
            </div>
        @endif
        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="flex items-center justify-between bg-blue-50 border border-blue-400 text-blue-800 px-4 py-3 rounded-lg">
                <span>ℹ️ {{ session('info') }}</span>
                <button @click="show = false" class="text-blue-600 hover:text-blue-800 ml-4">✕</button>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- ══ CONTENU PRINCIPAL ═══════════════════════════════════════════ --}}
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 flex-1">
        @yield('content')
    </main>

    {{-- ══ FOOTER ══════════════════════════════════════════════════════ --}}
    <footer class="bg-white border-t border-gray-100 py-3 text-center text-xs text-gray-400">
        eCompta360 — Comptabilité Intelligente &copy; {{ date('Y') }}
    </footer>

    {{-- ══ POLLING JS — mise à jour statut factures ════════════════════ --}}
    {{-- Remplace SSE/WebSockets (non disponibles sur mutualisé Hostinger) --}}
    <script>
        /**
         * Polling toutes les 10 secondes pour les factures en traitement.
         * Si le statut change, on recharge la page.
         * @param {string} factureId  UUID de la facture
         */
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
                } catch (e) { /* silencieux */ }
            }, 10000);
        }
    </script>

    @stack('scripts')
</body>
</html>
