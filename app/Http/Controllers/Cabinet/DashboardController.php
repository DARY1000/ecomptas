<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\EcritureComptable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $tenant   = $user->tenant;
        $tenantId = $user->tenant_id;

        // ── KPIs du mois courant ──────────────────────────────────────
        $facturesCeMois   = Facture::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $facturesAValider = Facture::where('tenant_id', $tenantId)
            ->where('statut', 'a_valider')
            ->count();

        $facturesEnCours  = Facture::where('tenant_id', $tenantId)
            ->where('statut', 'traitement_en_cours')
            ->count();

        $facturesErreur   = Facture::where('tenant_id', $tenantId)
            ->where('statut', 'erreur')
            ->count();

        // ── Montants du mois ─────────────────────────────────────────
        $totalAchats = Facture::where('tenant_id', $tenantId)
            ->where('type_document', 'ACHAT')
            ->where('statut', 'valide')
            ->whereMonth('date_facture', now()->month)
            ->whereYear('date_facture', now()->year)
            ->sum('montant_ttc');

        $totalVentes = Facture::where('tenant_id', $tenantId)
            ->where('type_document', 'VENTE')
            ->where('statut', 'valide')
            ->whereMonth('date_facture', now()->month)
            ->whereYear('date_facture', now()->year)
            ->sum('montant_ttc');

        // ── Dernières factures ────────────────────────────────────────
        $dernieresFactures = Facture::where('tenant_id', $tenantId)
            ->with('uploadePar:id,name')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // ── Répartition par type ─────────────────────────────────────
        $repartitionTypes = Facture::where('tenant_id', $tenantId)
            ->where('statut', 'valide')
            ->whereMonth('created_at', now()->month)
            ->selectRaw('type_document, COUNT(*) as total')
            ->groupBy('type_document')
            ->pluck('total', 'type_document');

        return view('dashboard.index', compact(
            'tenant',
            'facturesCeMois', 'facturesAValider', 'facturesEnCours', 'facturesErreur',
            'totalAchats', 'totalVentes',
            'dernieresFactures', 'repartitionTypes'
        ));
    }
}
