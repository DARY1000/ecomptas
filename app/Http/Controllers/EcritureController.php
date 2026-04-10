<?php

namespace App\Http\Controllers;

use App\Models\EcritureComptable;
use App\Models\Facture;
use Illuminate\Http\Request;

class EcritureController extends Controller
{
    /**
     * Journal comptable — liste des écritures du cabinet avec filtres.
     */
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $ecritures = EcritureComptable::with('facture')
            ->where('tenant_id', $tenantId)
            ->when($request->q, fn($q, $s) => $q->where(function ($sub) use ($s) {
                $sub->where('numero_compte', 'like', "%{$s}%")
                    ->orWhere('libelle', 'like', "%{$s}%");
            }))
            ->when($request->type, fn($q, $t) => $q->where('type_document', $t))
            ->when($request->date_debut, fn($q) => $q->whereDate('date_ecriture', '>=', $request->date_debut))
            ->when($request->date_fin,   fn($q) => $q->whereDate('date_ecriture', '<=', $request->date_fin))
            ->orderBy('date_ecriture', 'desc')
            ->orderBy('facture_id')
            ->orderBy('ordre_ligne')
            ->paginate(50)
            ->withQueryString();

        // Totaux globaux pour la période filtrée (toutes pages)
        $totauxQuery = EcritureComptable::where('tenant_id', $tenantId)
            ->when($request->type, fn($q, $t) => $q->where('type_document', $t))
            ->when($request->date_debut, fn($q) => $q->whereDate('date_ecriture', '>=', $request->date_debut))
            ->when($request->date_fin,   fn($q) => $q->whereDate('date_ecriture', '<=', $request->date_fin));

        $totalDebit  = $totauxQuery->sum('montant_debit');
        $totalCredit = (clone $totauxQuery)->sum('montant_credit');

        return view('ecritures.index', compact('ecritures', 'totalDebit', 'totalCredit'));
    }
}
