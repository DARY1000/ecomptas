{{-- Formulaire partagé create/edit plan --}}

<div class="grid md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du plan *</label>
        <input type="text" name="nom" value="{{ old('nom', $plan->nom ?? '') }}" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
    </div>

    @if(!isset($plan))
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (identifiant unique) *</label>
        <input type="text" name="slug" value="{{ old('slug') }}" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-mono"
               placeholder="ex: pro-plus">
        <p class="text-xs text-gray-400 mt-1">Minuscules, tirets uniquement. Ne peut pas être modifié après création.</p>
    </div>
    @else
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <input type="text" value="{{ $plan->slug }}" disabled
               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-sm font-mono text-gray-400">
    </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Prix mensuel (FCFA) *</label>
        <input type="number" name="prix_mensuel_xof" value="{{ old('prix_mensuel_xof', $plan->prix_mensuel_xof ?? 0) }}" required min="0"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        <p class="text-xs text-gray-400 mt-1">Mettre 0 pour un plan gratuit.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage *</label>
        <input type="number" name="ordre" value="{{ old('ordre', $plan->ordre ?? 0) }}" required min="0"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Quota factures/mois *</label>
        <input type="number" name="quota_factures" value="{{ old('quota_factures', $plan->quota_factures ?? 10) }}" required min="1"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        <p class="text-xs text-gray-400 mt-1">Mettre 9999 pour illimité.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Quota utilisateurs *</label>
        <input type="number" name="quota_users" value="{{ old('quota_users', $plan->quota_users ?? 1) }}" required min="1"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        <p class="text-xs text-gray-400 mt-1">Mettre 99 pour illimité.</p>
    </div>

</div>

{{-- Options booléennes --}}
<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-3">Fonctionnalités incluses</label>
    <div class="grid md:grid-cols-2 gap-3">

        @php
        $boolFields = [
            'export_xlsx'   => 'Export Excel / CSV',
            'google_sheets' => 'Synchronisation Google Sheets',
            'api_access'    => 'Accès API',
            'actif'         => 'Plan actif (visible sur la landing)',
        ];
        @endphp

        @foreach($boolFields as $field => $label)
        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
            <input type="checkbox" name="{{ $field }}" value="1"
                   {{ old($field, isset($plan) ? ($plan->$field ? '1' : '') : ($field === 'actif' ? '1' : '')) ? 'checked' : '' }}
                   class="w-4 h-4 text-blue-600 rounded border-gray-300">
            <span class="text-sm text-gray-700">{{ $label }}</span>
        </label>
        @endforeach
    </div>
</div>
