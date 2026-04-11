<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function monitoring(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));

        [$annee, $m] = explode('-', $mois);

        // Statistiques IA par statut ce mois
        $statsStatuts = Facture::whereYear('created_at', $annee)
            ->whereMonth('created_at', $m)
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Top 10 cabinets par volume de factures
        $topCabinets = Facture::whereYear('created_at', $annee)
            ->whereMonth('created_at', $m)
            ->selectRaw('tenant_id, COUNT(*) as total')
            ->groupBy('tenant_id')
            ->orderByDesc('total')
            ->with('tenant:id,nom')
            ->limit(10)
            ->get();

        // Factures en erreur récentes
        $facturesErreur = Facture::where('statut', 'erreur')
            ->with('tenant:id,nom')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        // Factures en cours depuis plus de 2h (bloquées)
        $facturesBloquees = Facture::where('statut', 'traitement_en_cours')
            ->where('updated_at', '<', now()->subHours(2))
            ->with('tenant:id,nom')
            ->orderBy('updated_at')
            ->limit(20)
            ->get();

        $totalCeMois = Facture::whereYear('created_at', $annee)->whereMonth('created_at', $m)->count();

        return view('admin.monitoring', compact(
            'statsStatuts', 'topCabinets', 'facturesErreur',
            'facturesBloquees', 'mois', 'totalCeMois'
        ));
    }

    public function quotas()
    {
        $tenants = Tenant::where('actif', true)
            ->orderByDesc('quota_factures_mensuel')
            ->get()
            ->map(function ($t) {
                $used = $t->facturesCeMois();
                $max  = $t->quota_factures_mensuel;
                $t->quota_used = $used;
                $t->quota_pct  = $max > 0 ? min(100, round($used / $max * 100)) : 0;
                return $t;
            })
            ->sortByDesc('quota_pct');

        return view('admin.quotas', compact('tenants'));
    }

    public function users(Request $request)
    {
        $query = User::with('tenant:id,nom')->orderBy('name');

        if ($request->q) {
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%")
            );
        }
        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(30)->withQueryString();

        $statsRoles = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.users', compact('users', 'statsRoles'));
    }
}
