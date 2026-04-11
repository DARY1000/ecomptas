@extends('layouts.admin')
@section('title', 'Monitoring IA & OCR')
@section('page-title', 'Monitoring IA & OCR')

@section('content')
<div class="space-y-6">

    {{-- Filtre mois --}}
    <form method="GET" class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-600">Mois :</label>
        <input type="month" name="mois" value="{{ $mois }}"
               class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Voir</button>
        <span class="text-sm text-gray-400">{{ $totalCeMois }} factures traitées</span>
    </form>

    {{-- Stats par statut --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $statutLabels = [
            'uploade'             => ['label'=>'Uploadées',    'bg'=>'bg-gray-50',   'text'=>'text-gray-700',   'dot'=>'bg-gray-400'],
            'traitement_en_cours' => ['label'=>'En cours',     'bg'=>'bg-blue-50',   'text'=>'text-blue-700',   'dot'=>'bg-blue-500'],
            'a_valider'           => ['label'=>'À valider',    'bg'=>'bg-yellow-50', 'text'=>'text-yellow-700', 'dot'=>'bg-yellow-500'],
            'valide'              => ['label'=>'Validées',     'bg'=>'bg-green-50',  'text'=>'text-green-700',  'dot'=>'bg-green-500'],
            'rejete'              => ['label'=>'Rejetées',     'bg'=>'bg-orange-50', 'text'=>'text-orange-700', 'dot'=>'bg-orange-500'],
            'erreur'              => ['label'=>'Erreurs',      'bg'=>'bg-red-50',    'text'=>'text-red-700',    'dot'=>'bg-red-500'],
        ];
        @endphp
        @foreach($statutLabels as $key => $cfg)
        <div class="{{ $cfg['bg'] }} rounded-xl p-4 text-center border border-white">
            <div class="flex justify-center mb-2">
                <span class="w-2.5 h-2.5 rounded-full {{ $cfg['dot'] }}"></span>
            </div>
            <div class="text-2xl font-black {{ $cfg['text'] }}">{{ $statsStatuts[$key] ?? 0 }}</div>
            <div class="text-xs {{ $cfg['text'] }} mt-1 font-medium opacity-80">{{ $cfg['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Top cabinets --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Top cabinets par volume</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topCabinets as $row)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-xs font-black text-blue-700">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $row->tenant?->nom ?? '—' }}</p>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                            @php $pct = $topCabinets->first()->total > 0 ? round($row->total / $topCabinets->first()->total * 100) : 0; @endphp
                            <div class="h-1.5 rounded-full bg-blue-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-700">{{ $row->total }}</span>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Aucune facture ce mois.</p>
                @endforelse
            </div>
        </div>

        {{-- Factures bloquées --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Traitements bloqués (+2h)</h2>
                <span class="text-xs {{ $facturesBloquees->isEmpty() ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} px-2 py-0.5 rounded-full font-bold">
                    {{ $facturesBloquees->count() }}
                </span>
            </div>
            @if($facturesBloquees->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-400 text-center">✅ Aucun traitement bloqué.</p>
            @else
                <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                    @foreach($facturesBloquees as $f)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $f->pdf_nom_original ?? $f->id }}</p>
                            <p class="text-xs text-gray-400">{{ $f->tenant?->nom }} · {{ $f->updated_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">Bloqué</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Factures en erreur --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Factures en erreur (20 dernières)</h2>
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">{{ $facturesErreur->count() }}</span>
        </div>
        @if($facturesErreur->isEmpty())
            <p class="px-5 py-8 text-sm text-gray-400 text-center">✅ Aucune facture en erreur.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Fichier</th>
                        <th class="px-5 py-3 text-left">Cabinet</th>
                        <th class="px-5 py-3 text-left">Dernière MAJ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($facturesErreur as $f)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $f->pdf_nom_original ?? $f->id }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $f->tenant?->nom ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $f->updated_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
