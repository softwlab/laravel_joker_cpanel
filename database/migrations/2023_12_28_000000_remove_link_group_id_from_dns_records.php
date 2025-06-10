<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificamos se a coluna existe antes de tentar removê-la
        if (Schema::hasColumn('dns_records', 'link_group_id')) {
            // Precisamos remover qualquer constraint de chave estrangeira primeiro
            // Como isso pode variar entre diferentes sistemas de banco de dados,
            // usaremos uma abordagem em duas etapas

            // 1. Primeiro removemos qualquer chave estrangeira na coluna link_group_id
            Schema::table('dns_records', function (Blueprint $table) {
                // Tentar todas as possíveis convenções de nomenclatura de chaves estrangeiras
                try {
                    $table->dropForeign(['link_group_id']);
                } catch (\Exception $e) {
                    // Se falhar, podemos tentar outro formato de nome
                    try {
                        $table->dropForeign('dns_records_link_group_id_foreign');
                    } catch (\Exception $e) {
                        // Ignorar erro se a chave não existir
                    }
                }
            });

            // 2. Agora removemos a coluna com segurança
            Schema::table('dns_records', function (Blueprint $table) {
                $table->dropColumn('link_group_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Como estamos descontinuando o sistema legado, não seria realmente
        // necessário adicionar a coluna de volta, mas mantemos por consistência
        if (!Schema::hasColumn('dns_records', 'link_group_id')) {
            Schema::table('dns_records', function (Blueprint $table) {
                $table->unsignedBigInteger('link_group_id')->nullable();
            });
        }
    }
};
