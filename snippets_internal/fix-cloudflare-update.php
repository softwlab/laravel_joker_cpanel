<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correção do método updateDnsRecord no CloudflareService...');

// Abrir o arquivo CloudflareService.php
$cloudflareServicePath = __DIR__ . '/app/Services/CloudflareService.php';
$content = file_get_contents($cloudflareServicePath);

// Fazer backup do arquivo original
$backupPath = $cloudflareServicePath . '.backup.' . time() . '.php';
file_put_contents($backupPath, $content);
output('Backup do arquivo original criado em: ' . $backupPath);

// Localizar o método updateDnsRecord e substituir seu cabeçalho
$originalMethodHeader = 'public function updateDnsRecord($zoneId, $recordId, $recordData)';
$newMethodHeader = 'public function updateDnsRecord(\\App\\Models\\DnsRecord $record)';

$content = str_replace($originalMethodHeader, $newMethodHeader, $content);

// Substituir o corpo do método
$originalMethodBody = '{
    try {
        $response = Http::withHeaders($this->headers)
            ->put($this->baseUrl . \'/zones/\' . $zoneId . \'/dns_records/\' . $recordId, $recordData);
        
        $data = $response->json();';

$newMethodBody = '{
    try {
        // Extrair dados do registro
        $extraData = is_array($record->extra_data) ? $record->extra_data : [];
        $zoneId = $extraData[\'zone_id\'] ?? null;
        $recordId = $extraData[\'record_id\'] ?? null;
        
        // Verificar se temos os IDs necessários
        if (empty($zoneId)) {
            throw new \\Exception(\'Zone ID não encontrado nos dados extras do registro\');
        }
        
        if (empty($recordId)) {
            throw new \\Exception(\'Record ID não encontrado nos dados extras do registro\');
        }
        
        // Preparar os dados para atualização
        $recordData = [
            \'type\' => $record->record_type,
            \'name\' => $record->name,
            \'content\' => $record->content,
            \'ttl\' => (int) $record->ttl,
        ];
        
        // Adicionar propriedade priority para registros MX e SRV
        if (in_array($record->record_type, [\'MX\', \'SRV\']) && !empty($record->priority)) {
            $recordData[\'priority\'] = (int) $record->priority;
        }
        
        // Adicionar propriedade proxied para registros compatíveis
        if (isset($extraData[\'proxied\'])) {
            $recordData[\'proxied\'] = (bool) $extraData[\'proxied\'];
        }
        
        $response = Http::withHeaders($this->headers)
            ->put($this->baseUrl . \'/zones/\' . $zoneId . \'/dns_records/\' . $recordId, $recordData);
        
        $data = $response->json();';

$content = str_replace($originalMethodBody, $newMethodBody, $content);

// Salvar o arquivo modificado
file_put_contents($cloudflareServicePath, $content);
output('Método updateDnsRecord no CloudflareService corrigido para receber o objeto DnsRecord diretamente.');

// Agora vamos verificar se existem outros métodos que precisam ser atualizados
output('Verificando outros métodos que possam precisar de correção...');

// Verificar o método para criar registros DNS
if (strpos($content, 'public function createDnsRecord($zoneId, $recordData)') !== false) {
    output('Encontrado método createDnsRecord que pode precisar de correção.');
    output('Recomendação: Verificar e adaptar o método createDnsRecord para também receber o objeto DnsRecord.');
}

// Verificar o método para excluir registros DNS
if (strpos($content, 'public function deleteDnsRecord($zoneId, $recordId)') !== false) {
    output('Encontrado método deleteDnsRecord que pode precisar de correção.');
    output('Recomendação: Verificar e adaptar o método deleteDnsRecord para também receber o objeto DnsRecord.');
}

output('Correção concluída! Recarregue a página e tente novamente.');
