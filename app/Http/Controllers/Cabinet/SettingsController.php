<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        return view('cabinet.settings.index', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'nom'            => 'required|string|max:150',
            'sigle'          => 'nullable|string|max:30',
            'email_contact'  => 'required|email',
            'telephone'      => 'nullable|string|max:20',
            'site_web'       => 'nullable|url|max:255',
            'adresse'        => 'nullable|string|max:255',
            'ville'          => 'nullable|string|max:100',
            'ifu'            => 'nullable|string|max:13',
            'rccm'           => 'nullable|string|max:50',
            'regime_fiscal'  => ['required', Rule::in(['B', 'D'])],
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,svg|max:512',
            // Google Sheets
            'spreadsheet_id' => 'nullable|string',
        ]);

        // Mise à jour des infos légales et fiscales — reprises sur les factures,
        // exports et futures déclarations fiscales.
        $tenant->update([
            'nom'           => $validated['nom'],
            'sigle'         => $validated['sigle'] ?? null,
            'email_contact' => $validated['email_contact'],
            'telephone'     => $validated['telephone'] ?? null,
            'site_web'      => $validated['site_web'] ?? null,
            'adresse'       => $validated['adresse'] ?? null,
            'ville'         => $validated['ville'] ?? null,
            'ifu'           => $validated['ifu'] ?? null,
            'rccm'          => $validated['rccm'] ?? null,
            'regime_fiscal' => $validated['regime_fiscal'],
        ]);

        if ($request->hasFile('logo')) {
            if ($tenant->logo_path && Storage::disk('public')->exists($tenant->logo_path)) {
                Storage::disk('public')->delete($tenant->logo_path);
            }

            $path = $request->file('logo')->store("tenants/{$tenant->id}", 'public');
            $tenant->update(['logo_path' => $path]);
        }

        // Mise à jour config Google Sheets si fournie
        if ($request->filled('spreadsheet_id')) {
            $config = $tenant->config_google_sheets ?? [];
            $config['spreadsheet_id'] = $validated['spreadsheet_id'];
            $tenant->update(['config_google_sheets' => $config]);
        }

        return back()->with('succes', 'Paramètres du cabinet mis à jour.');
    }
}
