<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_id', 36);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('valide_par')->nullable()->constrained('users');

            // Classification IA
            $table->enum('type_document', ['VENTE', 'ACHAT', 'CHARGE'])->nullable();
            $table->string('sous_type', 30)->nullable();     // Marchandise, Service, Immobilisation, etc.
            $table->decimal('score_confiance_classification', 5, 2)->nullable();

            // Statut pipeline simplifié pour mutualisé
            $table->enum('statut', [
                'uploade',            // Vient d'être uploadé
                'traitement_en_cours',// Job dispatché, n8n en train de traiter
                'a_valider',          // n8n a retourné les données, en attente validation humaine
                'valide',             // Validé par le comptable
                'rejete',             // Rejeté avec motif
                'erreur'              // Erreur pipeline
            ])->default('uploade');

            // Fichier — stockage local Hostinger
            // Chemin relatif dans storage/app/private/tenants/{id}/pdfs/
            $table->string('pdf_path');
            $table->string('pdf_nom_original');
            $table->bigInteger('pdf_taille_bytes')->unsigned()->nullable();
            $table->tinyInteger('nombre_pages')->unsigned()->nullable();

            // Données IA — longText car MySQL TEXT supporte mieux les JSON volumineux
            // Castés en array via Eloquent $casts
            $table->longText('ocr_texte')->nullable();
            $table->longText('donnees_extraites')->nullable();
            $table->longText('donnees_corrigees')->nullable();

            // Champs dénormalisés pour tri et recherche rapide sans déserialiser le JSON
            $table->string('numero_facture')->nullable();
            $table->string('fournisseur_client')->nullable();
            $table->string('ifu_tiers', 13)->nullable();
            $table->string('code_mecef', 50)->nullable();    // Code MECEF Bénin
            $table->date('date_facture')->nullable();
            $table->decimal('montant_ht', 15, 2)->nullable();
            $table->decimal('montant_tva', 15, 2)->nullable();
            $table->decimal('montant_ttc', 15, 2)->nullable();
            $table->decimal('montant_aib', 15, 2)->nullable(); // AIB 1% — spécifique Bénin
            $table->decimal('montant_rirf', 15, 2)->nullable(); // RIRF — retenue à la source
            $table->string('regime_fiscal', 5)->nullable();   // B (TVA 18%) ou D (exonéré)
            $table->string('mode_paiement', 20)->nullable();  // especes|virement|mobile_money|cheque
            $table->text('justification_ia')->nullable();

            // Tracking n8n.cloud
            $table->string('n8n_execution_id')->nullable();
            $table->timestamp('n8n_started_at')->nullable();
            $table->timestamp('n8n_finished_at')->nullable();

            $table->timestamp('valide_le')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'statut']);
            $table->index(['tenant_id', 'type_document']);
            $table->index(['tenant_id', 'date_facture']);
            $table->index(['tenant_id', 'fournisseur_client']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
