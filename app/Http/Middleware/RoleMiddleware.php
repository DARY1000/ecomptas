<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'utilisateur possède l'un des rôles autorisés.
 *
 * Usage dans les routes :
 *   ->middleware('role:admin,super_admin')
 *   ->middleware('role:comptable,admin,super_admin')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès refusé — rôle insuffisant.'], 403);
            }

            abort(403, 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
