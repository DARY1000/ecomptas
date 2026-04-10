<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — eCompta360 Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-gray-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center font-bold text-sm text-gray-900">e</div>
                    <span class="font-bold">eCompta360 — Admin DRAWIS</span>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-yellow-300 {{ request()->routeIs('admin.dashboard') ? 'text-yellow-300' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.tenants.index') }}" class="hover:text-yellow-300 {{ request()->routeIs('admin.tenants.*') ? 'text-yellow-300' : '' }}">Cabinets</a>
                    <a href="{{ route('admin.plans.index') }}" class="hover:text-yellow-300 {{ request()->routeIs('admin.plans.*') ? 'text-yellow-300' : '' }}">Plans</a>
                    <a href="{{ route('landing') }}" class="hover:text-yellow-300" target="_blank">Site public ↗</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-red-400 hover:text-red-300">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        @if(session('succes'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-4">
                ✅ {{ session('succes') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded-lg mb-4">
                ℹ️ {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg mb-4">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        @yield('content')
    </main>
</body>
</html>
