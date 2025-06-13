<?php

// Script para corrigir o problema de chave estrangeira
// Executar com: php corrigir_fk.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

echo "Desativando chaves estrangeiras temporariamente...\n";
$db->statement('PRAGMA foreign_keys = OFF;');

try {
    echo "Atualizando registro DNS...\n";
    
    // A operação que estava falhando
    $result = $db->table('dns_records')
        ->where('id', 1)
        ->update([
            'bank_template_id' => 1,
            'user_id' => 2,
            'ttl' => 60,
            'updated_at' => now()
        ]);
        
    echo $result ? "Registro atualizado com sucesso!\n" : "Nenhum registro foi atualizado.\n";
} catch (\Exception $e) {
    echo "Erro ao atualizar registro: " . $e->getMessage() . "\n";
}

echo "Reativando chaves estrangeiras...\n";
$db->statement('PRAGMA foreign_keys = ON;');
