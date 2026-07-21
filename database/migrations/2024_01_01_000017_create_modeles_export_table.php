<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modeles_export', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->string('nom_original');
            $table->string('chemin_fichier');
            $table->string('extension', 10);
            $table->json('structure')->nullable();   // en-têtes détectés + correspondance avec nos champs d'écriture
            $table->text('notes_style')->nullable();  // conventions du cabinet déduites de l'échantillon (libellés, comptes)
            $table->timestamp('analyse_le')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modeles_export');
    }
};
