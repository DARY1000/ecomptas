<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            'nom'                  => 'required|string|max:150',
            'email_contact'        => 'required|email',
            'telephone'            => 'nullable|string|max:20',
            'adresse'              => 'nullable|string|max:255',
            'ville'                => 'nullable|string|max:100',
            'ifu'                  => 'nullable|string|max:13',
            // Google Sheets
            'spreadsheet_id'       => 'nullable|string',
        ]);

        // Mise à jour des infos générales
        $tenant->update([
            'nom'           => $validated['nom'],
            'email_contact' => $validated['email_contact'],
            'telephone'     => $validated['telephone'],
            'adresse'       => $validated['adresse'],
            'ville'         => $validated['ville'],
            'ifu'           => $validated['ifu'],
        ]);

        // Mise à jour config Google Sheets si fournie
        if ($request->filled('spreadsheet_id')) {
            $config = $tenant->config_google_sheets ?? [];
            $config['spreadsheet_id'] = $validated['spreadsheet_id'];
            $tenant->update(['config_google_sheets' => $config]);
        }

        return back()->with('succes', 'Paramètres du cabinet mis à jour.');
    }
}
