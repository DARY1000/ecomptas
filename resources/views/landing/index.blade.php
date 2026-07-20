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
        .dark-cta { background: linear-gradient(135deg, #052e1f 0%, #0a3d2e 100%); }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .marquee-track { animation: marquee 18s linear infinite; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

{{-- ══ NAVBAR — blanche, minimale ══════════════════════════════════════════ --}}
<header class="bg-white border-b border-gray-100 sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-7 h-7 bg-emerald-800 rounded-md flex items-center justify-center font-black text-white text-sm">e</div>
                <span class="font-bold text-base text-gray-900">eCompta360</span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm text-gray-500">
                <a href="#fonctions" class="hover:text-gray-900 transition">Fonctionnalités</a>
                <a href="#suite"     class="hover:text-gray-900 transition">Comment ça marche</a>
                <a href="#tarifs"    class="hover:text-gray-900 transition">Tarifs</a>
                <a href="#faq"       class="hover:text-gray-900 transition">FAQ</a>
                <a href="#contact"   class="hover:text-gray-900 transition">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="bg-emerald-800 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-emerald-900 transition">
                    S'inscrire
                </a>
                <button @click="open=!open" class="md:hidden p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="open" x-cloak class="md:hidden py-3 border-t border-gray-100 space-y-1 text-sm font-medium text-gray-600">
            <a href="#fonctions" @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Fonctionnalités</a>
            <a href="#suite"     @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Comment ça marche</a>
            <a href="#tarifs"    @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Tarifs</a>
            <a href="#faq"       @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">FAQ</a>
            <a href="#contact"   @click="open=false" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Contact</a>
            <a href="{{ route('login') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-50">Connexion</a>
        </div>
    </div>
</header>

{{-- ══ HERO — blanc, centré, minimal ═══════════════════════════════════════ --}}
<section id="accueil" class="bg-white pt-16 pb-14 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <span class="inline-flex items-center gap-2 text-xs font-medium text-gray-500 border border-gray-200 rounded-full px-3 py-1 mb-6">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
            La plateforme comptable pour l'Afrique de l'Ouest
        </span>
        <h1 class="text-5xl md:text-6xl font-black leading-none mb-6 text-gray-900">
            La Comptabilité.<br>
            <span class="text-emerald-700">Simplement.</span>
        </h1>
        <p class="text-gray-500 text-lg leading-relaxed max-w-xl mx-auto mb-8">
            Les écritures comptables SYSCOHADA automatisées, la facturation, la trésorerie et la fiscalité
            réunies dans une seule solution. Simple, rapide et accessible aux PME d'Afrique de l'Ouest.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="bg-emerald-800 text-white font-semibold px-6 py-3 rounded-lg hover:bg-emerald-900 transition">
                Démarrer gratuitement →
            </a>
            <a href="#suite"
               class="border border-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg hover:bg-gray-50 transition">
                Voir comment ça marche
            </a>
        </div>

    </div>

    {{-- Gérez votre comptabilité en toute sérénité --}}
    <div class="max-w-5xl mx-auto mt-16">
        <h2 class="text-2xl md:text-3xl font-black text-gray-900 text-center mb-8">
            Gérez votre comptabilité<br class="sm:hidden">en toute sérénité
        </h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['', 'Conforme OHADA', 'Respect des normes comptables en vigueur.'],
                ['🧾', 'Écritures Automatisées', 'Saisie comptable intelligente et rapide.'],
                ['🏛️', 'Fiscalité Intégrée', 'TVA et déclarations simplifiées.'],
                ['🔒', 'Sécurité des Données', 'Confidentialité et chiffrement avancé de vos données.'],
            ] as $card)
            <div class="border border-gray-200 rounded-xl p-5 text-center">
                @if($card[0])
                <div class="text-2xl mb-2">{{ $card[0] }}</div>
                @else
                <div class="w-8 h-8 mx-auto mb-2 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @endif
                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $card[1] }}</h3>
                <p class="text-gray-500 text-xs leading-relaxed">{{ $card[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Mockup produit --}}
    <div class="max-w-5xl mx-auto mt-14">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-2xl shadow-gray-200/60 overflow-hidden">
            <div class="flex items-center gap-1.5 px-4 py-3 border-b border-gray-100">
                <div class="w-2.5 h-2.5 bg-gray-200 rounded-full"></div>
                <div class="w-2.5 h-2.5 bg-gray-200 rounded-full"></div>
                <div class="w-2.5 h-2.5 bg-gray-200 rounded-full"></div>
                <span class="text-xs text-gray-400 ml-2">ecompta360.com/tableau-de-bord</span>
            </div>
            <div class="grid grid-cols-4">
                <div class="hidden sm:block bg-gray-50 border-r border-gray-100 p-4 space-y-1 text-xs text-gray-500">
                    <div class="bg-emerald-50 text-emerald-800 font-semibold rounded-lg px-3 py-2">Tableau de bord</div>
                    <div class="px-3 py-2">Factures</div>
                    <div class="px-3 py-2">Journal des écritures</div>
                    <div class="px-3 py-2">Exporter</div>
                </div>
                <div class="col-span-4 sm:col-span-3 p-5">
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-xs text-gray-400">Factures ce mois</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">48</p>
                        </div>
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-xs text-gray-400">Validées</p>
                            <p class="text-xl font-bold text-emerald-700 mt-1">45</p>
                        </div>
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-xs text-gray-400">En attente</p>
                            <p class="text-xl font-bold text-amber-600 mt-1">3</p>
                        </div>
                    </div>
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-400 uppercase">
                                <tr><th class="px-3 py-2 text-left">Facture</th><th class="px-3 py-2 text-left">Fournisseur</th><th class="px-3 py-2 text-right">Montant</th><th class="px-3 py-2 text-center">Statut</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach([['FACT-2026-014','Fournitures bureau','12 500 F','Validé'],['FACT-2026-015','Carburant véhicule','8 000 F','Validé'],['FACT-2026-016','Services informatiques','25 000 F','En attente']] as $row)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-700">{{ $row[0] }}</td>
                                    <td class="px-3 py-2 text-gray-500">{{ $row[1] }}</td>
                                    <td class="px-3 py-2 text-right text-gray-700">{{ $row[2] }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $row[3]==='Validé' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $row[3] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ TOUT CE DONT VOUS AVEZ BESOIN — grille d'icônes minimale ═══════════ --}}
<section id="fonctions" class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Fonctionnalités</span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mt-2">Tout ce dont vous avez besoin</h2>
            <p class="text-gray-500 mt-2">Une suite complète conçue pour les cabinets et entreprises d'Afrique de l'Ouest.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-gray-100 border border-gray-100 rounded-2xl overflow-hidden">
            @foreach([
                ['bg-blue-100 text-blue-600','M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z','Comptabilité SYSCOHADA','Journaux, plan comptable révisé et écritures générées automatiquement à chaque facture.'],
                ['bg-orange-100 text-orange-600','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Traitement automatique','Uploadez une facture PDF ou image, elle est lue et comptabilisée en quelques secondes.'],
                ['bg-purple-100 text-purple-600','M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','Trésorerie & Mobile Money','Suivez vos règlements MTN MoMo, Moov Money et carte via FeexPay.'],
                ['bg-teal-100 text-teal-600','M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Export multi-format','Excel, CSV et FEC (fichier des écritures comptables) pour votre expert-comptable.'],
                ['bg-pink-100 text-pink-600','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','Multi-utilisateurs & rôles','Administrateur, comptable, auditeur — chacun avec les droits qui lui conviennent.'],
                ['bg-amber-100 text-amber-600','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','Notes de frais','Catégorisez les dépenses par type de charge et exportez-les facilement.'],
                ['bg-sky-100 text-sky-600','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Déclarations fiscales','Retrouvez rapidement les données nécessaires à vos déclarations TVA et AIB.'],
                ['bg-indigo-100 text-indigo-600','M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','Sécurité & isolation','Chaque cabinet dispose d\'un espace de données entièrement cloisonné, chiffré en HTTPS.'],
                ['bg-emerald-100 text-emerald-700','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Conformité DGI Bénin','Régimes B et D, AIB 1%, RIRF — la fiscalité béninoise déjà intégrée.'],
            ] as $f)
            <div class="bg-white p-6">
                <div class="w-10 h-10 {{ $f[0] }} rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f[1] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1.5">{{ $f[2] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $f[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ VOTRE COMPTABLE TRAVAILLE PENDANT QUE VOUS VENDEZ ═══════════════════ --}}
<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Comptabilité automatisée</span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mt-2">
                Votre comptabilité travaille<br>pendant que vous vendez
            </h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto">
                Fini les doubles saisies et les fins de mois chaotiques. Chaque facture uploadée génère
                automatiquement les écritures SYSCOHADA conformes.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 items-start">
            {{-- Aperçu journal --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <span class="text-sm font-semibold text-gray-700">Journal des Ventes — VE</span>
                    <span class="text-xs text-gray-400">Juillet 2026</span>
                </div>
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-400 uppercase">
                        <tr><th class="px-4 py-2 text-left">Compte</th><th class="px-4 py-2 text-left">Libellé</th><th class="px-4 py-2 text-right">Débit</th><th class="px-4 py-2 text-right">Crédit</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 font-mono">
                        <tr><td class="px-4 py-2.5 text-gray-700">4111</td><td class="px-4 py-2.5 text-gray-500 font-sans">Client Diallo & Frères</td><td class="px-4 py-2.5 text-right text-gray-800">652 540</td><td class="px-4 py-2.5 text-right text-gray-300">—</td></tr>
                        <tr><td class="px-4 py-2.5 text-gray-700">7011</td><td class="px-4 py-2.5 text-gray-500 font-sans">Ventes de marchandises</td><td class="px-4 py-2.5 text-right text-gray-300">—</td><td class="px-4 py-2.5 text-right text-gray-800">552 000</td></tr>
                        <tr><td class="px-4 py-2.5 text-gray-700">4431</td><td class="px-4 py-2.5 text-gray-500 font-sans">TVA facturée sur ventes</td><td class="px-4 py-2.5 text-right text-gray-300">—</td><td class="px-4 py-2.5 text-right text-gray-800">100 540</td></tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold text-gray-700">
                        <tr><td colspan="2" class="px-4 py-2.5 text-right uppercase text-[10px] text-gray-400">Total</td><td class="px-4 py-2.5 text-right">652 540</td><td class="px-4 py-2.5 text-right">652 540</td></tr>
                    </tfoot>
                </table>
                <div class="px-4 py-2.5 border-t border-gray-100">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 text-xs font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Journal équilibré
                    </span>
                </div>
            </div>

            {{-- Liste features + stats --}}
            <div class="space-y-6">
                @foreach([
                    ['M13 10V3L4 14h7v7l9-11h-7z','Écritures automatiques','Chaque facture uploadée génère instantanément les écritures conformes au plan SYSCOHADA.'],
                    ['M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Journal & export en un clic','Consultez le journal des écritures à jour et exportez-le en Excel, CSV ou FEC.'],
                    ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Taxes calculées automatiquement','TVA, AIB et RIRF appliqués selon les taux en vigueur au Bénin.'],
                ] as $item)
                <div class="flex gap-4">
                    <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item[0] }}"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">{{ $item[1] }}</h3>
                        <p class="text-gray-500 text-sm mt-0.5">{{ $item[2] }}</p>
                    </div>
                </div>
                @endforeach

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-2xl font-black text-gray-900">100%</p>
                        <p class="text-xs text-gray-400 mt-0.5">Conforme plan comptable SYSCOHADA</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-2xl font-black text-gray-900">0h</p>
                        <p class="text-xs text-gray-400 mt-0.5">De ressaisie comptable manuelle</p>
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="inline-block bg-emerald-800 text-white font-semibold px-6 py-3 rounded-lg hover:bg-emerald-900 transition">
                    Démarrer gratuitement →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══ ILS FONT CONFIANCE ═══════════════════════════════════════════════ --}}
<section class="py-20 bg-white overflow-hidden">
    <div class="max-w-4xl mx-auto text-center px-4 mb-10">
        <h2 class="text-3xl md:text-4xl font-black text-gray-900">Ils font confiance à eCompta360</h2>
        <p class="text-gray-500 mt-2">Des cabinets qui pilotent déjà leur comptabilité avec eCompta360.</p>
    </div>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-white to-transparent z-10"></div>
        <div class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-white to-transparent z-10"></div>
        <div class="flex w-max marquee-track">
            @for($r = 0; $r < 2; $r++)
                @foreach(['AFM', 'Cabinet ICODE'] as $client)
                    @for($n = 0; $n < 4; $n++)
                    <div class="flex items-center justify-center mx-6 px-8 py-4 border border-gray-200 rounded-xl">
                        <span class="font-bold text-gray-400 text-lg whitespace-nowrap">{{ $client }}</span>
                    </div>
                    @endfor
                @endforeach
            @endfor
        </div>
    </div>
</section>

{{-- ══ 3 ÉTAPES SIMPLES ═════════════════════════════════════════════════ --}}
<section id="suite" class="py-20 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">Votre gestion en 3 étapes simples</h2>
        <p class="text-gray-500 mb-12">Passez du papier au numérique en quelques minutes, sans expertise comptable préalable.</p>
        <div class="grid md:grid-cols-3 gap-6 text-left">
            @foreach([
                ['1','M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','Inscrivez-vous','Créez votre compte gratuitement en 30 secondes. Pas de carte requise.'],
                ['2','M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12','Uploadez vos factures','PDF ou image — le traitement démarre automatiquement, sans configuration.'],
                ['3','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','Validez & exportez','Vérifiez les écritures générées, validez, puis exportez pour votre expert-comptable.'],
            ] as $step)
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <span class="text-xs font-bold text-emerald-700">{{ $step[0] }}</span>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center my-3">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step[1] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1.5">{{ $step[2] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TARIFS ═══════════════════════════════════════════════════════════ --}}
<section id="tarifs" class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">Des offres adaptées à votre activité</h2>
            <p class="text-gray-500 text-lg">Commencez gratuitement. Évoluez à votre rythme.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-5">
            @foreach($plans as $plan)
            @php $pop = $plan->slug === 'pro'; @endphp
            <div class="relative rounded-2xl overflow-hidden {{ $pop ? 'border-2 border-emerald-800 shadow-xl' : 'border border-gray-200' }}">
                @if($pop)
                <div class="text-center py-1.5 text-xs font-bold text-white uppercase tracking-widest bg-emerald-800">
                    Le plus choisi
                </div>
                @endif
                <div class="p-6 bg-white">
                    <div class="font-black text-xl mb-1 text-gray-900">{{ $plan->nom }}</div>
                    <div class="mt-3 mb-5">
                        <span class="text-3xl font-black text-gray-900">
                            @if($plan->prix_mensuel_xof === 0) Gratuit
                            @else {{ number_format($plan->prix_mensuel_xof, 0, ',', ' ') }}
                            @endif
                        </span>
                        <span class="text-sm text-gray-400">
                            @if($plan->prix_mensuel_xof > 0) FCFA/mois @else · 15 jours @endif
                        </span>
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
                            <span class="{{ $feat[2] ? 'text-emerald-600' : 'text-gray-300' }}">{{ $feat[2] ? '✓' : '✗' }}</span>
                            <span class="text-gray-600">{{ $feat[1] }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register', ['plan' => $plan->slug]) }}"
                       class="block text-center font-bold py-3 rounded-lg transition text-sm
                              {{ $pop ? 'bg-emerald-800 text-white hover:bg-emerald-900' : 'bg-gray-50 text-gray-800 hover:bg-gray-100 border border-gray-200' }}">
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
<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Ce que nos clients pensent de nous</h2>
            <p class="text-gray-500">Des cabinets qui ont transformé leur quotidien avec eCompta360.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['AM','Adjoua M.','Expert-comptable, Cotonou','bg-blue-100 text-blue-700','"eCompta360 a réduit de moitié le temps passé sur la saisie. Il reconnaît parfaitement les factures en XOF et génère les bons comptes SYSCOHADA automatiquement. Je recommande à tous mes confrères."'],
                ['KO','Kofi Ouedraogo','Cabinet comptable, Porto-Novo','bg-purple-100 text-purple-700','"Le gain de temps est impressionnant. Ce qui prenait 3 heures ne prend plus que 20 minutes. L\'intégration avec Mobile Money est un vrai plus — je règle mon abonnement depuis mon téléphone."'],
                ['FS','Fatou Sow','Directrice, Abomey-Calavi','bg-amber-100 text-amber-700','"Enfin une solution de comptabilité pensée pour l\'Afrique ! Le respect du SYSCOHADA Révisé est irréprochable. Mon équipe a été opérationnelle dès le premier jour sans formation particulière."'],
            ] as $t)
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
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
<section id="faq" class="py-20 px-4 bg-white" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-black text-center text-gray-900 mb-10">Questions fréquentes</h2>
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
            <div class="border border-gray-200 rounded-xl overflow-hidden">
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

