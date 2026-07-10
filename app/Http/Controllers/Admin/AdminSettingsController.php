<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Lire via config() et non env() : une fois la config mise en cache (config:cache,
        // exécuté à chaque déploiement), .env n'est plus chargé et env() retourne toujours null.
        $settings = [
            'app_name'          => config('app.name', 'eCompta360'),
            'mistral_api_key'   => config('services.mistral.api_key', ''),
            'openai_api_key'    => config('services.openai.api_key', ''),
            'mistral_ocr_model'    => config('services.mistral.model', 'mistral-ocr-latest'),
            'mistral_vision_model' => config('services.mistral.vision_model', 'pixtral-large-latest'),
            'openai_model'         => config('services.openai.model', 'gpt-4o'),
            'feexpay_shop_id'   => config('services.feexpay.shop_id', ''),
            'feexpay_token'     => config('services.feexpay.token', ''),
            'feexpay_mode'      => config('services.feexpay.mode', 'SANDBOX'),
            'mail_from'         => config('mail.from.address', ''),
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
        // config:clear (pas cache) : la requête en cours a déjà chargé la config en
        // mémoire AVANT ce point, donc un config:cache ici re-sauvegarderait les
        // anciennes valeurs et écraserait ce qu'on vient d'écrire. config:clear se
        // contente de supprimer le cache existant — la prochaine requête relira le
        // .env à jour normalement (comportement standard hors cache).
        \Artisan::call('config:clear');

        return back()->with('succes', 'Logo mis à jour avec succès.');
    }

    public function updateEnv(Request $request)
    {
        $request->validate([
            'mistral_api_key'   => 'nullable|string|min:8',
            'openai_api_key'    => 'nullable|string|min:8',
            'mistral_ocr_model'    => 'nullable|string|max:100',
            'mistral_vision_model' => 'nullable|string|max:100',
            'openai_model'         => 'nullable|string|max:100',
            'feexpay_shop_id' => 'nullable|string|max:100',
            'feexpay_token'   => 'nullable|string|min:8',
            'feexpay_mode'    => 'nullable|in:SANDBOX,LIVE',
            'mail_from'         => 'nullable|email',
            'mail_from_name'    => 'nullable|string|max:100',
        ]);

        // Champs sensibles : on n'écrase que si l'admin a saisi une nouvelle valeur
        // (le champ est laissé vide au chargement pour ne jamais ré-afficher la clé en clair)
        $map = array_filter([
            'MISTRAL_API_KEY'   => $request->filled('mistral_api_key')   ? $request->mistral_api_key   : null,
            'OPENAI_API_KEY'    => $request->filled('openai_api_key')    ? $request->openai_api_key    : null,
            'MISTRAL_OCR_MODEL'    => $request->filled('mistral_ocr_model')    ? $request->mistral_ocr_model    : null,
            'MISTRAL_VISION_MODEL' => $request->filled('mistral_vision_model') ? $request->mistral_vision_model : null,
            'OPENAI_MODEL'         => $request->filled('openai_model')         ? $request->openai_model         : null,
            'FEEXPAY_SHOP_ID' => $request->filled('feexpay_shop_id') ? $request->feexpay_shop_id : null,
            'FEEXPAY_TOKEN'   => $request->filled('feexpay_token')   ? $request->feexpay_token   : null,
            'FEEXPAY_MODE'    => $request->filled('feexpay_mode')    ? $request->feexpay_mode    : null,
            'MAIL_FROM_ADDRESS' => $request->filled('mail_from')         ? $request->mail_from          : null,
            'MAIL_FROM_NAME'    => $request->filled('mail_from_name')    ? $request->mail_from_name     : null,
        ], fn ($v) => $v !== null);

        foreach ($map as $key => $value) {
            $this->setEnvValue($key, $value);
        }

        // config:clear (pas cache) : la requête en cours a déjà chargé la config en
        // mémoire AVANT ce point, donc un config:cache ici re-sauvegarderait les
        // anciennes valeurs et écraserait ce qu'on vient d'écrire. config:clear se
        // contente de supprimer le cache existant — la prochaine requête relira le
        // .env à jour normalement (comportement standard hors cache).
        \Artisan::call('config:clear');

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
