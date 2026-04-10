<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — eCompta360</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen py-8 px-4">

<div class="max-w-2xl mx-auto">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                <span class="text-blue-900 font-black text-xl">e</span>
            </div>
            <span class="text-white font-bold text-xl">eCompta360</span>
        </a>
        <p class="text-blue-200 text-sm mt-2">Créez votre espace cabinet en 2 minutes</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Créer votre cabinet</h2>
        <p class="text-gray-500 text-sm mb-6">30 jours d'essai gratuit · Aucune carte requise</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" x-data="{ plan: '{{ $planSlug }}' }">
            @csrf

            <div class="grid md:grid-cols-2 gap-5">

                {{-- Nom du cabinet --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du cabinet *</label>
                    <input type="text" name="cabinet_nom" value="{{ old('cabinet_nom') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="Cabinet ACME Expertise Comptable">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email administrateur *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="vous@cabinet.bj">
                </div>

                {{-- Téléphone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="+229 97 000 000">
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="8 caractères minimum">
                </div>

                {{-- Confirmation --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="Répéter le mot de passe">
                </div>

                {{-- IFU --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro IFU</label>
                    <input type="text" name="ifu" value="{{ old('ifu') }}" maxlength="13"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="1234567890123">
                </div>

                {{-- Régime fiscal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Régime fiscal *</label>
                    <select name="regime_fiscal" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                        <option value="">-- Choisir --</option>
                        <option value="B" {{ old('regime_fiscal') === 'B' ? 'selected' : '' }}>Régime B — Normal</option>
                        <option value="D" {{ old('regime_fiscal') === 'D' ? 'selected' : '' }}>Régime D — Simplifié</option>
                    </select>
                </div>

            </div>

            {{-- Choix du plan --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Choisir votre plan</label>
                <input type="hidden" name="plan_slug" :value="plan">
                <div class="grid md:grid-cols-4 gap-3">
                    @foreach($plans as $p)
                    <label class="cursor-pointer">
                        <input type="radio" x-model="plan" value="{{ $p->slug }}" class="sr-only">
                        <div :class="plan === '{{ $p->slug }}'
                                 ? 'border-2 border-blue-600 bg-blue-50 ring-2 ring-blue-200'
                                 : 'border-2 border-gray-200 hover:border-blue-300'"
                             class="rounded-xl p-3 text-center transition">
                            <div class="font-bold text-gray-900 text-sm">{{ $p->nom }}</div>
                            <div class="text-xs mt-1 font-semibold
                                        {{ $p->prix_mensuel_xof === 0 ? 'text-green-600' : 'text-blue-700' }}">
                                {{ $p->prix_formatté }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ $p->quota_factures >= 9999 ? '∞ factures' : $p->quota_factures . ' factures' }}
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="mt-8 w-full bg-blue-700 text-white py-3.5 rounded-xl font-bold hover:bg-blue-800 transition text-base shadow">
                Créer mon espace cabinet →
            </button>

            <p class="text-center text-gray-400 text-xs mt-4">
                En vous inscrivant, vous acceptez nos conditions d'utilisation.
                Votre essai de 30 jours démarre immédiatement.
            </p>
        </form>
    </div>

    <p class="text-center text-blue-200 text-sm mt-6">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">Se connecter</a>
    </p>
</div>

</body>
</html>
