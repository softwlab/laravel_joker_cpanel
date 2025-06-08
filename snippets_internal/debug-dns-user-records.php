<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Inicializar aplicação Laravel
$app = new Illuminate\Foundation\Application(__DIR__);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Verificando registros DNS e usuários associados...\n\n";

// Verificar modelo Usuario
echo "## Verificando modelo Usuario ##\n";
try {
    $usuarioClass = new ReflectionClass('App\Models\Usuario');
    echo "- Classe Usuario existe\n";
    
    // Verificar método dnsRecords
    if ($usuarioClass->hasMethod('dnsRecords')) {
        echo "- Método dnsRecords() existe no modelo Usuario\n";
    } else {
        echo "- ALERTA: Método dnsRecords() NÃO existe no modelo Usuario\n";
        
        echo "  Adicionando relacionamento dnsRecords() ao modelo Usuario...\n";
        $usuarioPath = __DIR__ . '/app/Models/Usuario.php';
        $usuarioContent = file_get_contents($usuarioPath);
        
        // Verificar se o método já existe, mesmo que a reflexão não o tenha encontrado
        if (strpos($usuarioContent, 'function dnsRecords') === false) {
            $lastBracePos = strrpos($usuarioContent, '}');
            
            if ($lastBracePos !== false) {
                $dnsRecordsMethod = '
    /**
     * Relacionamento com registros DNS
     */
    public function dnsRecords()
    {
        return $this->hasMany(\App\Models\DnsRecord::class, \'user_id\');
    }
';
                $usuarioContent = substr($usuarioContent, 0, $lastBracePos) . $dnsRecordsMethod . substr($usuarioContent, $lastBracePos);
                file_put_contents($usuarioPath, $usuarioContent);
                echo "  Relacionamento adicionado com sucesso.\n";
            }
        }
    }
} catch (Exception $e) {
    echo "- ERRO: " . $e->getMessage() . "\n";
}

// Verificar registros DNS no banco
echo "\n## Verificando registros DNS com usuários associados ##\n";
try {
    $dnsRecords = \App\Models\DnsRecord::whereNotNull('user_id')->limit(5)->get();
    echo "- Encontrados " . $dnsRecords->count() . " registros DNS com user_id não nulo\n";
    
    if ($dnsRecords->count() > 0) {
        foreach ($dnsRecords as $record) {
            echo "\n  Registro DNS #{$record->id}:\n";
            echo "  - user_id: " . $record->user_id . "\n";
            
            // Verificar se conseguimos obter o usuário
            $usuario = \App\Models\Usuario::find($record->user_id);
            if ($usuario) {
                echo "  - Usuario encontrado: ID={$usuario->id}, Nome='{$usuario->name}'\n";
                
                // Verificar acesso direto ao relacionamento
                echo "  - Tentando acessar via relacionamento: ";
                try {
                    $userViaRelation = $record->user;
                    echo $userViaRelation ? "Sucesso ('{$userViaRelation->name}')\n" : "Falha (null)\n";
                } catch (Exception $e) {
                    echo "Erro: " . $e->getMessage() . "\n";
                }
            } else {
                echo "  - ALERTA: Usuário com ID {$record->user_id} NÃO ENCONTRADO no banco de dados\n";
            }
        }
    }
} catch (Exception $e) {
    echo "- ERRO ao consultar registros DNS: " . $e->getMessage() . "\n";
}

// Verificar definição do relacionamento em DnsRecord
echo "\n## Verificando relacionamento no modelo DnsRecord ##\n";
try {
    $dnsRecordClass = new ReflectionClass('App\Models\DnsRecord');
    
    if ($dnsRecordClass->hasMethod('user')) {
        echo "- Método user() existe no modelo DnsRecord\n";
        
        // Verificar se o método está correto
        echo "  Examinando código do relacionamento:\n";
        $userMethod = $dnsRecordClass->getMethod('user');
        $fileName = $userMethod->getFileName();
        $startLine = $userMethod->getStartLine();
        $endLine = $userMethod->getEndLine();
        
        if ($fileName && file_exists($fileName)) {
            $fileContent = file($fileName);
            $methodContent = implode('', array_slice($fileContent, $startLine - 1, $endLine - $startLine + 1));
            echo "  $methodContent\n";
            
            // Verificar se o relacionamento está configurado corretamente
            if (strpos($methodContent, "belongsTo") !== false) {
                if (strpos($methodContent, "App\\Models\\Usuario") !== false || strpos($methodContent, "\\App\\Models\\Usuario") !== false) {
                    echo "  - Relacionamento usa o modelo Usuario correto\n";
                } else {
                    echo "  - ALERTA: Relacionamento pode estar usando um modelo incorreto\n";
                }
                
                if (strpos($methodContent, "'user_id'") !== false) {
                    echo "  - Relacionamento usa a chave estrangeira 'user_id'\n";
                } else {
                    echo "  - ALERTA: Relacionamento pode estar usando uma chave estrangeira incorreta\n";
                }
            }
        }
    } else {
        echo "- ALERTA: Método user() NÃO existe no modelo DnsRecord\n";
        
        // Adicionar o relacionamento
        echo "  Adicionando relacionamento user() ao modelo DnsRecord...\n";
        $dnsRecordPath = __DIR__ . '/app/Models/DnsRecord.php';
        $dnsRecordContent = file_get_contents($dnsRecordPath);
        
        if (strpos($dnsRecordContent, 'function user') === false) {
            $lastBracePos = strrpos($dnsRecordContent, '}');
            
            if ($lastBracePos !== false) {
                $userMethod = '
    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Usuario::class, \'user_id\');
    }
';
                $dnsRecordContent = substr($dnsRecordContent, 0, $lastBracePos) . $userMethod . substr($dnsRecordContent, $lastBracePos);
                file_put_contents($dnsRecordPath, $dnsRecordContent);
                echo "  Relacionamento adicionado com sucesso.\n";
            }
        }
    }
} catch (Exception $e) {
    echo "- ERRO: " . $e->getMessage() . "\n";
}

