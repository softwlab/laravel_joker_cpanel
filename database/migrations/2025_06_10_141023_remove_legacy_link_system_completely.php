<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Remove completamente o sistema legado de links bancários.
     * Esta migração remove todas as tabelas e colunas relacionadas ao sistema legado.
     */
    public function up(): void
    {
        Log::info('Iniciando remoção completa do sistema legado de links bancários.');
        
        // Remover chaves estrangeiras relacionadas a link_id na tabela visitantes
        try {
            // Para MySQL/MariaDB
            if (DB::connection()->getDriverName() === 'mysql') {
                $foreignKeys = DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                     WHERE TABLE_NAME = 'visitantes' 
                     AND COLUMN_NAME = 'link_id' 
                     AND CONSTRAINT_NAME != 'PRIMARY' 
                     AND REFERENCED_TABLE_NAME IS NOT NULL"
                );
                
                if (!empty($foreignKeys)) {
                    Schema::table('visitantes', function (Blueprint $table) use ($foreignKeys) {
                        foreach ($foreignKeys as $fk) {
                            $table->dropForeign($fk->CONSTRAINT_NAME);
                        }
                    });
                    Log::info('Removidas chaves estrangeiras da coluna link_id.');
                }
            }
            // Para SQLite não precisa remover FK explicitamente
        } catch (\Exception $e) {
            Log::warning('Erro ao remover chaves estrangeiras: ' . $e->getMessage());
        }
        
        // Remover índices relacionados ao sistema legado da tabela visitantes
        try {
            // Para MySQL/MariaDB
            if (DB::connection()->getDriverName() === 'mysql') {
                $indexes = DB::select("SHOW INDEX FROM visitantes WHERE Column_name IN ('link_id', 'migrated_to_dns')");
                
                if (!empty($indexes)) {
                    Schema::table('visitantes', function (Blueprint $table) use ($indexes) {
                        $processedIndexes = [];
                        
                        foreach ($indexes as $index) {
                            if (!in_array($index->Key_name, $processedIndexes) && $index->Key_name !== 'PRIMARY') {
                                $table->dropIndex($index->Key_name);
                                $processedIndexes[] = $index->Key_name;
                                Log::info("Removido índice {$index->Key_name} da tabela visitantes");
                            }
                        }
                    });
                }
            } else if (DB::connection()->getDriverName() === 'sqlite') {
                // Para SQLite é mais complicado remover índices, vamos tentar uma abordagem diferente
                $indexes = DB::select("PRAGMA index_list('visitantes')");
                
                foreach ($indexes as $index) {
                    $indexInfo = DB::select("PRAGMA index_info('{$index->name}')");
                    $isTargetIndex = false;
                    
                    foreach ($indexInfo as $column) {
                        if (in_array($column->name, ['link_id', 'migrated_to_dns'])) {
                            $isTargetIndex = true;
                            break;
                        }
                    }
                    
                    if ($isTargetIndex) {
                        DB::statement("DROP INDEX IF EXISTS {$index->name}");
                        Log::info("Removido índice {$index->name} da tabela visitantes");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao remover índices: ' . $e->getMessage());
        }
        
        // Remover colunas relacionadas ao sistema legado da tabela visitantes
        // Remover dentro de um try-catch isolado para cada coluna
        try {
            if (Schema::hasColumn('visitantes', 'link_id')) {
                Schema::table('visitantes', function (Blueprint $table) {
                    $table->dropColumn('link_id');
                });
                Log::info('Coluna link_id removida da tabela visitantes.');
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao remover coluna link_id: ' . $e->getMessage());
        }
        
        try {
            if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                Schema::table('visitantes', function (Blueprint $table) {
                    $table->dropColumn('migrated_to_dns');
                });
                Log::info('Coluna migrated_to_dns removida da tabela visitantes.');
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao remover coluna migrated_to_dns: ' . $e->getMessage());
        }
        
        // Remover tabelas legadas na ordem correta (respeitando dependências)
        $tables = [
            'link_group_items',
            'link_group_banks',
            'link_groups'
        ];
        
        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    Schema::dropIfExists($table);
                    Log::info("Tabela {$table} removida com sucesso.");
                } else {
                    Log::info("Tabela {$table} já havia sido removida anteriormente.");
                }
            } catch (\Exception $e) {
                Log::error("Erro ao remover tabela {$table}: {$e->getMessage()}");
            }
        }
        
        // Atualizar conta do sistema para remover referências ao sistema legado
        try {
            DB::table('system_configs')->where('key', 'has_legacy_links')->delete();
            Log::info('Configurações do sistema atualizadas.');
        } catch (\Exception $e) {
            Log::warning('Erro ao atualizar configurações do sistema: ' . $e->getMessage());
        }
        
        Log::info('Sistema legado de links bancários removido completamente com sucesso!');
    }

    /**
     * Reverse the migrations.
     * 
     * Esta operação recria apenas a estrutura das tabelas sem restaurar os dados.
     */
    public function down(): void
    {
        Log::warning('Tentativa de reverter a remoção do sistema legado. Apenas a estrutura será recriada sem os dados.');
        
        // Recriar tabelas na ordem correta
        Schema::create('link_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('link_group_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_group_id');
            $table->unsignedBigInteger('bank_id');
            $table->timestamps();
            
            $table->foreign('link_group_id')->references('id')->on('link_groups');
            $table->foreign('bank_id')->references('id')->on('banks');
        });
        
        Schema::create('link_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('name');
            $table->string('url');
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('link_groups');
        });
        
        // Adicionar colunas na tabela visitantes
        Schema::table('visitantes', function (Blueprint $table) {
            $table->unsignedBigInteger('link_id')->nullable()->after('id');
            $table->boolean('migrated_to_dns')->default(false)->after('link_id');
        });
        
        Log::info('Estrutura do sistema legado recriada com sucesso, mas sem dados.');
    }
};
