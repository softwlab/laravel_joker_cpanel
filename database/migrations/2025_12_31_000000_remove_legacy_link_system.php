<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveLegacyLinkSystem extends Migration
{
    /**
     * Execute as migrações.
     *
     * @return void
     */
    public function up()
    {
        // Verificar se ainda existem visitantes não migrados
        $nonMigratedCount = DB::table('visitantes')
            ->where('migrated_to_dns', false)
            ->where('link_id', '!=', null)
            ->count();
            
        if ($nonMigratedCount > 0) {
            throw new \Exception("Ainda existem {$nonMigratedCount} visitantes não migrados para o sistema DNS. Execute 'php artisan migrate:links-to-dns' antes de continuar.");
        }
        
        // Remover a chave estrangeira link_id na tabela visitantes
        Schema::table('visitantes', function (Blueprint $table) {
            // Remover índice se existir
            if (Schema::hasIndex('visitantes', 'visitantes_link_id_foreign')) {
                $table->dropForeign(['link_id']);
            }
            
            // Remover coluna link_id
            if (Schema::hasColumn('visitantes', 'link_id')) {
                $table->dropColumn('link_id');
            }
            
            // Tornar dns_record_id obrigatório
            if (Schema::hasColumn('visitantes', 'dns_record_id')) {
                $table->unsignedBigInteger('dns_record_id')->nullable(false)->change();
            }
            
            // Remover o campo de controle de migração
            if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                $table->dropColumn('migrated_to_dns');
            }
        });
        
        // Remover tabelas do sistema legacy
        Schema::dropIfExists('link_group_banks');
        Schema::dropIfExists('link_group_items');
        Schema::dropIfExists('link_groups');
    }

    /**
     * Reverter as migrações.
     * Nota: Esta migração não pode ser revertida completamente.
     *
     * @return void
     */
    public function down()
    {
        // Recrear tabelas
        Schema::create('link_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
        
        Schema::create('link_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_group_id');
            $table->string('nome');
            $table->string('url');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->foreign('link_group_id')->references('id')->on('link_groups');
        });
        
        Schema::create('link_group_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_group_id');
            $table->unsignedBigInteger('bank_id');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->foreign('link_group_id')->references('id')->on('link_groups');
            $table->foreign('bank_id')->references('id')->on('banks');
        });
        
        // Adicionar coluna link_id de volta (se não existir)
        Schema::table('visitantes', function (Blueprint $table) {
            if (!Schema::hasColumn('visitantes', 'link_id')) {
                $table->unsignedBigInteger('link_id')->nullable()->after('usuario_id');
                $table->foreign('link_id')->references('id')->on('link_group_items');
            }
            
            if (!Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                $table->boolean('migrated_to_dns')->default(true)->after('dns_record_id');
            }
            
            // Permitir que dns_record_id seja nullable para compatibilidade
            if (Schema::hasColumn('visitantes', 'dns_record_id')) {
                $table->unsignedBigInteger('dns_record_id')->nullable()->change();
            }
        });
        
        echo "ATENÇÃO: Esta migração não pode restaurar os dados excluídos. As tabelas foram recriadas, mas os dados foram perdidos.\n";
    }
}
