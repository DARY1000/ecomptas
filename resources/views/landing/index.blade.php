<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eCompta360 — Comptabilité Intelligente pour cabinets africains</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .gradient-hero { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #0f766e 100%); }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.10); }
    </style>
</head>
<body class="bg-white text-gray-800">

{{-- ══ NAVBAR ══════════════════════════════════════════════════════════ --}}
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-700 rounded-lg flex items-center justify-center font-black text-white text-sm">e</div>
                <span class="font-bold text-xl text-blue-900">eCompta360</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
                <a href="#accueil" class="hover:text-blue-700 transition font-medium">Accueil</a>
                <a href="#fonctionnalites" class="hover:text-blue-700 transition">Fonctionnalités</a>
                <a href="#tarifs" class="hover:text-blue-700 transition">Tarifs</a>
                <a href="#faq" class="hover:text-blue-700 transition">FAQ</a>
                <a href="#contact" class="hover:text-blue-700 transition">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="text-sm text-blue-700 hover:text-blue-900 font-medium hidden sm:block">
                    Se connecter
                </a>
                <a href="{{ route('register') }}"
                   class="bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-800 transition shadow">
                    Essai gratuit
                </a>
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="mobileOpen" x-cloak class="md:hidden pb-4 space-y-2 text-sm text-gray-600">
            <a href="#accueil" class="block py-2 hover:text-blue-700">Accueil</a>
            <a href="#fonctionnalites" class="block py-2 hover:text-blue-700">Fonctionnalités</a>
            <a href="#tarifs" class="block py-2 hover:text-blue-700">Tarifs</a>
            <a href="#faq" class="block py-2 hover:text-blue-700">FAQ</a>
            <a href="#contact" class="block py-2 hover:text-blue-700">Contact</a>
        </div>
    </div>
</nav>

{{-- ══ HERO ═════════════════════════════════════════════════════════════ --}}
<section id="accueil" class="gradient-hero text-white py-24 px-4">
    <div class="max-w-5xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm mb-6">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Disponible maintenant — Zone OHADA/UEMOA · Bénin
        </div>
        <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight">
            La comptabilité SYSCOHADA<br>
            <span class="text-blue-300">automatisée par l'IA</span>
        </h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-8">
            Uploadez vos factures, l'intelligence artificielle les traite et génère vos écritures SYSCOHADA en quelques secondes.
            Conçu pour les cabinets comptables du Bénin et de la zone OHADA.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="bg-white text-blue-900 font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition shadow-lg text-lg">
                Commencer gratuitement — 15 jours
            </a>
            <a href="#tarifs"
               class="border-2 border-white/40 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition text-lg">
                Voir les offres →
            </a>
        </div>
        <p class="text-blue-300 text-sm mt-4">Aucune carte bancaire requise · Résiliable à tout moment</p>
    </div>
</section>

{{-- ══ STATS ════════════════════════════════════════════════════════════ --}}
<section class="bg-blue-900 text-white py-10">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <div class="text-3xl font-black text-blue-300">IA</div>
            <div class="text-sm text-blue-200 mt-1">Traitement automatique</div>
        </div>
        <div>
            <div class="text-3xl font-black text-blue-300">SYSCOHADA</div>
            <div class="text-sm text-blue-200 mt-1">Plan comptable révisé</div>
        </div>
        <div>
            <div class="text-3xl font-black text-blue-300">15 jours</div>
            <div class="text-sm text-blue-200 mt-1">Essai gratuit</div>
        </div>
        <div>
            <div class="text-3xl font-black text-blue-300">Multi-cabinet</div>
            <div class="text-sm text-blue-200 mt-1">Gestion centralisée</div>
        </div>
    </div>
</section>

{{-- ══ FONCTIONNALITÉS ═════════════════════════════════════════════════ --}}
<section id="fonctionnalites" class="py-20 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">Tout ce dont votre cabinet a besoin</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Une solution complète, pensée pour la réalité des cabinets comptables africains.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Upload & Extraction IA</h3>
                <p class="text-gray-500 text-sm">Importez vos factures PDF. L'IA extrait automatiquement fournisseur, montants, TVA, date — sans aucune saisie manuelle.</p>
            </div>

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Écritures SYSCOHADA</h3>
                <p class="text-gray-500 text-sm">Génération automatique des écritures comptables conformes au référentiel SYSCOHADA Révisé. Régimes B et D pris en charge.</p>
            </div>

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Tableau de bord & KPIs</h3>
                <p class="text-gray-500 text-sm">Suivi en temps réel : factures traitées, montants, pipeline de traitement. Exports Excel et CSV pour votre logiciel comptable.</p>
            </div>

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Équipe & rôles</h3>
                <p class="text-gray-500 text-sm">Invitez votre équipe : administrateur, comptable, auditeur. Chaque profil dispose d'accès adaptés à ses responsabilités.</p>
            </div>

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Paiement Mobile Money</h3>
                <p class="text-gray-500 text-sm">Abonnement via MTN Mobile Money et Moov Money (FeexPay). Aucune carte bancaire internationale nécessaire.</p>
            </div>

            <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Données isolées & sécurisées</h3>
                <p class="text-gray-500 text-sm">Chaque cabinet dispose de son espace isolé. Les données ne sont jamais partagées entre cabinets. Hébergement sécurisé en Europe.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══ TARIFS ═══════════════════════════════════════════════════════════ --}}
