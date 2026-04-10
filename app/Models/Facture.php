<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facture extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'uploaded_by', 'valide_par',
        'type_document', 'sous_type', 'score_confiance_classification',
        'statut', 'pdf_path', 'pdf_nom_original', 'pdf_taille_bytes', 'nombre_pages',
        'ocr_texte', 'donnees_extraites', 'donnees_corrigees',
        'numero_facture', 'fournisseur_client', 'ifu_tiers', 'code_mecef',
        'date_facture', 'montant_ht', 'montant_tva', 'montant_ttc',
        'montant_aib', 'montant_rirf', 'regime_fiscal', 'mode_paiement',
        'justification_ia', 'n8n_execution_id', 'n8n_started_at', 'n8n_finished_at',
        'valide_le', 'motif_rejet',
    ];

    protected $casts = [
        // MySQL longText castés en array via Eloquent
        'donnees_extraites' => 'array',
        'donnees_corrigees' => 'array',
        'date_facture'      => 'date',
        'montant_ht'        => 'decimal:2',
        'montant_tva'       => 'decimal:2',
        'montant_ttc'       => 'decimal:2',
        'montant_aib'       => 'decimal:2',
        'montant_rirf'      => 'decimal:2',
        'n8n_started_at'    => 'datetime',
        'n8n_finished_at'   => 'datetime',
        'valide_le'         => 'datetime',
    ];

    // ─────────────────────────────────────────────
    // Scopes — TOUJOURS utiliser pour l'isolation tenant
    // Ne jamais faire Facture::all() sans scope tenant
    // ─────────────────────────────────────────────

    public function scopePourTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // ─────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function uploadePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function ecritures(): HasMany
    {
        return $this->hasMany(EcritureComptable::class, 'facture_id', 'id')
                    ->orderBy('ordre_ligne');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TraitementLog::class, 'facture_id', 'id')
                    ->orderBy('created_at');
    }

    // ─────────────────────────────────────────────
    // Business logic
    // ─────────────────────────────────────────────

    /**
     * RÈGLE 4 SYSCOHADA : les écritures doivent être équilibrées (Σdébit = Σcrédit).
     * Tolérance de 0,01 FCFA pour les arrondis.
     */
    public function estEquilibree(): bool
    {
        $debit  = $this->ecritures()->sum('debit');
        $credit = $this->ecritures()->sum('credit');
        return abs($debit - $credit) < 0.01;
    }

    // ─────────────────────────────────────────────
    // Accesseurs
    // ─────────────────────────────────────────────

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'uploade'             => 'Uploadé',
            'traitement_en_cours' => 'Traitement IA...',
            'a_valider'           => 'À valider',
            'valide'              => 'Validé',
            'rejete'              => 'Rejeté',
            'erreur'              => 'Erreur',
            default               => $this->statut,
        };
    }

    /**
     * Couleur CSS Tailwind associée au statut (pour les badges dans les vues).
     */
    public function getStatutCouleurAttribute(): string
    {
        return match($this->statut) {
            'uploade'             => 'gray',
            'traitement_en_cours' => 'blue',
            'a_valider'           => 'yellow',
            'valide'              => 'green',
            'rejete'              => 'red',
            'erreur'              => 'red',
            default               => 'gray',
        };
    }

    /**
     * Retourne le montant TTC formaté en FCFA.
     */
    public function getMontantFormatteAttribute(): string
    {
        if (!$this->montant_ttc) return '—';
        return number_format((float) $this->montant_ttc, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Vérifie si la facture est en cours de traitement côté n8n.
     */
    public function enTraitement(): bool
    {
        return $this->statut === 'traitement_en_cours';
    }
}
