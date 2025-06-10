<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "Iniciando remoção do sistema legado...\n";

// Remover índices primeiro
try {
    echo "Removendo índices...\n";
    DB::statement("DROP INDEX IF EXISTS visitantes_migrated_to_dns_index");
    DB::statement("DROP INDEX IF EXISTS visitantes_link_id_index");
    echo "Índices removidos com sucesso.\n";
} catch (\Exception $e) {
    echo "Erro ao remover índices: " . $e->getMessage() . "\n";
}

// Remover colunas
try {
    echo "Removendo colunas da tabela visitantes...\n";
    if (Schema::hasColumn('visitantes', 'link_id')) {
        Schema::table('visitantes', function ($table) {
            $table->dropColumn('link_id');
        });
        echo "Coluna link_id removida.\n";
    }
} catch (\Exception $e) {
    echo "Erro ao remover coluna link_id: " . $e->getMessage() . "\n";
}

try {
    if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
        Schema::table('visitantes', function ($table) {
            $table->dropColumn('migrated_to_dns');
        });
        echo "Coluna migrated_to_dns removida.\n";
    }
} catch (\Exception $e) {
    echo "Erro ao remover coluna migrated_to_dns: " . $e->getMessage() . "\n";
}

// Remover tabelas
$tables = [
    'link_group_items',
    'link_group_banks',
    'link_groups'
];

foreach ($tables as $table) {
    try {
        if (Schema::hasTable($table)) {
            Schema::dropIfExists($table);
            echo "Tabela {$table} removida com sucesso.\n";
        } else {
            echo "Tabela {$table} já não existe.\n";
        }
    } catch (\Exception $e) {
        echo "Erro ao remover tabela {$table}: " . $e->getMessage() . "\n";
    }
}

echo "Processo de remoção concluído!\n";
