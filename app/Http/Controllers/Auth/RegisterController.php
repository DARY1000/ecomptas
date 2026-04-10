<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $plans     = Plan::actifs();
        $planSlug  = $request->get('plan', 'trial');
        return view('auth.register', compact('plans', 'planSlug'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'cabinet_nom'    => 'required|string|max:150',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'telephone'      => 'nullable|string|max:20',
            'ifu'            => 'nullable|string|max:13',
            'regime_fiscal'  => 'required|in:B,D',
            'plan_slug'      => 'required|exists:plans,slug',
        ]);

        $plan = Plan::where('slug', $validated['plan_slug'])->firstOrFail();

        // Générer un slug unique pour le cabinet
        $slug = \Illuminate\Support\Str::slug($validated['cabinet_nom']);
        $baseSlug = $slug;
        $i = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // Créer le tenant (cabinet)
        $tenant = Tenant::create([
            'nom'                    => $validated['cabinet_nom'],
            'slug'                   => $slug,
            'email_contact'          => $validated['email'],
            'telephone'              => $validated['telephone'] ?? null,
            'ifu'                    => $validated['ifu'] ?? null,
            'plan'                   => $plan->slug,
            'quota_factures_mensuel' => $plan->quota_factures,
            'quota_users'            => $plan->quota_users,
            'statut'                 => 'trial',
            'actif'                  => true,
            'abonnement_expire_le'   => now()->addDays(30),
        ]);

        // Créer l'utilisateur admin du cabinet
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['cabinet_nom'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'admin',
            'actif'     => true,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('succes', "Bienvenue sur eCompta360 ! Votre période d'essai de 30 jours a démarré.");
    }
}
