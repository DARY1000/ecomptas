<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie le quota mensuel de factures avant d'autoriser un upload.
 * Appliqué uniquement sur la route POST /factures.
 */
class CheckQuota
{
    public function handle(Request $request, Closure $next): Response
    {
        $user   = $request->user();
        $tenant = $user?->tenant;

        // Super admin sans tenant : pas de quota
        if (!$tenant) {
            return $next($request);
        }

        if (!$tenant->quotaDisponible()) {
            $utilisees = $tenant->facturesCeMois();
            $quota     = $tenant->quota_factures_mensuel;

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => "Quota mensuel atteint ({$utilisees}/{$quota} factures).",
                ], 402);
            }

            return back()->withErrors([
                'quota' => "Quota mensuel atteint ({$utilisees}/{$quota} factures). Passez à un plan supérieur pour continuer.",
            ]);
        }

        return $next($request);
    }
}
