<?php

require 'vendor/autoload.php';

// Iniciar aplicação Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar DB para modificação direta do esquema
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Verificando tabela link_groups...\n";

try {
    // Verificar se a coluna já existe
    $hasColumn = Schema::hasColumn('link_groups', 'usuario_id');
    
    if (!$hasColumn) {
        echo "Adicionando coluna usuario_id à tabela link_groups...\n";
        
        Schema::table('link_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->nullable()->after('id');
        });
        
        echo "Coluna adicionada com sucesso!\n";
    } else {
        echo "A coluna usuario_id já existe na tabela link_groups.\n";
    }
    
    echo "\nProcesso concluído com sucesso!\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
