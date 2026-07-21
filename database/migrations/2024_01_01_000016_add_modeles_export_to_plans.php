<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('modeles_export')->default(false)->after('api_access');
        });

        // Le déploiement ne relance pas les seeders : on active la fonctionnalité
        // directement ici pour les plans déjà en base (Pro et Cabinet+).
        DB::table('plans')->whereIn('slug', ['pro', 'cabinet'])->update(['modeles_export' => true]);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('modeles_export');
        });
    }
};
