<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Plan;
use Illuminate\Http\Request;

class AbonnementAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Abonnement::with(['tenant', 'plan'])
            ->orderByDesc('created_at');

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }
        if ($request->plan_id) {
            $query->where('plan_id', $request->plan_id);
        }
        if ($request->mois) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$request->mois]);
        }

        $abonnements = $query->paginate(30)->withQueryString();

        // Statistiques globales
        $revenuTotal     = Abonnement::where('statut', 'actif')->sum('montant_xof');
        $revenuCeMois    = Abonnement::where('statut', 'actif')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('montant_xof');
        $totalAbonnements = Abonnement::where('statut', 'actif')->count();
        $enAttente        = Abonnement::where('statut', 'en_attente')->count();

        // Revenus par plan
        $revenuParPlan = Abonnement::where('statut', 'actif')
            ->with('plan')
            ->get()
            ->groupBy('plan_id')
            ->map(fn($group) => [
                'nom'    => $group->first()->plan?->nom ?? '—',
                'total'  => $group->sum('montant_xof'),
                'count'  => $group->count(),
            ]);

        $plans = Plan::orderBy('ordre')->get();

        return view('admin.abonnements.index', compact(
            'abonnements', 'revenuTotal', 'revenuCeMois',
            'totalAbonnements', 'enAttente', 'revenuParPlan', 'plans'
        ));
    }
}
