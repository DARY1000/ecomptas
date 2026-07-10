<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abonnement extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'statut',
        'processeur_paiement', 'transaction_id',
        'montant_xof', 'debut_le', 'expire_le',
        'metadata_paiement',
    ];

    protected $casts = [
        'debut_le'           => 'datetime',
        'expire_le'          => 'datetime',
        'metadata_paiement'  => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif' && $this->expire_le->isFuture();
    }

    public function joursRestants(): int
    {
        return max(0, (int) now()->diffInDays($this->expire_le, false));
    }

    /**
     * Active (ou renouvelle) l'abonnement d'un tenant après un paiement FeexPay confirmé.
     * Idempotent : appelable depuis abonnement.succes ET le webhook sans créer de doublon.
     */
    public static function activerPourPaiement(Tenant $tenant, Plan $plan, string $reference, int $montant, array $metadata = []): self
    {
        $existant = static::where('transaction_id', $reference)->first();
        if ($existant) {
            return $existant;
        }

        $abonnement = static::create([
            'tenant_id'           => $tenant->id,
            'plan_id'             => $plan->id,
            'statut'              => 'actif',
            'processeur_paiement' => 'feexpay',
            'transaction_id'      => $reference,
            'montant_xof'         => $montant,
            'debut_le'            => now(),
            'expire_le'           => now()->addDays(30),
            'metadata_paiement'   => $metadata,
        ]);

        $tenant->update([
            'plan'                   => $plan->slug,
            'statut'                 => 'actif',
            'quota_factures_mensuel' => $plan->quota_factures,
            'quota_users'            => $plan->quota_users,
            'abonnement_expire_le'   => now()->addDays(30),
        ]);

        return $abonnement;
    }
}
