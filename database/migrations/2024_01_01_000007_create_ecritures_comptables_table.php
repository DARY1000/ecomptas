<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecritures_comptables', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->uuid('facture_id');                     // Référence vers factures.id (uuid)
            $table->string('journal', 5)->default('OD');   // ACH=Achat, VTE=Vente, OD=Opérations Diverses
            $table->date('date_ecriture');
            $table->string('numero_piece')->nullable();     // Numéro de facture
            $table->string('numero_compte', 10);            // Compte SYSCOHADA (ex: 6011)
            $table->string('libelle_compte')->nullable();   // Libellé du compte (ex: Achats marchandises)
            $table->string('libelle_ecriture');             // Libellé de la ligne d'écriture
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('devise', 3)->default('XOF');
            $table->boolean('est_ecriture_reglement')->default(false); // Vrai pour la ligne de trésorerie
            $table->integer('ordre_ligne')->default(0);     // Pour l'affichage ordonné
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
            $table->index(['tenant_id', 'date_ecriture']);
            $table->index(['tenant_id', 'numero_compte']);
            $table->index('facture_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecritures_comptables');
    }
};
