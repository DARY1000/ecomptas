<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                          // Trial, Starter, Pro, Cabinet+
            $table->string('slug')->unique();               // trial, starter, pro, cabinet
            $table->integer('prix_mensuel_xof');            // Prix en FCFA
            $table->integer('quota_factures');              // Nombre de factures/mois
            $table->integer('quota_users');                 // Nombre d'utilisateurs max
            $table->boolean('export_xlsx')->default(true);
            $table->boolean('google_sheets')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);           // Ordre d'affichage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
