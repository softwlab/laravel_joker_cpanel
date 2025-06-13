<?php

// Arquivo para verificação de chaves estrangeiras e dados nas tabelas
// Executar com: php verificar_fk.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

// Verificar se as chaves estrangeiras estão habilitadas
$foreign_keys_status = $db->select('PRAGMA foreign_keys;');
echo "Status das chaves estrangeiras: " . ($foreign_keys_status[0]->{'foreign_keys'} ? "ATIVADAS" : "DESATIVADAS") . "\n\n";

// Verificar registros na tabela bank_templates
$bank_templates = $db->table('bank_templates')->get();
echo "Registros na tabela bank_templates:\n";
echo "--------------------------------\n";
foreach ($bank_templates as $template) {
    echo "ID: {$template->id}, Nome: {$template->name}\n";
}
echo "--------------------------------\n\n";

// Verificar o registro DNS específico que está causando o erro
$dns_record = $db->table('dns_records')->where('id', 1)->first();
if ($dns_record) {
    echo "Registro DNS com ID 1:\n";
    echo "--------------------------------\n";
    echo "ID: {$dns_record->id}\n";
    echo "bank_template_id: " . ($dns_record->bank_template_id ?? "NULL") . "\n";
    echo "user_id: " . ($dns_record->user_id ?? "NULL") . "\n";
    echo "--------------------------------\n\n";
    
    // Verificar se o banco de dados tem o template referenciado
    if ($dns_record->bank_template_id) {
        $template = $db->table('bank_templates')
            ->where('id', $dns_record->bank_template_id)
            ->first();
            
        if ($template) {
            echo "O template referenciado EXISTE no banco.\n";
        } else {
            echo "O template referenciado NÃO EXISTE no banco!\n";
        }
    }
} else {
    echo "Registro DNS com ID 1 não encontrado.\n";
}

// Verificar a estrutura da tabela dns_records
$table_info = $db->select('PRAGMA table_info(dns_records);');
echo "\nEstrutura da tabela dns_records:\n";
echo "--------------------------------\n";
foreach ($table_info as $column) {
    echo "{$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}
echo "--------------------------------\n\n";

// Verificar as chaves estrangeiras definidas na tabela dns_records
$foreign_keys = $db->select('PRAGMA foreign_key_list(dns_records);');
echo "Chaves estrangeiras na tabela dns_records:\n";
echo "--------------------------------\n";
foreach ($foreign_keys as $fk) {
    echo "Coluna: {$fk->from}, Referência: {$fk->table}.{$fk->to}, Ação: " . 
         "ON UPDATE {$fk->on_update}, ON DELETE {$fk->on_delete}\n";
}
echo "--------------------------------\n";
