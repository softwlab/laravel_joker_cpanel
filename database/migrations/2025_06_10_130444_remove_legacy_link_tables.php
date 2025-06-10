<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Remove as tabelas do sistema legado de links bancários.
     * 
     * ATENÇÃO: Esta migração remove completamente o sistema legado de links bancários.
     * Certifique-se que todos os dados relevantes foram migrados para o novo sistema DNS.
     */
    public function up(): void
    {
        
        // Verificar se existem visitantes não migrados
        $unmigrated = DB::table('visitantes')
            ->whereNotNull('link_id')
            ->whereNull('dns_record_id')
            ->where('migrated_to_dns', false)
            ->count();
            
        if ($unmigrated > 0) {
            Log::warning("Ainda existem {$unmigrated} visitantes não migrados. Execute o comando 'php artisan migrate:links-to-dns --force' antes de prosseguir.");
            throw new \Exception("Ainda existem {$unmigrated} visitantes não migrados para o sistema DNS. Migração abortada.");
        }
        
        // Criar backup das tabelas antes de removê-las
        $date = Carbon::now()->format('Y_m_d');
        
        // Tabelas a serem removidas
        $tables = [
            'link_group_items',
            'link_group_banks',
            'link_groups',
        ];
        
        // Criar tabelas de backup com timestamp
        foreach ($tables as $table) {
            $backupTable = "backup_{$table}_{$date}";
            
            try {
                // Verificar se a tabela de backup já existe
                $tableExists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name=?", [$backupTable]);
                
                if (empty($tableExists)) {
                    // Criar backup da tabela
                    DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM {$table}");
                    Log::info("Criado backup da tabela {$table} em {$backupTable}");
                } else {
                    Log::info("Tabela de backup {$backupTable} já existe, pulando criação");
                }
            } catch (\Exception $e) {
                Log::warning("Erro ao criar backup da tabela {$table}: {$e->getMessage()}");
            }
        }
        
        // Remover tabelas legadas
        Schema::dropIfExists('link_group_items');
        Schema::dropIfExists('link_group_banks');
        Schema::dropIfExists('link_groups');
        
        // Verificar e remover chaves estrangeiras antes de remover campos
        try {
            // Obter todas as chaves estrangeiras da tabela
            $foreignKeys = DB::select("PRAGMA foreign_key_list('visitantes')");
            
            // Remover as chaves estrangeiras relacionadas a link_id
            Schema::table('visitantes', function (Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey->from === 'link_id') {
                        $table->dropForeign([$foreignKey->from]);
                        Log::info("Removida chave estrangeira da coluna {$foreignKey->from}");
                    }
                }
            });
        } catch (\Exception $e) {
            Log::warning("Erro ao remover chaves estrangeiras: {$e->getMessage()}");
            // Continuar mesmo se houver erro, pois SQLite pode não fornecer essa informação
        }
        
        // Remover campos legados da tabela visitantes
        Schema::table('visitantes', function (Blueprint $table) {
            if (Schema::hasColumn('visitantes', 'link_id')) {
                $table->dropColumn('link_id');
            }
            if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                $table->dropColumn('migrated_to_dns');
            }
        });
        
        Log::info('Tabelas do sistema legado de links bancários foram removidas com sucesso.');
    }

    /**
     * Reverse the migrations.
     * 
     * Esta operação não pode ser revertida automaticamente, pois os dados foram removidos.
     * Em caso de necessidade, restaure os backups criados durante o processo de up().
     */
    public function down(): void
    {
        Log::warning('Tentativa de reverter a remoção das tabelas legadas. Esta operação não pode ser revertida automaticamente.');
        
        // Recriar estrutura das tabelas
        Schema::create('link_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
        
        Schema::create('link_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_group_id');
            $table->string('name');
            $table->string('url');
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('link_group_id')->references('id')->on('link_groups');
        });
        
        Schema::create('link_group_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_group_id');
            $table->unsignedBigInteger('bank_id');
            $table->timestamps();
            
            $table->foreign('link_group_id')->references('id')->on('link_groups');
            $table->foreign('bank_id')->references('id')->on('banks');
        });
        
        // Adicionar campos novamente à tabela visitantes
        Schema::table('visitantes', function (Blueprint $table) {
            $table->unsignedBigInteger('link_id')->nullable()->after('id');
            $table->boolean('migrated_to_dns')->default(false)->after('link_id');
        });
        
        Log::info('Estrutura das tabelas legadas foi recriada, mas sem os dados. Os dados devem ser restaurados manualmente.');
    }
};
