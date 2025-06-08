<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CloudflareDomain;
use App\Models\DnsRecord;

echo "Removendo domínios fictícios (exemplo*)...\n";

// Apagar domínios que começam com 'exemplo'
$domains = CloudflareDomain::where('name', 'like', 'exemplo%')->get();

$count = 0;
foreach ($domains as $domain) {
    echo "Removendo {$domain->name} (ID: {$domain->id}, Zone ID: {$domain->zone_id})...\n";
    
    // Remover todas as associações com usuários (tabela pivot)
    $domain->usuarios()->detach();
    
    // Não tentamos remover registros DNS através do relacionamento (que está mal configurado)
    // $domain->dnsRecords()->delete();
    
    // Remover o domínio
    $domain->delete();
    
    $count++;
}

echo "\nTotal de {$count} domínios fictícios removidos com sucesso!\n";
echo "Os domínios foram removidos do banco de dados junto com suas associações de usuários.\n";
