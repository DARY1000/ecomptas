<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('ordre')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:100',
            'slug'              => 'required|string|max:50|unique:plans,slug',
            'prix_mensuel_xof'  => 'required|integer|min:0',
            'quota_factures'    => 'required|integer|min:1',
            'quota_users'       => 'required|integer|min:1',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'export_xlsx'       => 'boolean',
            'google_sheets'     => 'boolean',
            'api_access'        => 'boolean',
            'actif'             => 'boolean',
            'ordre'             => 'required|integer|min:0',
        ]);

        $validated['export_xlsx']   = $request->boolean('export_xlsx');
        $validated['google_sheets'] = $request->boolean('google_sheets');
        $validated['api_access']    = $request->boolean('api_access');
        $validated['actif']         = $request->boolean('actif', true);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('succes', "Plan « {$validated['nom']} » créé avec succès.");
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:100',
            'prix_mensuel_xof'  => 'required|integer|min:0',
            'quota_factures'    => 'required|integer|min:1',
            'quota_users'       => 'required|integer|min:1',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'export_xlsx'       => 'boolean',
            'google_sheets'     => 'boolean',
            'api_access'        => 'boolean',
            'actif'             => 'boolean',
            'ordre'             => 'required|integer|min:0',
        ]);

        $validated['export_xlsx']   = $request->boolean('export_xlsx');
        $validated['google_sheets'] = $request->boolean('google_sheets');
        $validated['api_access']    = $request->boolean('api_access');
        $validated['actif']         = $request->boolean('actif', true);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('succes', "Plan « {$plan->nom} » mis à jour.");
    }

    public function destroy(Plan $plan)
    {
        if ($plan->slug === 'trial') {
            return back()->withErrors(['plan' => 'Le plan Trial ne peut pas être supprimé.']);
        }
        $plan->update(['actif' => false]);
        return back()->with('succes', "Plan « {$plan->nom} » désactivé.");
    }
}
