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
        .hero-bg { background: linear-gradient(160deg, #052e1f 0%, #0a3d2e 55%, #0d3d2d 100%); }
        .mint-bg { background-color: #dcefe6; }
        .card-shadow { box-shadow: 0 4px 24px rgba(6,95,70,.08); }
        .step-circle { background: radial-gradient(circle at 35% 30%, #14b8a6, #065f46); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

{{-- ══ NAVBAR (vert foncé, dans le hero) ═════════════════════════════════ --}}
<header class="hero-bg text-white sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-9 h-9 bg-amber-400 rounded-xl flex items-center justify-center font-black text-green-950 text-lg shadow">e</div>
                <span class="font-black text-xl tracking-tight">eCompta<span class="text-amber-300">360</span></span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-green-100">
                <a href="#fonctions" class="hover:text-white transition">Fonctionnalités</a>
                <a href="#suite"     class="hover:text-white transition">Nos outils</a>
                <a href="#tarifs"    class="hover:text-white transition">Tarifs</a>
                <a href="#faq"       class="hover:text-white transition">FAQ</a>
                <a href="#contact"   class="hover:text-white transition">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-green-100 hover:text-white transition">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="bg-amber-400 text-green-950 text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-amber-300 transition shadow-sm">
                    Essai gratuit
                </a>
                <button @click="open=!open" class="md:hidden p-2 text-green-100 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="open" x-cloak class="md:hidden py-3 border-t border-white/10 space-y-1 text-sm font-medium text-green-100">
            <a href="#fonctions" @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-white/10">Fonctionnalités</a>
            <a href="#suite"     @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-white/10">Nos outils</a>
            <a href="#tarifs"    @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-white/10">Tarifs</a>
            <a href="#faq"       @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-white/10">FAQ</a>
            <a href="#contact"   @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-white/10">Contact</a>
            <a href="{{ route('login') }}" class="block py-2 px-3 rounded-lg hover:bg-white/10">Connexion</a>
        </div>
    </div>
</header>

{{-- ══ HERO ═════════════════════════════════════════════════════════════ --}}
<section id="accueil" class="hero-bg text-white overflow-hidden relative" x-data="{ profil: 'cabinet' }">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute top-10 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-10 w-64 h-64 bg-teal-400 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-16">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-5">
                    Logiciel de comptabilité<br>
                    pour <span class="text-amber-300">cabinets & entreprises</span><br>
                    SYSCOHADA
                </h1>
                <p class="text-green-100 text-lg mb-8 leading-relaxed max-w-lg">
                    Plus qu'un logiciel, eCompta360 automatise la saisie comptable pour que votre cabinet
                    passe moins de temps sur la donnée et plus de temps avec ses clients.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-block bg-amber-400 text-green-950 font-bold px-7 py-3.5 rounded-xl hover:bg-amber-300 transition shadow-lg text-center">
                    Démarrer gratuitement — 15 jours
                </a>
                <p class="text-green-300 text-xs mt-4">Sans carte bancaire · Résiliable à tout moment</p>
            </div>

            {{-- Illustration dashboard --}}
            <div class="float hidden lg:block">
                <div class="bg-white/10 border border-white/20 rounded-2xl p-5 backdrop-blur-sm">
                    <div class="bg-white rounded-xl p-4 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-800 font-bold text-sm">Tableau de bord</span>
                            <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">Actif</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-emerald-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-emerald-700">48</div>
                                <div class="text-xs text-emerald-600 mt-0.5">Factures</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-green-700">45</div>
                                <div class="text-xs text-green-600 mt-0.5">Validées</div>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-black text-amber-600">3</div>
                                <div class="text-xs text-amber-500 mt-0.5">En attente</div>
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
                                    <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $row[3]==='validé'?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700' }}">
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

        {{-- Toggle profil --}}
        <div class="mt-10 flex flex-wrap gap-3">
            <button @click="profil = 'cabinet'"
                    :class="profil === 'cabinet' ? 'bg-white text-green-900' : 'bg-white/10 text-green-100 hover:bg-white/15'"
                    class="px-5 py-2.5 rounded-full text-sm font-semibold transition">
                Je suis un cabinet comptable
            </button>
            <button @click="profil = 'entreprise'"
                    :class="profil === 'entreprise' ? 'bg-white text-green-900' : 'bg-white/10 text-green-100 hover:bg-white/15'"
                    class="px-5 py-2.5 rounded-full text-sm font-semibold transition">
                Je suis une entreprise TPE-PME
            </button>
        </div>
        <p x-show="profil === 'cabinet'" x-cloak class="text-green-200 text-sm mt-3 max-w-lg">
            Gérez tous vos clients depuis un seul espace : upload, traitement automatique, validation et export SYSCOHADA.
        </p>
        <p x-show="profil === 'entreprise'" x-cloak class="text-green-200 text-sm mt-3 max-w-lg">
            Confiez vos factures à un cabinet équipé d'eCompta360 — moins d'allers-retours papier, une comptabilité à jour en continu.
        </p>
    </div>
</section>

{{-- ══ POURQUOI CHOISIR ═══════════════════════════════════════════════════ --}}
<section class="mint-bg py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-green-950 mb-3">Pourquoi choisir eCompta360 ?</h2>
            <p class="text-green-800/70 text-lg max-w-2xl mx-auto">
                Une plateforme pensée pour les réalités comptables et fiscales de l'espace OHADA.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach([
                ['M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Un moteur SYSCOHADA complet','Journaux, plan comptable révisé, calcul automatique de la TVA et de l\'AIB — tout est déjà paramétré pour le Bénin.'],
                ['M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z','Conçu pour l\'Afrique','Mobile Money (MTN, Moov), montants en FCFA, fiscalité locale : pensé pour les cabinets ouest-africains, pas adapté après coup.'],
                ['M13 10V3L4 14h7v7l9-11h-7z','Rapide et automatisé','Uploadez une facture, elle est traitée en quelques secondes. Plus de ressaisie manuelle ligne par ligne.'],
            ] as $c)
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c[0] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $c[1] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $c[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ PILOTER VOTRE CABINET (texte + illustration) ═══════════════════════ --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">
                Développer et piloter votre<br>cabinet comptable
            </h2>
            <p class="text-gray-500 text-lg leading-relaxed">
                Entre les nouvelles exigences fiscales, la digitalisation des services et le suivi quotidien
                des dossiers clients, votre cabinet fait face à de multiples défis. eCompta360 est conçu pour
                alléger cette charge et vous redonner du temps utile.
            </p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-8 border border-emerald-100 flex items-center justify-center">
            <div class="grid grid-cols-2 gap-4 w-full max-w-xs">
                @foreach([
                    ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Écritures validées'],
                    ['M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z','Suivi en temps réel'],
                    ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','Équipe collaborative'],
                    ['M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Exports conformes'],
                ] as $i)
                <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                    <svg class="w-6 h-6 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $i[0] }}"/>
                    </svg>
                    <p class="text-xs font-semibold text-gray-700">{{ $i[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══ RÉPONDRE À VOS ENJEUX MÉTIER ═══════════════════════════════════════ --}}
<section id="fonctions" class="mint-bg py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-green-950 mb-3">Répondre à vos enjeux métier</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['from-emerald-700 to-emerald-900','M13 10V3L4 14h7v7l9-11h-7z','Gagner en productivité','Automatisez la saisie répétitive et concentrez-vous sur le conseil.'],
                ['from-teal-700 to-teal-900','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Fiabiliser vos écritures','Des écritures conformes au SYSCOHADA Révisé, sans erreur de codification.'],
                ['from-green-700 to-green-900','M13 10V3L4 14h7v7l9-11h-7z','Réduire les tâches répétitives','Moins de ressaisie manuelle, plus de temps pour l\'analyse.'],
                ['from-emerald-800 to-teal-900','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','Devenir le copilote de vos clients','Libérez du temps pour un accompagnement à plus forte valeur ajoutée.'],
            ] as $card)
            <div class="rounded-2xl overflow-hidden card-shadow bg-gradient-to-br {{ $card[0] }} p-5 text-white flex flex-col min-h-[180px]">
                <svg class="w-6 h-6 text-amber-300 mb-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card[1] }}"/>
                </svg>
                <h3 class="font-bold mt-4 mb-1.5">{{ $card[2] }}</h3>
                <p class="text-white/80 text-xs leading-relaxed">{{ $card[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CONFORMITÉ (remplace le bandeau "logos clients") ═══════════════════ --}}
<section class="py-8 px-4 bg-white border-y border-gray-100">
    <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-sm font-semibold text-gray-400">
        <span class="uppercase tracking-wide text-xs text-gray-400">Conforme à :</span>
        <span>SYSCOHADA Révisé 2017</span>
        <span>·</span>
        <span>Zone OHADA</span>
        <span>·</span>
        <span>Fiscalité DGI Bénin</span>
        <span>·</span>
        <span>Paiement sécurisé FeexPay</span>
    </div>
</section>

{{-- ══ STATS BAR ════════════════════════════════════════════════════════ --}}
<section class="hero-bg py-8">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <div class="text-2xl font-black text-white">SYSCOHADA</div>
            <div class="text-xs text-green-300 mt-1">Plan comptable révisé</div>
        </div>
        <div>
            <div class="text-2xl font-black text-white">100%</div>
            <div class="text-xs text-green-300 mt-1">Traitement automatique</div>
        </div>
        <div>
            <div class="text-2xl font-black text-white">15 jours</div>
            <div class="text-xs text-green-300 mt-1">Essai gratuit</div>
        </div>
        <div>
            <div class="text-2xl font-black text-white">Mobile Money</div>
            <div class="text-xs text-green-300 mt-1">MTN & Moov</div>
        </div>
    </div>
</section>

{{-- ══ SUITE D'OUTILS (liste + CTA, comme "15 logiciels" chez Agiris) ═════ --}}
<section id="suite" class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">
                Une suite pensée pour<br>votre cabinet
            </h2>
            <p class="text-gray-500 text-lg leading-relaxed mb-6">
                Tout ce dont vous avez besoin pour la tenue comptable de vos clients, dans un seul espace.
            </p>
            <a href="{{ route('register') }}"
               class="inline-block bg-amber-400 text-green-950 font-bold px-6 py-3 rounded-xl hover:bg-amber-300 transition">
                Découvrir tous nos outils
            </a>
        </div>
        <div class="space-y-3">
            @foreach([
                ['M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z','Comptabilité','Écritures SYSCOHADA automatiques, journaux, plan comptable complet.'],
                ['M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','Notes de frais','Catégorisez vos dépenses par type de charge, exportez pour l\'expert-comptable.'],
                ['M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Déclarations','Retrouvez facilement les données pour vos déclarations fiscales TVA.'],
                ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','Immobilisations','Suivi des actifs immobilisés et de leur amortissement.'],
            ] as $tool)
            <div class="flex items-center gap-4 bg-gray-50 hover:bg-emerald-50 rounded-xl p-4 transition group">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tool[0] }}"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm">{{ $tool[1] }}</p>
                    <p class="text-gray-400 text-xs">{{ $tool[2] }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ COMMENT ÇA MARCHE ═══════════════════════════════════════════════════ --}}
<section class="mint-bg py-20 px-4" x-data="{ step: 1 }">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-green-950 mb-3">Comment fonctionne eCompta360 ?</h2>
            <p class="text-green-800/70 text-lg">Trois étapes, sans formation nécessaire.</p>
        </div>

        <div class="bg-white rounded-3xl overflow-hidden card-shadow grid md:grid-cols-2">
            <div class="step-circle flex items-center justify-center p-12">
                <svg class="w-24 h-24 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13l2 2 4-4"/>
                </svg>
            </div>
            <div class="hero-bg p-8 md:p-10 text-white flex flex-col justify-center">
                <template x-if="step === 1">
                    <div>
                        <h3 class="text-xl font-black mb-2">1. Uploadez vos factures</h3>
                        <p class="text-green-100 text-sm leading-relaxed">PDF ou image (JPG, PNG) — fournisseur, client, notes de frais, tout passe par le même point d'entrée.</p>
                    </div>
                </template>
                <template x-if="step === 2">
                    <div>
                        <h3 class="text-xl font-black mb-2">2. Traitement automatique</h3>
                        <p class="text-green-100 text-sm leading-relaxed">eCompta360 lit le document, identifie les montants, la TVA, l'AIB, et génère les écritures SYSCOHADA en quelques secondes.</p>
                    </div>
                </template>
                <template x-if="step === 3">
                    <div>
                        <h3 class="text-xl font-black mb-2">3. Validez et exportez</h3>
                        <p class="text-green-100 text-sm leading-relaxed">Vérifiez les données extraites, validez, puis exportez au format Excel, CSV ou FEC.</p>
                    </div>
                </template>
                <div class="flex gap-2 mt-6">
                    @for($i = 1; $i <= 3; $i++)
                    <button @click="step = {{ $i }}"
                            :class="step === {{ $i }} ? 'bg-amber-400 text-green-950' : 'bg-white/10 text-green-100 hover:bg-white/20'"
                            class="w-9 h-9 rounded-full font-bold text-sm transition">{{ $i }}</button>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ NOS INTÉGRATIONS ═════════════════════════════════════════════════════ --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Nos intégrations</h2>
            <p class="text-gray-500 text-lg leading-relaxed mb-6">
                Connectez eCompta360 aux outils que vous utilisez déjà et optimisez l'organisation de votre cabinet.
            </p>
            <a href="#tarifs"
               class="inline-block bg-amber-400 text-green-950 font-bold px-6 py-3 rounded-xl hover:bg-amber-300 transition">
                Voir les offres disponibles
            </a>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach([
                ['Google Sheets','Synchronisation automatique de vos écritures.'],
                ['FeexPay','Paiement Mobile Money (MTN, Moov) et carte.'],
                ['Export Excel/CSV','Compatible avec vos logiciels existants.'],
                ['Export FEC','Format standardisé pour la transmission fiscale.'],
            ] as $intg)
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5">
                <p class="font-bold text-gray-800 text-sm mb-1">{{ $intg[0] }}</p>
                <p class="text-gray-500 text-xs leading-relaxed">{{ $intg[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TARIFS ═══════════════════════════════════════════════════════════ --}}
<section id="tarifs" class="mint-bg py-20 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-green-950 mb-3">Des offres adaptées à votre activité</h2>
            <p class="text-green-800/70 text-lg">Commencez gratuitement. Évoluez à votre rythme.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-5">
            @foreach($plans as $plan)
            @php $pop = $plan->slug === 'pro'; @endphp
            <div class="relative rounded-2xl overflow-hidden card-shadow {{ $pop ? 'hero-bg scale-105 ring-2 ring-amber-300 ring-offset-2' : 'bg-white border border-gray-200' }}">
                @if($pop)
                <div class="text-center py-1.5 text-xs font-bold text-amber-300 uppercase tracking-widest border-b border-white/10">
                    ⭐ Le plus choisi
                </div>
                @endif
                <div class="p-6">
                    <div class="font-black text-xl mb-1 {{ $pop ? 'text-white' : 'text-gray-900' }}">{{ $plan->nom }}</div>
                    <div class="mt-3 mb-5">
                        <span class="text-3xl font-black {{ $pop ? 'text-amber-300' : 'text-emerald-700' }}">
                            @if($plan->prix_mensuel_xof === 0) Gratuit
                            @else {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }}
                            @endif
                        </span>
                        @if($plan->prix_mensuel_xof > 0)
                        <span class="text-sm {{ $pop ? 'text-green-200' : 'text-gray-400' }}"> FCFA/mois</span>
                        @else
                        <span class="text-sm {{ $pop ? 'text-green-200' : 'text-gray-400' }}"> · 15 jours</span>
                        @endif
                    </div>
                    <ul class="space-y-2 mb-6 text-sm">
                        @foreach([
                            ['quota_factures', $plan->quota_factures >= 9999 ? 'Factures illimitées' : $plan->quota_factures.' factures/mois', true],
                            ['quota_users', $plan->quota_users >= 99 ? 'Utilisateurs illimités' : $plan->quota_users.' utilisateur(s)', true],
                            ['export_xlsx', 'Export Excel/CSV/FEC', $plan->export_xlsx],
                            ['google_sheets', 'Sync Google Sheets', $plan->google_sheets],
                            ['api_access', 'Accès API', $plan->api_access],
                        ] as $feat)
                        <li class="flex items-center gap-2 {{ !$feat[2] ? 'opacity-40' : '' }}">
                            <span class="{{ $feat[2] ? ($pop?'text-amber-300':'text-emerald-600') : 'text-gray-300' }}">
                                {{ $feat[2] ? '✓' : '✗' }}
                            </span>
                            <span class="{{ $pop ? 'text-green-100' : 'text-gray-600' }}">{{ $feat[1] }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register', ['plan' => $plan->slug]) }}"
                       class="block text-center font-bold py-3 rounded-xl transition text-sm
                              {{ $pop ? 'bg-amber-400 text-green-950 hover:bg-amber-300' : 'bg-emerald-700 text-white hover:bg-emerald-800' }}">
                        {{ $plan->prix_mensuel_xof === 0 ? 'Commencer gratuitement' : 'Choisir cette offre' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-center text-green-800/60 text-sm mt-8">
            Paiement sécurisé · MTN Mobile Money · Moov Money · Pas de carte internationale requise
        </p>
    </div>
</section>

{{-- ══ TÉMOIGNAGES ══════════════════════════════════════════════════════ --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Ce que nos clients pensent de nous</h2>
            <p class="text-gray-400">Des cabinets qui ont transformé leur quotidien avec eCompta360.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['AM','Adjoua M.','Expert-comptable, Cotonou','bg-emerald-100 text-emerald-700','"eCompta360 a réduit de moitié le temps passé sur la saisie. Il reconnaît parfaitement les factures en XOF et génère les bons comptes SYSCOHADA automatiquement. Je recommande à tous mes confrères."'],
                ['KO','Kofi Ouedraogo','Cabinet comptable, Porto-Novo','bg-teal-100 text-teal-700','"Le gain de temps est impressionnant. Ce qui prenait 3 heures ne prend plus que 20 minutes. L\'intégration avec Mobile Money est un vrai plus — je règle mon abonnement depuis mon téléphone."'],
                ['FS','Fatou Sow','Directrice, Abomey-Calavi','bg-amber-100 text-amber-700','"Enfin une solution de comptabilité pensée pour l\'Afrique ! Le respect du SYSCOHADA Révisé est irréprochable. Mon équipe a été opérationnelle dès le premier jour sans formation particulière."'],
            ] as $t)
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 card-shadow">
                <div class="flex gap-0.5 mb-4">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.5 11.5 1 7.91l6.061-.882L10 2l2.939 5.028L19 7.91l-4.5 3.59 1.378 6.59z"/></svg>
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
<section id="faq" class="mint-bg py-20 px-4" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-black text-center text-green-950 mb-10">Questions fréquentes</h2>
        @php $faqs = [
            ["Qu'est-ce que le SYSCOHADA Révisé ?","Le SYSCOHADA Révisé est le référentiel comptable des 17 pays membres de l'OHADA. eCompta360 génère des écritures automatiquement conformes à ce standard, pour les régimes B (réel normal) et D (simplifié)."],
            ["Mes données sont-elles sécurisées ?","Oui. Chaque cabinet dispose d'un espace de données entièrement isolé. Les fichiers sont stockés sur un serveur sécurisé, avec chiffrement HTTPS. Aucune donnée n'est partagée entre cabinets."],
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
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform" :class="open==={{$i}} ? 'rotate-180' : ''"
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

{{-- ══ CTA FINAL (bandeau sombre, façon "Intéressé ? Des questions ?") ════ --}}
<section id="contact" class="bg-gray-950 py-16 px-4 text-center text-white">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-black mb-3">Intéressé ? Des questions ?</h2>
        <p class="text-gray-400 mb-8 text-lg">Notre équipe vous répond dans les 24 heures ouvrées.</p>
        <a href="{{ route('register') }}"
           class="inline-block bg-amber-400 text-green-950 font-bold px-10 py-4 rounded-xl hover:bg-amber-300 transition shadow-xl text-lg">
            Créer mon espace gratuitement →
        </a>
        <div class="flex flex-wrap justify-center gap-6 mt-8 text-sm text-gray-400">
            <a href="mailto:contact@ecompta360.com" class="hover:text-white transition">contact@ecompta360.com</a>
            <span>+229 00 00 00 00</span>
        </div>
    </div>
</section>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
<footer class="mint-bg text-green-900 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 bg-emerald-700 rounded-lg flex items-center justify-center font-black text-white text-sm">e</div>
                    <span class="font-black text-green-950 text-lg">eCompta360</span>
                </div>
                <p class="text-sm text-green-800/70 leading-relaxed max-w-xs">
                    Comptabilité intelligente pour cabinets africains. Conforme SYSCOHADA Révisé — Zone OHADA, Bénin.
                </p>
            </div>
            <div>
                <p class="font-semibold text-green-950 text-sm mb-4">Navigation</p>
                <div class="space-y-2 text-sm text-green-800/70">
                    <a href="#fonctions" class="block hover:text-green-950 transition">Fonctionnalités</a>
                    <a href="#tarifs" class="block hover:text-green-950 transition">Nos offres</a>
                    <a href="#faq" class="block hover:text-green-950 transition">FAQ</a>
                    <a href="#contact" class="block hover:text-green-950 transition">Contact</a>
                </div>
            </div>
            <div>
                <p class="font-semibold text-green-950 text-sm mb-4">Compte</p>
                <div class="space-y-2 text-sm text-green-800/70">
                    <a href="{{ route('login') }}" class="block hover:text-green-950 transition">Se connecter</a>
                    <a href="{{ route('register') }}" class="block hover:text-green-950 transition">Créer un espace</a>
                </div>
            </div>
        </div>
        <div class="border-t border-green-900/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-green-800/70">
            <p>© {{ date('Y') }} eCompta360 · Tous droits réservés</p>
            <div class="flex items-center gap-5">
                <a href="{{ route('legal.privacy') }}" class="hover:text-green-950 transition">Confidentialité</a>
                <a href="{{ route('legal.cookies') }}" class="hover:text-green-950 transition">Cookies</a>
                <a href="{{ route('legal.mentions') }}" class="hover:text-green-950 transition">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
