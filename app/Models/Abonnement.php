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
}
