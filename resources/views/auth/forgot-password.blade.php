<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — ComptaSaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Réinitialiser le mot de passe</h2>
        <p class="text-sm text-gray-500 mb-6">Entrez votre email pour recevoir un lien de réinitialisation.</p>

        @if(session('status'))
            <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="votre@email.com"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="w-full bg-blue-900 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                Envoyer le lien
            </button>
        </form>
        <a href="{{ route('login') }}" class="block text-center text-sm text-blue-600 mt-4">
            ← Retour à la connexion
        </a>
    </div>
</body>
</html>
