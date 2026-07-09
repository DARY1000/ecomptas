<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Journaux comptables SYSCOHADA Révisé.
 * Les 7 journaux standards sont globaux (tenant_id = null).
 * Les cabinets peuvent ajouter leurs propres journaux.
 */
class Journal extends Model
{
    protected $table = 'journaux';

    protected $fillable = [
        'tenant_id', 'code', 'libelle', 'type', 'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // ── Codes standards SYSCOHADA ──────────────────────────────────────
    const HA = 'HA';   // Journal des Achats
    const VE = 'VE';   // Journal des Ventes
    const BQ = 'BQ';   // Journal de Banque
    const CA = 'CA';   // Journal de Caisse
    const OD = 'OD';   // Journal des Opérations Diverses
    const AN = 'AN';   // Journal des À-Nouveaux
    const SA = 'SA';   // Journal de Situation (Bilan)

    /**
     * Retourne les journaux disponibles pour un tenant.
     * Inclut les journaux globaux + les journaux personnalisés du cabinet.
     */
    public static function pourTenant(?string $tenantId)
    {
        return static::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })
        ->where('actif', true)
        ->orderBy('code')
        ->get();
    }

    /**
     * Les écritures de ce journal.
     */
    public function ecritures(): HasMany
    {
        return $this->hasMany(EcritureComptable::class, 'journal', 'code');
    }
}
