<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrepareLinkGroupRemoval extends Migration
{
    /**
     * Execute as migrações.
     *
     * @return void
     */
    public function up()
    {
        // Esta migração marca o campo link_id como nullable
        // para permitir a transição para dns_record_id
        Schema::table('visitantes', function (Blueprint $table) {
            // Tornar o campo link_id opcional para permitir sua eventual remoção
            $table->unsignedBigInteger('link_id')->nullable()->change();
            
            // Garantir que dns_record_id esteja como nullable mas eventualmente será obrigatório
            if (Schema::hasColumn('visitantes', 'dns_record_id')) {
                $table->unsignedBigInteger('dns_record_id')->nullable()->change();
            } else {
                $table->unsignedBigInteger('dns_record_id')->nullable()->after('link_id');
            }
            
            // Garantir que o campo de migração exista
            if (!Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                $table->boolean('migrated_to_dns')->default(false)->after('dns_record_id');
            }
        });
        
        // Adicionar índices para melhorar performance durante a migração
        Schema::table('visitantes', function (Blueprint $table) {
            if (!Schema::hasIndex('visitantes', 'visitantes_dns_record_id_index')) {
                $table->index('dns_record_id', 'visitantes_dns_record_id_index');
            }
            
            if (!Schema::hasIndex('visitantes', 'visitantes_migrated_to_dns_index')) {
                $table->index('migrated_to_dns', 'visitantes_migrated_to_dns_index');
            }
        });
    }

    /**
     * Reverter as migrações.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitantes', function (Blueprint $table) {
            // Tornar link_id obrigatório novamente
            $table->unsignedBigInteger('link_id')->nullable(false)->change();
            
            // Remover índices
            $table->dropIndex('visitantes_dns_record_id_index');
            $table->dropIndex('visitantes_migrated_to_dns_index');
        });
    }
}
