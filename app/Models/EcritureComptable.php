<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcritureComptable extends Model
{
    protected $fillable = [
        'tenant_id', 'facture_id', 'journal',
        'date_ecriture', 'numero_piece',
        'numero_compte', 'libelle_compte', 'libelle_ecriture',
        'debit', 'credit', 'devise',
        'est_ecriture_reglement', 'ordre_ligne',
    ];

    protected $casts = [
        'date_ecriture'          => 'date',
        'debit'                  => 'decimal:2',
        'credit'                 => 'decimal:2',
        'est_ecriture_reglement' => 'boolean',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id', 'id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Libellé formaté pour l'export XLSX.
     */
    public function getLigneExportAttribute(): array
    {
        return [
            $this->journal,
            $this->date_ecriture?->format('d/m/Y'),
            $this->numero_piece,
            $this->numero_compte,
            $this->libelle_ecriture,
            $this->debit > 0 ? number_format((float) $this->debit, 2, ',', ' ') : '',
            $this->credit > 0 ? number_format((float) $this->credit, 2, ',', ' ') : '',
            $this->devise,
        ];
    }
}
