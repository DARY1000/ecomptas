<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'nom', 'slug', 'prix_mensuel_xof',
        'quota_factures', 'quota_users', 'duree_essai_jours',
        'export_xlsx', 'google_sheets', 'api_access',
        'actif', 'ordre',
    ];

    protected $casts = [
        'export_xlsx'   => 'boolean',
        'google_sheets' => 'boolean',
        'api_access'    => 'boolean',
        'actif'         => 'boolean',
    ];

    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class);
    }

    /**
     * Prix formaté en FCFA avec séparateur de milliers.
     */
    public function getPrixFormatteAttribute(): string
    {
        if ($this->prix_mensuel_xof === 0) return 'Gratuit';
        return number_format($this->prix_mensuel_xof, 0, ',', ' ') . ' FCFA/mois';
    }

    public static function actifs()
    {
        return static::where('actif', true)->orderBy('ordre')->get();
    }
}
