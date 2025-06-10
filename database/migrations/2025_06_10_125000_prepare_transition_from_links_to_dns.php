<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrepareTransitionFromLinksToDns extends Migration
{
    /**
     * Execute as migrações.
     */
    public function up(): void
    {
        // Tornando o campo dns_record_id obrigatório para futuros registros
        // mas mantendo como nullable para registros existentes
        if (!Schema::hasColumn('visitantes', 'dns_record_id')) {
            Schema::table('visitantes', function (Blueprint $table) {
                $table->foreignId('dns_record_id')->nullable()->after('link_id')
                      ->constrained('dns_records')->onDelete('cascade');
            });
        }
        
        // Adicionando flag para indicar se o registro já foi migrado
        Schema::table('visitantes', function (Blueprint $table) {
            $table->boolean('migrated_to_dns')->default(false)->after('dns_record_id');
        });
    }

    /**
     * Reverter as migrações.
     */
    public function down(): void
    {
        Schema::table('visitantes', function (Blueprint $table) {
            $table->dropColumn('migrated_to_dns');
        });
    }
}
