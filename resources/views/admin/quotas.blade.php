@extends('layouts.admin')
@section('title', 'Gestion des quotas')
@section('page-title', 'Gestion des quotas')

@section('content')
<div class="space-y-5">

    <p class="text-sm text-gray-500">Consommation des quotas factures/mois par cabinet — {{ now()->translatedFormat('F Y') }}</p>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($tenants->isEmpty())
            <p class="px-5 py-8 text-center text-gray-400">Aucun cabinet actif.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Cabinet</th>
                        <th class="px-5 py-3 text-left">Plan</th>
                        <th class="px-5 py-3 text-center">Utilisé</th>
                        <th class="px-5 py-3 text-center">Quota</th>
                        <th class="px-5 py-3 text-left w-48">Consommation</th>
                        <th class="px-5 py-3 text-center">Statut quota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $t)
                    @php $pct = $t->quota_pct; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.tenants.show', $t) }}"
                               class="font-medium text-blue-700 hover:underline">{{ $t->nom }}</a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-semibold">
                                {{ ucfirst($t->plan ?? 'trial') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $t->quota_used }}</td>
                        <td class="px-5 py-3 text-center text-gray-500">{{ $t->quota_factures_mensuel }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all
                                        {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                         style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-bold w-8 text-right
                                    {{ $pct >= 100 ? 'text-red-600' : ($pct >= 80 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($pct >= 100)
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">Quota atteint</span>
                            @elseif($pct >= 80)
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-semibold">Proche limite</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Normal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
