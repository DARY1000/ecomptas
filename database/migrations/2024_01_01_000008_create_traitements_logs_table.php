<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Log de chaque étape du pipeline de traitement IA
        // Permet le débogage et le suivi en temps réel pour l'interface
        Schema::create('traitements_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('facture_id');
            $table->enum('etape', [
                'upload',               // Fichier uploadé
                'n8n_envoi',            // Job envoyé à n8n.cloud
                'ocr',                  // OCR Mistral AI
                'classification',       // Classification GPT-4o-mini
                'extraction',           // Extraction des champs
                'generation_ecritures', // Génération écritures SYSCOHADA
                'validation',           // Validation humaine
                'export'                // Export XLSX/GSheets
            ]);
            $table->enum('statut', ['en_cours', 'succes', 'erreur']);
            $table->text('message')->nullable();
            $table->integer('duree_ms')->nullable();        // Durée de l'étape en millisecondes
            $table->timestamp('created_at')->useCurrent();  // Pas besoin de updated_at

            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
            $table->index('facture_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traitements_logs');
    }
};
