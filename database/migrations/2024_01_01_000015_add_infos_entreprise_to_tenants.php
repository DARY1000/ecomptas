<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brique 1 du module fiscal — Configuration de l'entreprise.
 * Ces informations sont reprises automatiquement sur les documents générés
 * (factures, exports, futures déclarations).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'sigle')) {
                $table->string('sigle', 30)->nullable()->after('nom');
            }
            if (!Schema::hasColumn('tenants', 'site_web')) {
                $table->string('site_web')->nullable()->after('email_contact');
            }
            if (!Schema::hasColumn('tenants', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('site_web');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['sigle', 'site_web', 'logo_path']);
        });
    }
};
