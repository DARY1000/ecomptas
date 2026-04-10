<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraitementLog extends Model
{
    // Pas de updated_at sur cette table (append-only)
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'facture_id', 'etape', 'statut', 'message', 'duree_ms',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id', 'id');
    }

    /**
     * Icône correspondant au statut du log (pour l'affichage Blade).
     */
    public function getIconeAttribute(): string
    {
        return match($this->statut) {
            'succes'   => '✅',
            'erreur'   => '❌',
            'en_cours' => '⏳',
            default    => '•',
        };
    }

    /**
     * Libellé de l'étape en français.
     */
    public function getEtapeLabelAttribute(): string
    {
        return match($this->etape) {
            'upload'               => 'Upload',
            'n8n_envoi'            => 'Envoi n8n.cloud',
            'ocr'                  => 'OCR Mistral',
            'classification'       => 'Classification IA',
            'extraction'           => 'Extraction données',
            'generation_ecritures' => 'Génération écritures',
            'validation'           => 'Validation comptable',
            'export'               => 'Export',
            default                => $this->etape,
        };
    }
}
