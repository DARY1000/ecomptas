<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de cookies — eCompta360</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">
<div class="max-w-3xl mx-auto px-4 py-16">
    <a href="{{ route('landing') }}" class="text-blue-700 text-sm hover:underline mb-8 inline-block">← Retour à l'accueil</a>
    <h1 class="text-3xl font-black text-gray-900 mb-2">Politique de cookies</h1>
    <p class="text-gray-400 text-sm mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>
    <div class="space-y-6 text-sm text-gray-600 leading-relaxed">
        <p>eCompta360 utilise uniquement des cookies strictement nécessaires au fonctionnement de la plateforme.</p>
        <h2 class="text-lg font-bold text-gray-900">Cookies utilisés</h2>
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="border border-gray-200 px-3 py-2 text-left font-semibold text-gray-700">Nom</th>
                <th class="border border-gray-200 px-3 py-2 text-left font-semibold text-gray-700">Finalité</th>
                <th class="border border-gray-200 px-3 py-2 text-left font-semibold text-gray-700">Durée</th>
            </tr></thead>
            <tbody>
                <tr><td class="border border-gray-200 px-3 py-2 font-mono text-xs">ecompta360_session</td><td class="border border-gray-200 px-3 py-2">Session d'authentification</td><td class="border border-gray-200 px-3 py-2">Session</td></tr>
                <tr><td class="border border-gray-200 px-3 py-2 font-mono text-xs">XSRF-TOKEN</td><td class="border border-gray-200 px-3 py-2">Protection contre les attaques CSRF</td><td class="border border-gray-200 px-3 py-2">2h</td></tr>
            </tbody>
        </table>
        <p>Aucun cookie de suivi ou de publicité n'est utilisé sur cette plateforme.</p>
    </div>
</div>
</body>
</html>
