@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')

@section('content')
<div class="space-y-5">

    {{-- Stats rôles --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $roleConfig = [
            'super_admin' => ['label'=>'Super Admins', 'bg'=>'bg-red-50',    'text'=>'text-red-700'],
            'admin'       => ['label'=>'Admins',       'bg'=>'bg-blue-50',   'text'=>'text-blue-700'],
            'comptable'   => ['label'=>'Comptables',   'bg'=>'bg-green-50',  'text'=>'text-green-700'],
            'auditeur'    => ['label'=>'Auditeurs',    'bg'=>'bg-purple-50', 'text'=>'text-purple-700'],
        ];
        @endphp
        @foreach($roleConfig as $role => $cfg)
        <div class="{{ $cfg['bg'] }} rounded-xl p-4 text-center border border-white">
            <div class="text-2xl font-black {{ $cfg['text'] }}">{{ $statsRoles[$role] ?? 0 }}</div>
            <div class="text-xs {{ $cfg['text'] }} mt-1 font-medium opacity-80">{{ $cfg['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher nom ou email…"
                   class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <select name="role" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">Tous les rôles</option>
                <option value="admin"       {{ request('role')==='admin'       ? 'selected':'' }}>Admin</option>
                <option value="comptable"   {{ request('role')==='comptable'   ? 'selected':'' }}>Comptable</option>
                <option value="auditeur"    {{ request('role')==='auditeur'    ? 'selected':'' }}>Auditeur</option>
                <option value="super_admin" {{ request('role')==='super_admin' ? 'selected':'' }}>Super Admin</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Filtrer</button>
                @if(request()->hasAny(['q','role']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-500">✕</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($users->isEmpty())
            <p class="px-6 py-12 text-center text-gray-400">Aucun utilisateur trouvé.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Utilisateur</th>
                        <th class="px-5 py-3 text-left">Cabinet</th>
                        <th class="px-5 py-3 text-center">Rôle</th>
                        <th class="px-5 py-3 text-left">Créé le</th>
                        <th class="px-5 py-3 text-left">Dernière connexion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $u)
                    @php
                    $roleColors = [
                        'super_admin' => 'bg-red-100 text-red-700',
                        'admin'       => 'bg-blue-100 text-blue-700',
                        'comptable'   => 'bg-green-100 text-green-700',
                        'auditeur'    => 'bg-purple-100 text-purple-700',
                    ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @if($u->tenant)
                                <a href="{{ route('admin.tenants.show', $u->tenant_id) }}"
                                   class="text-blue-700 hover:underline font-medium">{{ $u->tenant->nom }}</a>
                            @else
                                <span class="text-gray-400 text-xs italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $roleColors[$u->role] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">
                            {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
