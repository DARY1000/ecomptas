<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->foreignId('plan_id')->constrained();
            $table->enum('statut', ['en_attente', 'actif', 'expire', 'annule'])->default('en_attente');
            $table->string('processeur_paiement', 20);      // feexpay|cinetpay
            $table->string('transaction_id')->nullable();
            $table->integer('montant_xof');
            $table->timestamp('debut_le');
            $table->timestamp('expire_le');
            // MySQL 8 JSON natif — métadonnées brutes FeexPay
            $table->json('metadata_paiement')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};
