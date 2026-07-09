<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des journaux comptables SYSCOHADA Révisé.
 * Les 7 journaux standards + possibilité de personnalisation par cabinet.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('journaux', function (Blueprint $table) {
            $table->id();
            // tenant_id = null → journal global partagé (les 7 standards SYSCOHADA)
            // tenant_id = uuid → journal personnalisé d'un cabinet
            $table->string('tenant_id', 36)->nullable();
            $table->string('code', 5);           // HA, VE, BQ, CA, OD, AN, SA
            $table->string('libelle');            // ex: "Journal des Achats"
            $table->string('type', 20);           // achat, vente, banque, caisse, operations_diverses, a_nouveaux, situation
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journaux');
    }
};
