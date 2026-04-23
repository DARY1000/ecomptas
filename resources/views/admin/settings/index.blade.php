@extends('layouts.admin')
@section('title', 'Paramètres')
@section('page-title', 'Paramètres')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Logo ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Logo de la plateforme
        </h2>

        <div class="flex items-start gap-6 mb-5">
            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200 overflow-hidden flex-shrink-0">
                @if($settings['logo_path'] && file_exists(public_path('storage/'.$settings['logo_path'])))
                    <img src="{{ asset('storage/'.$settings['logo_path']).'?v='.time() }}"
                         alt="Logo" class="w-full h-full object-contain p-1">
                @else
                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center font-black text-white text-xl">e</div>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                <p class="font-medium text-gray-700 mb-1">Logo actuel</p>
                <p>Format : PNG, JPG ou SVG · Max 512 Ko</p>
                <p>Taille recommandée : <strong>160 × 40 px</strong> (ratio 4:1)</p>
                <p class="text-xs mt-1 text-gray-400">Le logo s'affiche dans la barre de navigation et les emails.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data"
              class="flex items-center gap-3">
            @csrf
            <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml"
                   class="flex-1 block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-lg px-2 py-1.5">
            <button type="submit"
                    class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition whitespace-nowrap">
                Mettre à jour
            </button>
        </form>
    </div>

    {{-- ── Paramètres IA / n8n ───────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            Pipeline IA — n8n
        </h2>
        <p class="text-sm text-gray-400 mb-5">
            eCompta360 envoie les factures uploadées vers un workflow n8n qui extrait les données (OCR + IA)
            et retourne les écritures comptables. Configurez ici les credentials de connexion.
        </p>

        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-5 text-sm text-blue-800">
            <p class="font-semibold mb-1">Comment ça fonctionne :</p>
            <ol class="list-decimal list-inside space-y-1 text-blue-700">
                <li>Le cabinet uploade une facture PDF</li>
                <li>Laravel envoie le fichier au webhook n8n via HTTPS + signature HMAC</li>
                <li>n8n extrait : fournisseur, date, montants, TVA via OCR + modèle IA (GPT-4o / Claude)</li>
                <li>n8n retourne les données à Laravel via un webhook de callback</li>
                <li>Laravel crée les écritures SYSCOHADA et notifie le cabinet</li>
            </ol>
            <p class="mt-2 text-xs text-blue-600">Pour configurer n8n : créez un compte sur n8n.cloud, importez le workflow fourni, activez-le et copiez l'URL du webhook ici.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.env') }}" class="space-y-4">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Webhook n8n</label>
                    <input type="url" name="n8n_webhook_url"
                           value="{{ old('n8n_webhook_url', $settings['n8n_webhook_url']) }}"
                           placeholder="https://your-instance.n8n.cloud/webhook/..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret HMAC</label>
                    <input type="text" name="n8n_secret"
                           value="{{ old('n8n_secret', $settings['n8n_secret'] ? str_repeat('•', min(strlen($settings['n8n_secret']), 20)) : '') }}"
                           placeholder="Clé secrète partagée (min. 32 caractères)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 font-mono">
                    <p class="text-xs text-gray-400 mt-0.5">Laissez vide pour conserver la valeur actuelle.</p>
                </div>
            </div>

            <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                Sauvegarder les paramètres IA
            </button>
        </form>
    </div>

    {{-- ── Email ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Emails transactionnels
        </h2>

        <form method="POST" action="{{ route('admin.settings.env') }}" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email expéditeur</label>
                    <input type="email" name="mail_from"
                           value="{{ old('mail_from', $settings['mail_from']) }}"
                           placeholder="noreply@ecompta360.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom expéditeur</label>
                    <input type="text" name="mail_from_name"
                           value="{{ old('mail_from_name', env('MAIL_FROM_NAME', 'eCompta360')) }}"
                           placeholder="eCompta360"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                Sauvegarder les paramètres email
            </button>
        </form>

        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg text-xs text-yellow-700">
            <strong>Note :</strong> Les emails sont configurés dans le fichier <code>.env</code> (MAIL_MAILER, MAIL_HOST, etc.).
            Ces paramètres modifient directement ce fichier. Un redémarrage du serveur peut être nécessaire.
        </div>
    </div>

</div>
@endsection
