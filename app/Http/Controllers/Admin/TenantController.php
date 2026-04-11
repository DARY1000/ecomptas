<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Facture;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * Gestion globale des tenants par le super_admin (iCODE).
 * Accessible uniquement via le middleware role:super_admin.
 */
class TenantController extends Controller
{
    /**
     * Dashboard super admin — vue globale de la plateforme.
     */
    public function dashboard()
    {
        $totalTenants    = Tenant::count();
        $tenantsActifs   = Tenant::where('statut', 'actif')->count();
        $tenantsTrial    = Tenant::where('statut', 'trial')->count();
        $totalUsers      = User::count();
        $facturesTotales = Facture::count();
        $facturesCeMois  = Facture::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();

        // MRR : somme des prix des plans actifs
        $mrr = Tenant::where('statut', 'actif')
            ->join('plans', 'tenants.plan', '=', 'plans.slug')
            ->sum('plans.prix_mensuel_xof');

        // Revenu total cumulé (abonnements actifs)
        $revenuTotal = Abonnement::where('statut', 'actif')->sum('montant_xof');

        $repartitionPlans = Tenant::selectRaw('plan, count(*) as total')
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        $derniersTenants = Tenant::orderByDesc('created_at')->limit(8)->get();

        // Derniers paiements
        $derniersAbonnements = Abonnement::with(['tenant:id,nom', 'plan:id,nom'])
            ->where('statut', 'actif')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Monitoring IA — ce mois
        $facturesValidees  = Facture::where('statut', 'valide')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $facturesEnCours   = Facture::where('statut', 'traitement_en_cours')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $facturesAValider  = Facture::where('statut', 'a_valider')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $facturesErreur    = Facture::where('statut', 'erreur')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        // Essais expirant dans 7 jours
        $trialsExpirant = Tenant::where('statut', 'trial')
            ->whereNotNull('abonnement_expire_le')
            ->whereBetween('abonnement_expire_le', [now(), now()->addDays(7)])
            ->orderBy('abonnement_expire_le')
            ->get();

        return view('admin.dashboard', compact(
            'totalTenants', 'tenantsActifs', 'tenantsTrial', 'totalUsers',
            'facturesTotales', 'facturesCeMois', 'mrr', 'revenuTotal',
            'repartitionPlans', 'derniersTenants', 'derniersAbonnements',
            'facturesValidees', 'facturesEnCours', 'facturesAValider', 'facturesErreur',
            'trialsExpirant'
        ));
    }

    /**
     * Liste de tous les tenants.
     */
    public function index(Request $request)
    {
        $tenants = Tenant::withCount('factures', 'users')
            ->when($request->q, fn($q) =>
                $q->where('nom', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%')
            )
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->plan,   fn($q) => $q->where('plan', $request->plan))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Formulaire de création d'un nouveau cabinet.
     */
    public function create()
    {
        $plans = Plan::actifs();
        return view('admin.tenants.create', compact('plans'));
    }

    /**
     * Crée un nouveau cabinet + son admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:150',
            'email_cabinet'  => 'nullable|email',
            'telephone'      => 'nullable|string|max:20',
            'ifu'            => 'nullable|string|max:13',
            'rccm'           => 'nullable|string|max:50',
            'regime_fiscal'  => 'required|in:B,D',
            'plan_slug'      => 'required|exists:plans,slug',  // alias UI → stocké dans 'plan'
            'statut'         => 'required|in:trial,actif,suspendu',
            'abonnement_expire_le' => 'nullable|date',
            // Admin du cabinet
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8',
        ]);

        /** @var \App\Models\Plan $plan */
        $plan = Plan::where('slug', $validated['plan_slug'])->firstOrFail();

        // Générer un slug unique
        $slug = \Illuminate\Support\Str::slug($validated['nom']);
        $base = $slug; $i = 1;
        while (Tenant::where('slug', $slug)->exists()) { $slug = $base . '-' . $i++; }

        // Créer le tenant
        $tenant = Tenant::create([
            'nom'                   => $validated['nom'],
            'slug'                  => $slug,
            'email_contact'         => $validated['email_cabinet'],
            'telephone'             => $validated['telephone'],
            'ifu'                   => $validated['ifu'],
            'plan'                  => $plan->slug,
            'quota_factures_mensuel'=> $plan->quota_factures,
            'quota_users'           => $plan->quota_users,
            'statut'                => $validated['statut'],
            'actif'                 => true,
            'abonnement_expire_le'  => $validated['abonnement_expire_le'],
        ]);

        // Créer l'administrateur du cabinet
        User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => Hash::make($validated['admin_password']),
            'role'      => 'admin',
        ]);

        return redirect()
            ->route('admin.tenants.show', $tenant)
            ->with('success', "Cabinet {$tenant->nom} créé avec succès.");
    }

    /**
     * Détail d'un tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('users');
        $stats = [
            'total_factures'    => $tenant->factures()->count(),
            'factures_valides'  => $tenant->factures()->where('statut', 'valide')->count(),
            'total_users'       => $tenant->users()->count(),
        ];
        $dernieresFactures = $tenant->factures()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.tenants.show', compact('tenant', 'stats', 'dernieresFactures'));
    }

    /**
     * Suspend un cabinet (bloque l'accès immédiatement).
     */
    public function suspendre(Tenant $tenant)
    {
        $tenant->update(['statut' => 'suspendu', 'actif' => false]);
        return back()->with('info', "Cabinet {$tenant->nom} suspendu.");
    }

    /**
     * Réactive un cabinet suspendu ou expiré.
     */
    public function activer(Tenant $tenant)
    {
        $tenant->update(['statut' => 'actif', 'actif' => true]);
        return back()->with('succes', "Cabinet {$tenant->nom} réactivé.");
    }
}
