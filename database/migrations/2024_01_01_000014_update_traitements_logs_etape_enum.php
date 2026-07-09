<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Mise à jour de l'enum etape dans traitements_logs.
 * - Remplace 'n8n_envoi' par 'traitement_ia' (pipeline direct Mistral/OpenAI sans n8n)
 * - Conserve 'n8n_envoi' pour la rétrocompatibilité des logs existants
 */
return new class extends Migration {
    public function up(): void
    {
        // MySQL — modifier un ENUM nécessite de réécrire toute la définition
        DB::statement("ALTER TABLE traitements_logs MODIFY COLUMN etape ENUM(
            'upload',
            'n8n_envoi',
            'traitement_ia',
            'ocr',
            'classification',
            'extraction',
            'generation_ecritures',
            'validation',
            'export'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE traitements_logs MODIFY COLUMN etape ENUM(
            'upload',
            'n8n_envoi',
            'ocr',
            'classification',
            'extraction',
            'generation_ecritures',
            'validation',
            'export'
        ) NOT NULL");
    }
};
