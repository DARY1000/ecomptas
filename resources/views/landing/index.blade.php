<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eCompta360 — La comptabilité SYSCOHADA automatisée</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        .hero-bg { background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #0ea5e9 100%); }
        .feature-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
        .card-shadow { box-shadow: 0 4px 24px rgba(30,58,138,.08); }
        .popular-card { background: linear-gradient(160deg, #1e3a8a 0%, #1d4ed8 100%); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

{{-- ══ NAVBAR ══════════════════════════════════════════════════════════ --}}
<header class="bg-white/95 backdrop-blur border-b border-gray-100 sticky top-0 z-50 shadow-sm" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-9 h-9 bg-blue-700 rounded-xl flex items-center justify-center font-black text-white text-lg shadow">e</div>
                <span class="font-black text-xl text-blue-900 tracking-tight">eCompta<span class="text-blue-500">360</span></span>
            </a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-500">
                <a href="#accueil"   class="hover:text-blue-700 transition">Accueil</a>
                <a href="#fonctions" class="hover:text-blue-700 transition">Fonctionnalités</a>
                <a href="#tarifs"    class="hover:text-blue-700 transition">Tarifs</a>
                <a href="#faq"       class="hover:text-blue-700 transition">FAQ</a>
                <a href="#contact"   class="hover:text-blue-700 transition">Contact</a>
            </nav>

            {{-- CTAs --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-blue-700 hover:text-blue-900 transition">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-blue-800 transition shadow-sm">
                    Essai gratuit
                </a>
                <button @click="open=!open" class="md:hidden p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        {{-- Mobile menu --}}
        <div x-show="open" x-cloak class="md:hidden py-3 border-t border-gray-100 space-y-1 text-sm font-medium text-gray-600">
            <a href="#accueil"   @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Accueil</a>
            <a href="#fonctions" @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Fonctionnalités</a>
            <a href="#tarifs"    @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Tarifs</a>
            <a href="#faq"       @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">FAQ</a>
            <a href="#contact"   @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Contact</a>
            <a href="{{ route('login') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Connexion</a>
        </div>
    </div>
</header>

{{-- ══ HERO ═════════════════════════════════════════════════════════════ --}}
<section id="accueil" class="hero-bg text-white overflow-hidden relative">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-10 w-64 h-64 bg-sky-300 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Disponible · Zone OHADA — Bénin
                </div>
                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-5">
                    La gestion sereine de votre<br>
                    <span class="text-sky-300">cabinet commence ici</span>
                </h1>
                <p class="text-blue-100 text-lg mb-8 leading-relaxed">
                    Uploadez vos factures, eCompta360 génère vos écritures <strong class="text-white">SYSCOHADA</strong> en quelques secondes.
                    Moins de saisie, plus de temps pour vos clients.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}"
                       class="bg-white text-blue-900 font-bold px-7 py-3.5 rounded-xl hover:bg-blue-50 transition shadow-lg text-center">
                        Démarrer gratuitement — 15 jours
                    </a>
                    <a href="#fonctions"
                       class="border-2 border-white/30 text-white font-semibold px-7 py-3.5 rounded-xl hover:bg-white/10 transition text-center">
                        Découvrir →
                    </a>
                </div>
                <p class="text-blue-300 text-xs mt-4">Sans carte bancaire · Résiliable à tout moment</p>
            </div>

            {{-- Illustration dashboard --}}
            <div class="float hidden lg:block">
                <div class="bg-white/10 border border-white/20 rounded-2xl p-5 backdrop-blur-sm">
                    <div class="bg-white rounded-xl p-4 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-800 font-bold text-sm">Tableau de bord</span>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Actif</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-blue-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-blue-700">48</div>
                                <div class="text-xs text-blue-500 mt-0.5">Factures</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-green-700">45</div>
                                <div class="text-xs text-green-500 mt-0.5">Validées</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-yellow-700">3</div>
                                <div class="text-xs text-yellow-500 mt-0.5">En attente</div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach([['FACT-001','Fournitures bureau','12 500','validé'],['FACT-002','Carburant véhicule','8 000','validé'],['FACT-003','Services informatiques','25 000','traitement']] as $row)
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                <div>
                                    <span class="text-xs font-bold text-gray-700">{{ $row[0] }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $row[1] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-gray-700">{{ $row[2] }} XOF</span>
                                    <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $row[3]==='validé'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">
                                        {{ $row[3]==='validé' ? '✓' : '⋯' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ STATS BAR ════════════════════════════════════════════════════════ --}}
<section class="bg-blue-950 py-8">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <div class="text-2xl font-black text-blue-300">SYSCOHADA</div>
            <div class="text-xs text-blue-400 mt-1">Plan comptable révisé</div>
        </div>
        <div>
            <div class="text-2xl font-black text-blue-300">100%</div>
            <div class="text-xs text-blue-400 mt-1">Traitement automatique</div>
        </div>
        <div>
            <div class="text-2xl font-black text-blue-300">15 jours</div>
            <div class="text-xs text-blue-400 mt-1">Essai gratuit</div>
        </div>
        <div>
            <div class="text-2xl font-black text-blue-300">Mobile Money</div>
            <div class="text-xs text-blue-400 mt-1">MTN & Moov</div>
        </div>
    </div>
</section>

{{-- ══ SIMPLIFIEZ VOS TÂCHES (macompta style) ══════════════════════════ --}}
<section id="fonctions" class="py-20 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">
                Simplifiez vos tâches administratives<br>
                <span class="text-blue-700">pour vous concentrer sur l'essentiel</span>
            </h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">
                eCompta360 prend en charge les tâches répétitives de votre comptabilité. Vous gardez le contrôle.
            </p>
        </div>

        <div class="space-y-16">

            {{-- Feature 1 --}}
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Extraction automatique
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Capturez vos factures en un clic</h3>
                    <p class="text-gray-500 mb-5 leading-relaxed">
                        Importez vos factures PDF ou images. Notre moteur de traitement extrait automatiquement toutes les informations clés : fournisseur, date, montants, TVA, type de charge.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Reconnaissance automatique du fournisseur','Extraction des montants HT, TVA, TTC','Détection du type de charge SYSCOHADA','Vérification de cohérence des données'] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                    <div class="bg-white rounded-xl p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center text-red-600 font-bold text-xs">PDF</div>
                            <div>
                                <div class="text-sm font-semibold text-gray-800">FACT_FOURNITURES_MAI.pdf</div>
                                <div class="text-xs text-gray-400">En cours de traitement…</div>
                            </div>
                            <div class="ml-auto">
                                <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-gray-50"><span class="text-gray-400">Fournisseur</span><span class="font-semibold text-gray-700">SONAC Bénin</span></div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50"><span class="text-gray-400">Date</span><span class="font-semibold text-gray-700">15/05/2025</span></div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50"><span class="text-gray-400">Montant HT</span><span class="font-semibold text-gray-700">45 000 XOF</span></div>
                            <div class="flex justify-between py-1.5"><span class="text-gray-400">Compte SYSCOHADA</span><span class="font-semibold text-blue-700">6011 — Achats de marchandises</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feature 2 --}}
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div class="lg:order-2">
                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Conformité SYSCOHADA
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Créez votre journal comptable collectif</h3>
                    <p class="text-gray-500 mb-5 leading-relaxed">
                        Les écritures sont générées automatiquement selon le Plan Comptable SYSCOHADA Révisé. Régimes B et D, journaux ACH, VTE et OD. Plus d'erreur de codification.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Écritures conformes au SYSCOHADA Révisé','Journaux d\'achat, de vente et opérations diverses','Validation et correction avant export','Export Excel/CSV compatible votre logiciel'] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="lg:order-1 bg-green-50 rounded-2xl p-6 border border-green-100">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gray-800 px-4 py-2 flex gap-1.5">
                            <div class="w-2.5 h-2.5 bg-red-400 rounded-full"></div>
                            <div class="w-2.5 h-2.5 bg-yellow-400 rounded-full"></div>
                            <div class="w-2.5 h-2.5 bg-green-400 rounded-full"></div>
                            <span class="text-gray-400 text-xs ml-2">Journal des écritures — Mai 2025</span>
                        </div>
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider">
                                <tr><th class="px-3 py-2 text-left">Journal</th><th class="px-3 py-2 text-left">N° Compte</th><th class="px-3 py-2 text-right">Débit</th><th class="px-3 py-2 text-right">Crédit</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 font-mono">
                                <tr class="hover:bg-gray-50"><td class="px-3 py-2 font-bold text-blue-700">ACH</td><td class="px-3 py-2 text-gray-700">6011</td><td class="px-3 py-2 text-right text-gray-800 font-semibold">45 000</td><td class="px-3 py-2 text-right text-gray-300">—</td></tr>
                                <tr class="hover:bg-gray-50"><td class="px-3 py-2 font-bold text-blue-700">ACH</td><td class="px-3 py-2 text-gray-700">4454</td><td class="px-3 py-2 text-right text-gray-800 font-semibold">8 100</td><td class="px-3 py-2 text-right text-gray-300">—</td></tr>
                                <tr class="hover:bg-gray-50"><td class="px-3 py-2 font-bold text-blue-700">ACH</td><td class="px-3 py-2 text-gray-700">4011</td><td class="px-3 py-2 text-right text-gray-300">—</td><td class="px-3 py-2 text-right text-green-700 font-semibold">53 100</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Feature 3 --}}
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Équipe & Rôles
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Profitez d'un accompagnement d'équipe</h3>
                    <p class="text-gray-500 mb-5 leading-relaxed">
                        Invitez vos collaborateurs avec des accès adaptés à leurs rôles. Chaque profil dispose exactement des droits nécessaires pour travailler efficacement.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Administrateur : accès complet au cabinet','Comptable : saisie, upload, validation','Auditeur : consultation et export uniquement','Gestion des quotas par abonnement'] as $item)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-purple-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100">
                    <div class="space-y-3">
                        @foreach([['AD','Adjoua Mensah','Administrateur','bg-blue-100 text-blue-700','Complet'],['KO','Kofi Ouedraogo','Comptable','bg-green-100 text-green-700','Upload + Écritures'],['FS','Fatou Sow','Auditeur','bg-gray-100 text-gray-600','Lecture seule']] as $user)
                        <div class="bg-white rounded-xl p-4 flex items-center gap-3 shadow-sm">
                            <div class="w-9 h-9 {{ $user[3] }} rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">{{ $user[0] }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm">{{ $user[1] }}</div>
                                <div class="text-xs text-gray-400">{{ $user[2] }}</div>
                            </div>
                            <span class="text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">{{ $user[4] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══ SUITE D'OUTILS (comme macompta) ═════════════════════════════════ --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-3">Une suite complète pour votre gestion</h2>
            <p class="text-gray-500">Tout ce dont vous avez besoin, dans un seul espace.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['bg-blue-50 border-blue-100','text-blue-700','bg-blue-100','Comptabilité','Écritures SYSCOHADA automatiques, journaux ACH/VTE/OD, plan comptable complet.','M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['bg-green-50 border-green-100','text-green-700','bg-green-100','Notes de frais','Catégorisez vos dépenses par type de charge. Exportez pour votre expert-comptable.','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['bg-yellow-50 border-yellow-100','text-yellow-700','bg-yellow-100','Déclarations','Retrouvez facilement les données pour vos déclarations fiscales TVA et IS.','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['bg-teal-50 border-teal-100','text-teal-700','bg-teal-100','Immobilisations','Suivi de vos actifs immobilisés et de leur amortissement dans le respect du SYSCOHADA.','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ] as $f)
            <div class="border {{ $f[0] }} rounded-2xl p-5 card-shadow">
                <div class="w-10 h-10 {{ $f[2] }} rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 {{ $f[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f[5] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1.5">{{ $f[3] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $f[4] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TARIFS ═══════════════════════════════════════════════════════════ --}}
<section id="tarifs" class="py-20 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">Des offres adaptées à votre activité</h2>
            <p class="text-gray-500 text-lg">Commencez gratuitement. Évoluez à votre rythme.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-5">
            @foreach($plans as $plan)
            @php $pop = $plan->slug === 'pro'; @endphp
            <div class="relative rounded-2xl overflow-hidden card-shadow {{ $pop ? 'popular-card scale-105 ring-2 ring-blue-400 ring-offset-2' : 'bg-white border border-gray-200' }}">
                @if($pop)
                <div class="text-center py-1.5 text-xs font-bold text-blue-200 uppercase tracking-widest border-b border-white/10">
                    ⭐ Le plus choisi
                </div>
                @endif
                <div class="p-6">
                    <div class="font-black text-xl mb-1 {{ $pop ? 'text-white' : 'text-gray-900' }}">{{ $plan->nom }}</div>
                    <div class="mt-3 mb-5">
                        <span class="text-3xl font-black {{ $pop ? 'text-blue-200' : 'text-blue-700' }}">
                            @if($plan->prix_mensuel_xof === 0) Gratuit
                            @else {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }}
                            @endif
                        </span>
                        @if($plan->prix_mensuel_xof > 0)
                        <span class="text-sm {{ $pop ? 'text-blue-300' : 'text-gray-400' }}"> FCFA/mois</span>
                        @else
                        <span class="text-sm {{ $pop ? 'text-blue-300' : 'text-gray-400' }}"> · 15 jours</span>
                        @endif
                    </div>
                    <ul class="space-y-2 mb-6 text-sm">
                        @foreach([
                            ['quota_factures', $plan->quota_factures >= 9999 ? 'Factures illimitées' : $plan->quota_factures.' factures/mois', true],
                            ['quota_users', $plan->quota_users >= 99 ? 'Utilisateurs illimités' : $plan->quota_users.' utilisateur(s)', true],
                            ['export_xlsx', 'Export Excel/CSV', $plan->export_xlsx],
                            ['google_sheets', 'Sync Google Sheets', $plan->google_sheets],
                            ['api_access', 'Accès API', $plan->api_access],
                        ] as $feat)
                        <li class="flex items-center gap-2 {{ !$feat[2] ? 'opacity-40' : '' }}">
                            <span class="{{ $feat[2] ? ($pop?'text-blue-300':'text-blue-600') : 'text-gray-300' }}">
                                {{ $feat[2] ? '✓' : '✗' }}
                            </span>
                            <span class="{{ $pop ? 'text-blue-100' : 'text-gray-600' }}">{{ $feat[1] }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register', ['plan' => $plan->slug]) }}"
                       class="block text-center font-bold py-3 rounded-xl transition text-sm
                              {{ $pop ? 'bg-white text-blue-900 hover:bg-blue-50' : 'bg-blue-700 text-white hover:bg-blue-800' }}">
                        {{ $plan->prix_mensuel_xof === 0 ? 'Commencer gratuitement' : 'Choisir cette offre' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-center text-gray-400 text-sm mt-8">
            Paiement sécurisé · MTN Mobile Money · Moov Money · Pas de carte internationale requise
        </p>
    </div>
</section>

{{-- ══ TÉMOIGNAGES ══════════════════════════════════════════════════════ --}}
<section class="py-20 px-4 bg-white" x-data="{ slide: 0 }">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Ce que nos clients pensent de nous</h2>
            <p class="text-gray-400">Des cabinets qui ont transformé leur quotidien avec eCompta360.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['AM','Adjoua M.','Expert-comptable, Cotonou','bg-blue-100 text-blue-700','"eCompta360 a réduit de moitié le temps passé sur la saisie. Il reconnaît parfaitement les factures en XOF et génère les bons comptes SYSCOHADA automatiquement. Je recommande à tous mes confrères."'],
                ['KO','Kofi Ouedraogo','Cabinet comptable, Porto-Novo','bg-teal-100 text-teal-700','"Le gain de temps est impressionnant. Ce qui prenait 3 heures ne prend plus que 20 minutes. L\'intégration avec Mobile Money est un vrai plus — je règle mon abonnement depuis mon téléphone."'],
                ['FS','Fatou Sow','Directrice, Abomey-Calavi','bg-purple-100 text-purple-700','"Enfin une solution de comptabilité pensée pour l\'Afrique ! Le respect du SYSCOHADA Révisé est irréprochable. Mon équipe a été opérationnelle dès le premier jour sans formation particulière."'],
            ] as $t)
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 card-shadow">
                <div class="flex gap-0.5 mb-4">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.5 11.5 1 7.91l6.061-.882L10 2l2.939 5.028L19 7.91l-4.5 3.59 1.378 6.59z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-5 italic">{{ $t[4] }}</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $t[3] }} rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">{{ $t[0] }}</div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $t[1] }}</div>
                        <div class="text-xs text-gray-400">{{ $t[2] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ FAQ ════════════════════════════════════════════════════════════ --}}
<section id="faq" class="py-20 px-4 bg-gray-50" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-black text-center text-gray-900 mb-10">Questions fréquentes</h2>
        @php $faqs = [
            ["Qu'est-ce que le SYSCOHADA Révisé ?","Le SYSCOHADA Révisé est le référentiel comptable des 17 pays membres de l'OHADA. eCompta360 génère des écritures automatiquement conformes à ce standard, pour les régimes B (réel normal) et D (simplifié)."],
            ["Mes données sont-elles sécurisées ?","Oui. Chaque cabinet dispose d'un espace de données entièrement isolé. Les fichiers sont stockés sur un serveur sécurisé en Europe, avec chiffrement HTTPS. Aucune donnée n'est partagée entre cabinets."],
            ["Comment fonctionne le traitement automatique ?","Vous importez une facture PDF. Notre moteur extrait automatiquement le fournisseur, la date, les montants, la TVA. Il génère les écritures SYSCOHADA correspondantes. Il suffit de valider."],
            ["Puis-je changer d'offre en cours de mois ?","Oui. Vous pouvez évoluer vers une offre supérieure à tout moment depuis votre espace. Le changement est immédiat."],
            ["Comment se passe le paiement ?","Nous acceptons MTN Mobile Money et Moov Money via FeexPay. Aucune carte bancaire internationale requise. Le paiement est 100% local."],
            ["Que se passe-t-il à la fin de l'essai gratuit ?","Votre compte est suspendu jusqu'à souscription d'une offre payante. Vos données restent intactes et accessibles à la réactivation."],
            ["Y a-t-il une formation pour démarrer ?","Non. L'interface est intuitive et conçue pour être utilisée sans formation. Un email de bienvenue avec les étapes clés vous est envoyé à l'inscription."],
        ]; @endphp
        <div class="space-y-2">
            @foreach($faqs as $i => $faq)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <button @click="open = open === {{$i}} ? null : {{$i}}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50 transition text-sm">
                    <span>{{ $faq[0] }}</span>
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform" :class="open===={{$i}} ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === {{$i}}" x-cloak class="px-6 pb-4 text-gray-500 text-sm leading-relaxed">{{ $faq[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CTA FINAL ═══════════════════════════════════════════════════════ --}}
<section class="hero-bg py-16 px-4 text-center text-white">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-black mb-3">Prêt à moderniser votre cabinet ?</h2>
        <p class="text-blue-200 mb-8 text-lg">Rejoignez les cabinets qui font confiance à eCompta360.<br>15 jours d'essai gratuit, sans engagement, sans carte bancaire.</p>
        <a href="{{ route('register') }}"
           class="inline-block bg-white text-blue-900 font-bold px-10 py-4 rounded-xl hover:bg-blue-50 transition shadow-xl text-lg">
            Créer mon espace gratuitement →
        </a>
    </div>
</section>

{{-- ══ CONTACT ══════════════════════════════════════════════════════════ --}}
<section id="contact" class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl font-black text-gray-900 mb-2">Nous contacter</h2>
        <p class="text-gray-500 mb-8">Notre équipe répond dans les 24 heures ouvrées.</p>
        <div class="grid sm:grid-cols-2 gap-4 max-w-xl mx-auto text-left">
            <div class="bg-blue-50 rounded-xl p-5 border border-blue-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700 text-sm mb-0.5">Email</p>
                    <a href="mailto:contact@ecompta360.com" class="text-blue-700 text-sm hover:underline">contact@ecompta360.com</a>
                </div>
            </div>
            <div class="bg-green-50 rounded-xl p-5 border border-green-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700 text-sm mb-0.5">WhatsApp</p>
                    <span class="text-gray-600 text-sm">+229 00 00 00 00</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
<footer class="bg-gray-950 text-gray-500 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 bg-blue-700 rounded-lg flex items-center justify-center font-black text-white text-sm">e</div>
                    <span class="font-black text-white text-lg">eCompta360</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed max-w-xs">
                    Comptabilité intelligente pour cabinets africains. Conforme SYSCOHADA Révisé — Zone OHADA, Bénin.
                </p>
            </div>
            <div>
                <p class="font-semibold text-white text-sm mb-4">Navigation</p>
                <div class="space-y-2 text-sm">
                    <a href="#fonctions" class="block hover:text-white transition">Fonctionnalités</a>
                    <a href="#tarifs" class="block hover:text-white transition">Nos offres</a>
                    <a href="#faq" class="block hover:text-white transition">FAQ</a>
                    <a href="#contact" class="block hover:text-white transition">Contact</a>
                </div>
            </div>
            <div>
                <p class="font-semibold text-white text-sm mb-4">Compte</p>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('login') }}" class="block hover:text-white transition">Se connecter</a>
                    <a href="{{ route('register') }}" class="block hover:text-white transition">Créer un espace</a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
            <p>© {{ date('Y') }} eCompta360 · Tous droits réservés</p>
            <div class="flex items-center gap-5">
                <a href="{{ route('legal.privacy') }}" class="hover:text-gray-300 transition">Confidentialité</a>
                <a href="{{ route('legal.cookies') }}" class="hover:text-gray-300 transition">Cookies</a>
                <a href="{{ route('legal.mentions') }}" class="hover:text-gray-300 transition">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
