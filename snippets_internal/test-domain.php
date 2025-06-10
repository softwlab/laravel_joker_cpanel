<?php

// Carregar o framework Laravel
require __DIR__.'/vendor/autoload.php';

// Carregar o .env
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configurar para mostrar erros
error_reporting(E_ALL);
ini_set('display_errors', true);

// Modelos que vamos usar
use App\Models\DnsRecord;
use App\Models\Usuario;
use App\Models\BankTemplate;
use App\Models\UserConfig;
use App\Models\CloudflareDomain;
use Illuminate\Support\Facades\DB;

// Domínio que queremos buscar
$domainName = "app.acessarchaveprime.com";

echo "Buscando informações para o domínio: {$domainName}\n\n";

// 1. Procurar no DnsRecord
$dnsRecord = DnsRecord::where('name', $domainName)
                   ->where('status', 'active')
                   ->first();

if ($dnsRecord) {
    echo "== ENCONTRADO DNS RECORD ==\n";
    echo "ID: " . $dnsRecord->id . "\n";
    echo "Nome: " . $dnsRecord->name . "\n";
    echo "Conteúdo: " . $dnsRecord->content . "\n";
    echo "Status: " . $dnsRecord->status . "\n";
    echo "User ID: " . ($dnsRecord->user_id ?? "NULL") . "\n\n";
    
    // 2. Buscar usuário associado
    if ($dnsRecord->user_id) {
        $usuario = Usuario::find($dnsRecord->user_id);
        if ($usuario) {
            echo "== USUÁRIO ENCONTRADO ==\n";
            echo "ID: " . $usuario->id . "\n";
            echo "Nome: " . $usuario->nome . "\n";
            echo "Email: " . $usuario->email . "\n";
            
            // 3. Buscar template direto
            $templates = BankTemplate::where('usuario_id', $usuario->id)->get();
            if ($templates && $templates->count() > 0) {
                echo "\n== TEMPLATES DO USUÁRIO ==\n";
                foreach ($templates as $template) {
                    echo "ID: " . $template->id . "\n";
                    echo "Nome: " . $template->name . "\n";
                    echo "Banco: " . $template->bank_code . "\n";
                    echo "Cor Primária: " . $template->primary_color . "\n";
                    echo "Cor Secundária: " . $template->secondary_color . "\n\n";
                }
            } else {
                echo "\nNenhum template direto encontrado para o usuário.\n";
            }
            
            // 4. Buscar configurações do usuário
            $configs = UserConfig::where('user_id', $usuario->id)->get();
            if ($configs && $configs->count() > 0) {
                echo "\n== CONFIGURAÇÕES DO USUÁRIO ==\n";
                foreach ($configs as $config) {
                    echo "ID: " . $config->id . "\n";
                    echo "Template ID: " . ($config->template_id ?? "NULL") . "\n";
                    echo "Record ID: " . ($config->record_id ?? "NULL") . "\n";
                    echo "Config: " . json_encode($config->config) . "\n";
                    echo "Config JSON: " . json_encode($config->config_json) . "\n\n";
                }
                
                // Se tiver template_id, buscar detalhes do template
                foreach ($configs as $config) {
                    if ($config->template_id) {
                        $configTemplate = BankTemplate::find($config->template_id);
                        if ($configTemplate) {
                            echo "\n== TEMPLATE VIA CONFIG ==\n";
                            echo "ID: " . $configTemplate->id . "\n";
                            echo "Nome: " . $configTemplate->name . "\n";
                            echo "Banco: " . $configTemplate->bank_code . "\n";
                            echo "Cor Primária: " . $configTemplate->primary_color . "\n";
                            echo "Cor Secundária: " . $configTemplate->secondary_color . "\n\n";
                        }
                    }
                }
            } else {
                echo "\nNenhuma configuração encontrada para o usuário.\n";
            }
        } else {
            echo "Usuário não encontrado com ID: " . $dnsRecord->user_id . "\n";
        }
    } else {
        echo "Registro DNS não tem usuário associado.\n";
    }
} else {
    echo "DNS Record não encontrado para o domínio: {$domainName}\n";
}

// 5. Tentar buscar pelo domínio Cloudflare
$cfDomain = CloudflareDomain::where('name', 'like', '%' . $domainName . '%')->first();
if ($cfDomain) {
    echo "\n== DOMÍNIO CLOUDFLARE ENCONTRADO ==\n";
    echo "ID: " . $cfDomain->id . "\n";
    echo "Nome: " . $cfDomain->name . "\n";
    echo "Zone ID: " . $cfDomain->zone_id . "\n";
    echo "Status: " . $cfDomain->status . "\n\n";
    
    // Buscar usuários associados via pivot
    $associacoes = DB::table('cloudflare_domain_usuario')
                    ->where('cloudflare_domain_id', $cfDomain->id)
                    ->get();
    
    if ($associacoes && $associacoes->count() > 0) {
        echo "== ASSOCIAÇÕES DE USUÁRIOS ==\n";
        foreach ($associacoes as $assoc) {
            echo "Usuário ID: " . $assoc->usuario_id . "\n";
            echo "Status: " . $assoc->status . "\n";
            echo "Config: " . ($assoc->config ?? "NULL") . "\n";
            echo "Notas: " . ($assoc->notes ?? "NULL") . "\n\n";
            
            // Verificar se esse usuário tem templates
            $assocUser = Usuario::find($assoc->usuario_id);
            if ($assocUser) {
                echo "Usuário: " . $assocUser->nome . "\n";
                
                $assocTemplates = BankTemplate::where('usuario_id', $assoc->usuario_id)->get();
                if ($assocTemplates && $assocTemplates->count() > 0) {
                    echo "\n== TEMPLATES DO USUÁRIO ASSOCIADO ==\n";
                    foreach ($assocTemplates as $template) {
                        echo "ID: " . $template->id . "\n";
                        echo "Nome: " . $template->name . "\n";
                        echo "Banco: " . $template->bank_code . "\n";
                        echo "Cor Primária: " . $template->primary_color . "\n";
                        echo "Cor Secundária: " . $template->secondary_color . "\n\n";
                    }
                } else {
                    echo "Nenhum template para este usuário associado.\n";
                }
            }
        }
    } else {
        echo "Nenhuma associação de usuário encontrada para o domínio Cloudflare.\n";
    }
} else {
    echo "\nNenhum domínio Cloudflare encontrado para: {$domainName}\n";
}

// 6. Verificar diretamente se há algum template no banco
$allTemplates = BankTemplate::all();
echo "\n== TODOS OS TEMPLATES DISPONÍVEIS ==\n";
echo "Total de templates no sistema: " . $allTemplates->count() . "\n";
if ($allTemplates->count() > 0) {
    foreach ($allTemplates as $template) {
        echo "ID: " . $template->id . "\n";
        echo "Nome: " . $template->name . "\n";
        echo "Usuário ID: " . ($template->usuario_id ?? "NULL") . "\n";
        echo "Banco: " . $template->bank_code . "\n\n";
    }
} else {
    echo "Nenhum template encontrado no sistema.\n";
}

echo "\nFIM DA ANÁLISE\n";
