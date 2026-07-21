<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeleExport extends Model
{
    protected $table = 'modeles_export';

    protected $fillable = [
        'tenant_id', 'nom_original', 'chemin_fichier', 'extension',
        'structure', 'notes_style', 'analyse_le',
    ];

    protected $casts = [
        'structure'  => 'array',
        'analyse_le' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
