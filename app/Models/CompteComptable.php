<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteComptable extends Model
{
    protected $table = 'comptes_comptables';

    protected $fillable = [
        'tenant_id', 'numero', 'libelle', 'classe', 'nature', 'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Retourne les comptes disponibles pour un tenant :
     * les comptes globaux (tenant_id = null) + les comptes personnalisés du cabinet.
     */
    public static function pourTenant(?string $tenantId)
    {
        return static::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })
        ->where('actif', true)
        ->orderBy('numero')
        ->get();
    }

    /**
     * Recherche un compte par numéro pour un tenant donné.
     * Priorité : compte personnalisé du cabinet > compte global SYSCOHADA.
     */
    public static function trouver(string $numero, ?string $tenantId = null): ?self
    {
        // D'abord chercher un compte personnalisé du cabinet
        if ($tenantId) {
            $compte = static::where('tenant_id', $tenantId)
                           ->where('numero', $numero)
                           ->first();
            if ($compte) return $compte;
        }

        // Sinon fallback sur le compte global SYSCOHADA
        return static::whereNull('tenant_id')
                     ->where('numero', $numero)
                     ->first();
    }
}
