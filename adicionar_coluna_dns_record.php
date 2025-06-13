<?php

// Script para adicionar a coluna dns_record_id à tabela visitantes
// Executar com: php adicionar_coluna_dns_record.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

echo "Verificando se a coluna dns_record_id existe na tabela visitantes...\n";

// Verificar se a coluna já existe (para evitar tentar adicionar duas vezes)
$columns = $db->select('PRAGMA table_info(visitantes);');
$columnExists = false;

foreach ($columns as $column) {
    if ($column->name === 'dns_record_id') {
        $columnExists = true;
        break;
    }
}

if ($columnExists) {
    echo "A coluna dns_record_id já existe na tabela visitantes.\n";
} else {
    echo "Adicionando coluna dns_record_id à tabela visitantes...\n";
    
    // Desativar temporariamente chaves estrangeiras
    $db->statement('PRAGMA foreign_keys = OFF;');
    
    try {
        // Adicionar a coluna
        $db->statement('ALTER TABLE visitantes ADD COLUMN dns_record_id INTEGER NULL');
        
        // Adicionar a chave estrangeira
        $db->statement('CREATE INDEX idx_visitantes_dns_record_id ON visitantes(dns_record_id)');
        
        echo "Coluna adicionada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao adicionar a coluna: " . $e->getMessage() . "\n";
    }
    
    // Reativar chaves estrangeiras
    $db->statement('PRAGMA foreign_keys = ON;');
}

echo "Verificando estrutura atual da tabela visitantes...\n";
$columns = $db->select('PRAGMA table_info(visitantes);');
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

echo "Script concluído!\n";
