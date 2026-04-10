<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'nom', 'slug', 'ifu', 'email_contact', 'telephone',
        'adresse', 'ville', 'pays', 'devise', 'plan', 'statut',
        'quota_factures_mensuel', 'quota_users',
        'abonnement_expire_le', 'config_google_sheets', 'actif',
    ];

    protected $casts = [
        'config_google_sheets' => 'array',
        'actif'                => 'boolean',
        'abonnement_expire_le' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id', 'id');
    }

    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'tenant_id', 'id');
    }

    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class, 'tenant_id', 'id');
    }

    /**
     * Retourne l'abonnement actif en cours s'il existe.
     */
    public function abonnementActif(): ?Abonnement
    {
        return $this->abonnements()
            ->where('statut', 'actif')
            ->where('expire_le', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Le cabinet peut utiliser l'application si actif ou en trial.
     */
    public function estActif(): bool
    {
        return in_array($this->statut, ['actif', 'trial']);
    }

    /**
     * Nombre de factures traitées ce mois (exclut rejetées et erreurs).
     */
    public function facturesCeMois(): int
    {
        return $this->factures()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('statut', ['rejete', 'erreur'])
            ->count();
    }

    /**
     * Vérifie si le quota mensuel est disponible avant d'accepter un upload.
     */
    public function quotaDisponible(): bool
    {
        return $this->facturesCeMois() < $this->quota_factures_mensuel;
    }

    /**
     * Pourcentage d'utilisation du quota mensuel.
     */
    public function quotaPourcentage(): int
    {
        if ($this->quota_factures_mensuel <= 0) return 100;
        return (int) round(($this->facturesCeMois() / $this->quota_factures_mensuel) * 100);
    }
}
