<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

$usuario = new Usuario();
$usuario->nome = 'Cliente Teste';
$usuario->email = 'cliente1';
$usuario->senha = Hash::make('123456');
$usuario->nivel = 'cliente';
$usuario->ativo = true;
$usuario->save();

echo "Usuário cliente criado com sucesso!\n";
echo "Usuário: cliente1\n";
echo "Senha: 123456\n";
