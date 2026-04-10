<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'abonnement du cabinet est actif ou en trial.
 * Redirige vers la page d'abonnement si expiré ou suspendu.
 * Le super_admin est exempté de cette vérification.
 */
class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admin : accès illimité à tout
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $user?->tenant;

        if (!$tenant || !$tenant->estActif()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'    => 'Abonnement expiré ou inactif.',
                    'redirect' => route('abonnement.index'),
                ], 402);
            }

            return redirect()
                ->route('abonnement.index')
                ->withErrors(['abonnement' => 'Votre abonnement est expiré. Renouvelez pour continuer à utiliser eCompta360.']);
        }

        return $next($request);
    }
}
