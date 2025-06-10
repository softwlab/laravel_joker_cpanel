<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Verificando e criando usuário com ID 3...\n";

try {
    // Verificar se o usuário já existe
    $usuario = DB::table('usuarios')->where('id', 3)->first();
    
    if ($usuario) {
        echo "Usuário ID 3 já existe: {$usuario->nome}\n";
    } else {
        // Criar o usuário
        DB::table('usuarios')->insert([
            'id' => 3,
            'nome' => 'Cliente Adicional',
            'email' => 'cliente3@example.com',
            'senha' => Hash::make('senha123'),
            'nivel' => 'cliente',
            'ativo' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "Usuário ID 3 foi criado com sucesso!\n";
        
        // Verificar se foi criado corretamente
        $usuario = DB::table('usuarios')->where('id', 3)->first();
        if ($usuario) {
            echo "Confirmado: Usuário ID 3 existe no banco de dados.\n";
        }
    }
    
    // Listar todos os usuários para referência
    echo "\nLista de todos os usuários no sistema:\n";
    $usuarios = DB::table('usuarios')->get();
    
    foreach ($usuarios as $user) {
        echo "ID: {$user->id} | Nome: {$user->nome} | Email: {$user->email} | Nível: {$user->nivel}\n";
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