{{-- ══ CTA FINAL ═══════════════════════════════════════════════════════ --}}
<section id="contact" class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto dark-cta rounded-3xl py-16 px-6 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-black mb-3">Prêt à transformer votre gestion ?</h2>
        <p class="text-green-200 mb-8 text-lg max-w-lg mx-auto">
            Rejoignez les cabinets qui font confiance à eCompta360 pour piloter leur comptabilité dès aujourd'hui.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="bg-white text-emerald-900 font-bold px-8 py-3.5 rounded-lg hover:bg-green-50 transition">
                Démarrer gratuitement →
            </a>
            <a href="#contact"
               class="border border-white/25 text-white font-semibold px-8 py-3.5 rounded-lg hover:bg-white/10 transition">
                Contacter l'équipe
            </a>
        </div>
        <p class="text-green-300 text-xs mt-6">Gratuit pour démarrer · Sans carte bancaire · Sans engagement</p>
    </div>
</section>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
<footer class="bg-white border-t border-gray-100 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-7 h-7 bg-emerald-800 rounded-md flex items-center justify-center font-black text-white text-sm">e</div>
                    <span class="font-bold text-gray-900">eCompta360</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs">
                    Plateforme de gestion comptable pour cabinets et entreprises d'Afrique de l'Ouest.
                    Conforme SYSCOHADA Révisé — Zone OHADA, Bénin.
                </p>
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-sm mb-4">Navigation</p>
                <div class="space-y-2 text-sm text-gray-500">
                    <a href="#fonctions" class="block hover:text-gray-900 transition">Fonctionnalités</a>
                    <a href="#tarifs" class="block hover:text-gray-900 transition">Nos offres</a>
                    <a href="#faq" class="block hover:text-gray-900 transition">FAQ</a>
                    <a href="#contact" class="block hover:text-gray-900 transition">Contact</a>
                </div>
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-sm mb-4">Compte</p>
                <div class="space-y-2 text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="block hover:text-gray-900 transition">Se connecter</a>
                    <a href="{{ route('register') }}" class="block hover:text-gray-900 transition">Créer un espace</a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-gray-400">
            <p>© {{ date('Y') }} eCompta360 · Tous droits réservés</p>
            <div class="flex items-center gap-5">
                <a href="{{ route('legal.privacy') }}" class="hover:text-gray-700 transition">Confidentialité</a>
                <a href="{{ route('legal.cookies') }}" class="hover:text-gray-700 transition">Cookies</a>
                <a href="{{ route('legal.mentions') }}" class="hover:text-gray-700 transition">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
