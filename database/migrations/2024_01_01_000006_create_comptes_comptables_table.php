<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comptes_comptables', function (Blueprint $table) {
            $table->id();
            // tenant_id = null → compte global SYSCOHADA partagé par tous les cabinets
            // tenant_id = uuid → personnalisation propre à un cabinet
            $table->string('tenant_id', 36)->nullable();
            $table->string('numero', 10);
            $table->string('libelle');
            $table->string('classe', 2);                    // 1 à 9 (classe SYSCOHADA)
            $table->enum('nature', ['actif', 'passif', 'charge', 'produit', 'bilan'])->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            // Unicité par tenant (null = global)
            $table->unique(['tenant_id', 'numero']);
            $table->index('numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comptes_comptables');
    }
};
