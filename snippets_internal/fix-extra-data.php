<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correção do campo extra_data no DnsRecordController...');

// Abrir o arquivo do controlador
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/DnsRecordController.php';
$content = file_get_contents($controllerPath);

// Localizar e corrigir o código problemático
$originalCode = '
        // Se a API for do tipo Cloudflare, atualizar dados extras
        if ($api->type === \'cloudflare\') {
            $extraData = $record->extra_data ?? [];
            $extraData[\'zone_id\'] = $request->input(\'zone_id\', $extraData[\'zone_id\'] ?? $api->config[\'cloudflare_zone_id\'] ?? \'\');
            $extraData[\'proxied\'] = $request->has(\'proxied\');
            $record->extra_data = $extraData;
            $record->save();
        }';

$fixedCode = '
        // Se a API for do tipo Cloudflare, atualizar dados extras
        if ($api->type === \'cloudflare\') {
            // Garantir que extra_data seja um array
            $extraData = is_array($record->extra_data) ? $record->extra_data : [];
            
            // Obter o zone_id do request, do registro existente, ou do config da API
            $currentZoneId = is_array($extraData) && isset($extraData[\'zone_id\']) ? $extraData[\'zone_id\'] : \'\';
            $configZoneId = is_array($api->config) && isset($api->config[\'cloudflare_zone_id\']) ? $api->config[\'cloudflare_zone_id\'] : \'\';
            $extraData[\'zone_id\'] = $request->input(\'zone_id\') ?: $currentZoneId ?: $configZoneId ?: \'\';
            
            // Definir se o registro está sob proxy
            $extraData[\'proxied\'] = $request->has(\'proxied\');
            
            // Salvar os dados extras atualizados
            $record->extra_data = $extraData;
            $record->save();
        }';

$newContent = str_replace($originalCode, $fixedCode, $content);

// Verificar se a substituição foi realizada
if ($newContent !== $content) {
    // Salvar as alterações no arquivo
    file_put_contents($controllerPath, $newContent);
    output('Código de manipulação de extra_data corrigido com sucesso!');
} else {
    // Se não encontrou o padrão exato, tentar uma abordagem mais específica
    output('Não foi possível encontrar o padrão exato. Tentando abordagem alternativa...');
    
    // Procurar apenas a linha problemática
    $problemLine = '$extraData[\'zone_id\'] = $request->input(\'zone_id\', $extraData[\'zone_id\'] ?? $api->config[\'cloudflare_zone_id\'] ?? \'\');';
    $safeLine = '            // Garantir que extra_data seja um array
            $extraData = is_array($record->extra_data) ? $record->extra_data : [];
            
            // Obter o zone_id do request, do registro existente, ou do config da API
            $currentZoneId = is_array($extraData) && isset($extraData[\'zone_id\']) ? $extraData[\'zone_id\'] : \'\';
            $configZoneId = is_array($api->config) && isset($api->config[\'cloudflare_zone_id\']) ? $api->config[\'cloudflare_zone_id\'] : \'\';
            $extraData[\'zone_id\'] = $request->input(\'zone_id\') ?: $currentZoneId ?: $configZoneId ?: \'\';';
    
    $newContent = str_replace($problemLine, $safeLine, $content);
    
    if ($newContent !== $content) {
        file_put_contents($controllerPath, $newContent);
        output('Linha problemática substituída com sucesso!');
    } else {
        output('ATENÇÃO: Não foi possível substituir automaticamente. É necessário editar manualmente o arquivo.');
        output('Por favor, abra o arquivo ' . $controllerPath . ' e modifique a linha 233.');
    }
}

output('Recarregue a página e tente novamente associar o usuário ao registro DNS.');
