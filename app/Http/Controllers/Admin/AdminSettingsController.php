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
            'app_name'          => config('app.name', 'eCompta360'),
            'mistral_api_key'   => env('MISTRAL_API_KEY', ''),
            'openai_api_key'    => env('OPENAI_API_KEY', ''),
            'mistral_ocr_model' => env('MISTRAL_OCR_MODEL', 'mistral-ocr-latest'),
            'openai_model'      => env('OPENAI_MODEL', 'gpt-4o'),
            'mail_from'         => env('MAIL_FROM_ADDRESS', ''),
            'logo_path'         => config('app.logo_path', null),
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
        \Artisan::call('config:cache');

        return back()->with('succes', 'Logo mis à jour avec succès.');
    }

    public function updateEnv(Request $request)
    {
        $request->validate([
            'mistral_api_key'   => 'nullable|string|min:8',
            'openai_api_key'    => 'nullable|string|min:8',
            'mistral_ocr_model' => 'nullable|string|max:100',
            'openai_model'      => 'nullable|string|max:100',
            'mail_from'         => 'nullable|email',
            'mail_from_name'    => 'nullable|string|max:100',
        ]);

        // Champs sensibles : on n'écrase que si l'admin a saisi une nouvelle valeur
        // (le champ est laissé vide au chargement pour ne jamais ré-afficher la clé en clair)
        $map = array_filter([
            'MISTRAL_API_KEY'   => $request->filled('mistral_api_key')   ? $request->mistral_api_key   : null,
            'OPENAI_API_KEY'    => $request->filled('openai_api_key')    ? $request->openai_api_key    : null,
            'MISTRAL_OCR_MODEL' => $request->filled('mistral_ocr_model') ? $request->mistral_ocr_model : null,
            'OPENAI_MODEL'      => $request->filled('openai_model')      ? $request->openai_model       : null,
            'MAIL_FROM_ADDRESS' => $request->filled('mail_from')         ? $request->mail_from          : null,
            'MAIL_FROM_NAME'    => $request->filled('mail_from_name')    ? $request->mail_from_name     : null,
        ], fn ($v) => $v !== null);

        foreach ($map as $key => $value) {
            $this->setEnvValue($key, $value);
        }

        // Sans ça, .env est recharché mais le cache de config (regénéré à chaque déploiement) garde les anciennes valeurs.
        \Artisan::call('config:cache');

        return back()->with('succes', 'Paramètres mis à jour et cache de configuration rechargé.');
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