// Verificar a view
echo "\n## Verificando conteúdo atual da view ##\n";
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';
if (file_exists($viewPath)) {
    $viewContent = file_get_contents($viewPath);
    
    // Verificar o bloco de associações de usuário
    $pattern = '/@php\s*\$associations\s*=\s*\[\].*?@endphp/s';
    if (preg_match($pattern, $viewContent, $matches)) {
        echo "Bloco de associações encontrado na view:\n";
        echo $matches[0] . "\n";
        
        // Verificar se há algum problema específico com as aspas
        if (strpos($matches[0], 'Usuário: \' . $userData->name') !== false) {
            echo "O código parece estar correto.\n";
        } else {
            echo "ALERTA: Possível problema com a concatenação do nome do usuário.\n";
        }
    } else {
        echo "ALERTA: Não foi possível encontrar o bloco de código das associações.\n";
    }
}

echo "\n## Aplicando correção direta para garantir ##\n";
// Aplicar correção direta para garantir que o nome do usuário seja exibido
$correctedPhpBlock = '@php
    $associations = [];
    if ($record->bank) $associations[] = \'<span class="badge bg-primary">Link</span>\';
    if ($record->bankTemplate) $associations[] = \'<span class="badge bg-info">Template</span>\';
    if ($record->linkGroup) $associations[] = \'<span class="badge bg-warning">Grupo</span>\';
    if ($record->user_id) {
        $userData = \App\Models\Usuario::find($record->user_id);
        if ($userData && !empty($userData->name)) {
            $associations[] = \'<span class="badge bg-dark">Usuário: \' . $userData->name . \'</span>\';
        } else {
            $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
        }
    }
@endphp';

// Substituir o bloco na view
$viewContent = preg_replace($pattern, $correctedPhpBlock, $viewContent);
file_put_contents($viewPath, $viewContent);
echo "Correção aplicada diretamente à view.\n";

echo "\nLimpando cache do Laravel...\n";
system('php artisan cache:clear');
system('php artisan view:clear');
system('php artisan optimize:clear');

echo "\nVerificação e correção concluídas!\n";