<section id="tarifs" class="py-20 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">Des offres adaptées à votre activité</h2>
            <p class="text-gray-500 text-lg">Commencez gratuitement. Évoluez à votre rythme.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            @foreach($plans as $plan)
            @php
                $popular = $plan->slug === 'pro';
            @endphp
            <div class="relative card-hover rounded-2xl shadow-sm overflow-hidden
                        {{ $popular ? 'ring-2 ring-blue-500 shadow-xl scale-105' : 'border border-gray-200 bg-white' }}">
                @if($popular)
                <div class="bg-blue-500 text-white text-xs font-bold text-center py-1.5 tracking-wider uppercase">
                    ⭐ Le plus choisi
                </div>
                @endif
                <div class="{{ $popular ? 'bg-blue-900 text-white' : 'bg-white' }} p-6">
                    <h3 class="font-black text-xl mb-1">{{ $plan->nom }}</h3>
                    <div class="text-3xl font-black mt-3 mb-1">
                        @if($plan->prix_mensuel_xof === 0)
                            <span class="{{ $popular ? 'text-blue-300' : 'text-green-600' }}">Gratuit</span>
                        @else
                            {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }}
                            <span class="text-base font-normal {{ $popular ? 'text-blue-300' : 'text-gray-400' }}"> FCFA</span>
                        @endif
                    </div>
                    <p class="text-xs {{ $popular ? 'text-blue-300' : 'text-gray-400' }} mb-5">par mois</p>

                    <ul class="space-y-2.5 text-sm mb-6">
                        <li class="flex items-center gap-2">
                            <span class="{{ $popular ? 'text-blue-300' : 'text-green-500' }}">✓</span>
                            <span class="{{ $popular ? 'text-blue-100' : 'text-gray-600' }}">
                                {{ $plan->quota_factures >= 9999 ? 'Factures illimitées' : $plan->quota_factures . ' factures/mois' }}
                            </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="{{ $popular ? 'text-blue-300' : 'text-green-500' }}">✓</span>
                            <span class="{{ $popular ? 'text-blue-100' : 'text-gray-600' }}">
                                {{ $plan->quota_users >= 99 ? 'Utilisateurs illimités' : $plan->quota_users . ' utilisateur(s)' }}
                            </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="{{ $plan->export_xlsx ? ($popular ? 'text-blue-300' : 'text-green-500') : 'text-gray-300' }}">
                                {{ $plan->export_xlsx ? '✓' : '✗' }}
                            </span>
                            <span class="{{ $popular ? 'text-blue-100' : ($plan->export_xlsx ? 'text-gray-600' : 'text-gray-300') }}">Export Excel/CSV</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="{{ $plan->google_sheets ? ($popular ? 'text-blue-300' : 'text-green-500') : 'text-gray-300' }}">
                                {{ $plan->google_sheets ? '✓' : '✗' }}
                            </span>
                            <span class="{{ $popular ? 'text-blue-100' : ($plan->google_sheets ? 'text-gray-600' : 'text-gray-300') }}">Google Sheets</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="{{ $plan->api_access ? ($popular ? 'text-blue-300' : 'text-green-500') : 'text-gray-300' }}">
                                {{ $plan->api_access ? '✓' : '✗' }}
                            </span>
                            <span class="{{ $popular ? 'text-blue-100' : ($plan->api_access ? 'text-gray-600' : 'text-gray-300') }}">Accès API</span>
                        </li>
                    </ul>

                    <a href="{{ route('register', ['plan' => $plan->slug]) }}"
                       class="block text-center font-bold py-3 rounded-xl transition text-sm
                              {{ $popular ? 'bg-white text-blue-900 hover:bg-blue-50' : 'bg-blue-700 text-white hover:bg-blue-800' }}">
                        {{ $plan->prix_mensuel_xof === 0 ? 'Commencer gratuitement' : 'Choisir cette offre' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <p class="text-center text-gray-400 text-sm mt-8">
            Tous les prix sont en Francs CFA (XOF) · Paiement via MTN Mobile Money ou Moov Money
        </p>
    </div>
</section>

{{-- ══ TÉMOIGNAGES ══════════════════════════════════════════════════════ --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-3">Ils nous font confiance</h2>
            <p class="text-gray-500">Des cabinets comptables qui ont transformé leur quotidien.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
                    @for($i=0;$i<5;$i++)<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.5 11.5 1 7.91l6.061-.882L10 2l2.939 5.028L19 7.91l-4.5 3.59 1.378 6.59z"/></svg>@endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    "eCompta360 a réduit de moitié le temps que je passais à saisir les écritures. L'IA comprend parfaitement les factures en XOF et génère les bons comptes SYSCOHADA automatiquement."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-700 text-sm">AM</div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Adjoua M.</p>
                        <p class="text-xs text-gray-400">Expert-comptable, Cotonou</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-900 rounded-2xl p-6">
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
                    @for($i=0;$i<5;$i++)<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.5 11.5 1 7.91l6.061-.882L10 2l2.939 5.028L19 7.91l-4.5 3.59 1.378 6.59z"/></svg>@endfor
                </div>
                <p class="text-blue-100 text-sm leading-relaxed mb-4">
                    "Le gain de temps est impressionnant. Ce qui prenait 3 heures ne prend plus que 20 minutes. L'intégration avec Mobile Money est un vrai plus — je règle mon abonnement directement depuis mon téléphone."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center font-bold text-white text-sm">KD</div>
                    <div>
                        <p class="font-semibold text-white text-sm">Kofi D.</p>
                        <p class="text-xs text-blue-300">Cabinet comptable, Porto-Novo</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
                    @for($i=0;$i<5;$i++)<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.5 11.5 1 7.91l6.061-.882L10 2l2.939 5.028L19 7.91l-4.5 3.59 1.378 6.59z"/></svg>@endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    "Enfin une application de comptabilité pensée pour l'Afrique ! Le respect du SYSCOHADA Révisé est parfait, et l'interface est claire. Mon équipe a été opérationnelle dès le premier jour."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center font-bold text-teal-700 text-sm">FS</div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Fatou S.</p>
                        <p class="text-xs text-gray-400">Directrice de cabinet, Abomey-Calavi</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══ FAQ ════════════════════════════════════════════════════════════ --}}
<section id="faq" class="py-20 px-4 bg-gray-50" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-black text-center text-gray-900 mb-12">Questions fréquentes</h2>

        @php
        $faqs = [
            ['q' => "Qu'est-ce que le SYSCOHADA Révisé ?", 'r' => "Le SYSCOHADA Révisé est le système comptable de référence pour les 17 pays membres de l'OHADA. eCompta360 génère automatiquement les écritures conformes à ce référentiel, pour les régimes B et D."],
            ['q' => "Mes données sont-elles sécurisées ?", 'r' => "Oui. Chaque cabinet dispose de son espace de données entièrement isolé. Les fichiers sont stockés sur notre serveur hébergé en Europe. Aucune donnée ne circule vers des tiers non autorisés."],
            ['q' => "Comment fonctionne le traitement automatique des factures ?", 'r' => "Vous importez une facture PDF. Notre moteur de traitement extrait automatiquement les données clés : fournisseur, date, montants, TVA, type de facture. Il vous suffit ensuite de valider l'écriture générée."],
            ['q' => "Puis-je changer d'offre en cours de mois ?", 'r' => "Oui. Vous pouvez passer à une offre supérieure à tout moment depuis votre espace abonnement. Le changement prend effet immédiatement."],
            ['q' => "Comment puis-je payer ?", 'r' => "Nous acceptons MTN Mobile Money et Moov Money via FeexPay. Aucune carte bancaire internationale requise. Le paiement est 100% local."],
            ['q' => "Que se passe-t-il après l'essai gratuit ?", 'r' => "À la fin de vos 15 jours d'essai, votre compte est suspendu jusqu'à souscription d'une offre payante. Toutes vos données sont conservées et accessibles dès réactivation."],
        ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50 transition">
                    <span>{{ $faq['q'] }}</span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': open === {{ $i }} }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === {{ $i }}" x-cloak class="px-6 pb-4 text-gray-500 text-sm leading-relaxed">
                    {{ $faq['r'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CTA FINAL ═══════════════════════════════════════════════════════ --}}
<section class="gradient-hero text-white py-16 px-4 text-center">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-black mb-4">Prêt à moderniser votre cabinet ?</h2>
        <p class="text-blue-200 mb-8">Rejoignez les cabinets qui font confiance à eCompta360. 15 jours d'essai gratuit, sans engagement.</p>
        <a href="{{ route('register') }}"
           class="inline-block bg-white text-blue-900 font-bold px-10 py-4 rounded-xl hover:bg-blue-50 transition shadow-lg text-lg">
            Créer mon espace gratuitement →
        </a>
    </div>
</section>

{{-- ══ CONTACT ══════════════════════════════════════════════════════════ --}}
<section id="contact" class="py-20 px-4 bg-white">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl font-black text-gray-900 mb-3">Nous contacter</h2>
        <p class="text-gray-500 mb-8">Une question ? Notre équipe vous répond dans les 24 heures.</p>
        <div class="grid sm:grid-cols-2 gap-4 text-left">
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm">Email</span>
                </div>
                <a href="mailto:contact@ecompta360.com" class="text-blue-700 text-sm hover:underline">contact@ecompta360.com</a>
            </div>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm">Téléphone / WhatsApp</span>
                </div>
                <span class="text-gray-600 text-sm">+229 00 00 00 00</span>
            </div>
        </div>
    </div>
</section>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
<footer class="bg-gray-900 text-gray-400 py-12 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center font-black text-white text-xs">e</div>
                    <span class="font-bold text-white">eCompta360</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">Comptabilité intelligente pour cabinets africains. Conforme SYSCOHADA Révisé — Zone OHADA.</p>
            </div>
            <div>
                <p class="font-semibold text-white text-sm mb-3">Navigation</p>
                <div class="space-y-2 text-sm">
                    <a href="#fonctionnalites" class="block hover:text-white transition">Fonctionnalités</a>
                    <a href="#tarifs" class="block hover:text-white transition">Nos offres</a>
                    <a href="#faq" class="block hover:text-white transition">FAQ</a>
                    <a href="#contact" class="block hover:text-white transition">Contact</a>
                </div>
            </div>
            <div>
                <p class="font-semibold text-white text-sm mb-3">Compte</p>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('login') }}" class="block hover:text-white transition">Se connecter</a>
                    <a href="{{ route('register') }}" class="block hover:text-white transition">Créer un compte</a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-gray-600">
            <p>© {{ date('Y') }} eCompta360 · Tous droits réservés</p>
            <div class="flex items-center gap-5">
                <a href="{{ route('legal.privacy') }}" class="hover:text-gray-400 transition">Politique de confidentialité</a>
                <a href="{{ route('legal.cookies') }}" class="hover:text-gray-400 transition">Cookies</a>
                <a href="{{ route('legal.mentions') }}" class="hover:text-gray-400 transition">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
