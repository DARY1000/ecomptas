<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name'        => config('app.name', 'eCompta360'),
            'n8n_webhook_url' => env('N8N_WEBHOOK_URL', ''),
            'n8n_secret'      => env('N8N_SECRET', ''),
            'mail_from'       => env('MAIL_FROM_ADDRESS', ''),
            'logo_path'       => config('app.logo_path', null),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:512',
        ]);

        // Supprimer l'ancien logo
        $oldPath = config('app.logo_path');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Stocker le nouveau
        $path = $request->file('logo')->storeAs('', 'logo.' . $request->file('logo')->extension(), 'public');

        // Mettre à jour le .env
        $this->setEnvValue('APP_LOGO_PATH', $path);

        return back()->with('succes', 'Logo mis à jour avec succès.');
    }

    public function updateEnv(Request $request)
    {
        $request->validate([
            'n8n_webhook_url' => 'nullable|url',
            'n8n_secret'      => 'nullable|string|min:8',
            'mail_from'       => 'nullable|email',
            'mail_from_name'  => 'nullable|string|max:100',
        ]);

        $map = [
            'N8N_WEBHOOK_URL'   => $request->n8n_webhook_url,
            'N8N_SECRET'        => $request->n8n_secret,
            'MAIL_FROM_ADDRESS' => $request->mail_from,
            'MAIL_FROM_NAME'    => $request->mail_from_name,
        ];

        foreach ($map as $key => $value) {
            if ($value !== null) {
                $this->setEnvValue($key, $value);
            }
        }

        return back()->with('succes', 'Paramètres mis à jour. Videz le cache de configuration.');
    }

    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;

        $content = file_get_contents($path);
        $escaped = preg_quote($key, '/');

        if (preg_match("/^{$escaped}=.*/m", $content)) {
            $content = preg_replace("/^{$escaped}=.*/m", "{$key}=\"{$value}\"", $content);
        } else {
            $content .= "\n{$key}=\"{$value}\"";
        }

        file_put_contents($path, $content);
    }
}
