<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('ifu', 13)->nullable();           // Identifiant Fiscal Unique Bénin
            $table->string('email_contact');
            $table->string('telephone', 20)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable()->default('Cotonou');
            $table->string('pays', 3)->default('BJ');
            $table->string('devise', 3)->default('XOF');
            $table->string('plan', 20)->default('trial');   // trial|starter|pro|cabinet
            $table->enum('statut', ['actif', 'trial', 'expire', 'suspendu'])->default('trial');
            $table->integer('quota_factures_mensuel')->default(10);
            $table->integer('quota_users')->default(1);
            $table->timestamp('abonnement_expire_le')->nullable();
            // MySQL 8 : JSON natif supporté — stocke config Google Sheets {spreadsheet_id, token_oauth}
            $table->json('config_google_sheets')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
