<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'regime_fiscal')) {
                $table->enum('regime_fiscal', ['B', 'D'])->default('B')->after('plan');
            }
            if (!Schema::hasColumn('tenants', 'rccm')) {
                $table->string('rccm', 50)->nullable()->after('ifu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['regime_fiscal', 'rccm']);
        });
    }
};
