<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\Usuario;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\LinkGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Este seeder exporta os dados existentes do sistema para arquivos que podem ser usados como seeders
     */
    public function run(): void
    {
        $this->command->info('Exportando dados existentes para seeders...');
        
        // Diretório onde serão salvos os arquivos de exportação
        $exportDir = database_path('seeders/exports');
        
        // Criar diretório se não existir
        if (!File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }
        
        // Exportar domínios Cloudflare
        $this->exportCloudflareDomainsToSeeder($exportDir);
        
        // Exportar registros DNS
        $this->exportDnsRecordsToSeeder($exportDir);
        
        // Exportar associações entre domínios e usuários
        $this->exportDomainUserAssociationsToSeeder($exportDir);
        
        $this->command->info('✓ Exportação concluída! Arquivos de seeder gerados em: ' . $exportDir);
        $this->command->info('Para restaurar os dados, use os arquivos gerados como seeders.');
    }
    
    /**
     * Exporta os domínios Cloudflare para um arquivo seeder
     */
    private function exportCloudflareDomainsToSeeder($exportDir)
    {
        $domains = CloudflareDomain::all();
        
        if ($domains->isEmpty()) {
            $this->command->warn('Nenhum domínio Cloudflare encontrado para exportar.');
            return;
        }
        
        $content = "<?php\n\nnamespace Database\\Seeders\\Exports;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\CloudflareDomain;\n";
        $content .= "use App\\Models\\ExternalApi;\n\n";
        $content .= "class CloudflareDomainExportSeeder extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";
        $content .= "        \$cloudflareApi = ExternalApi::where('type', 'cloudflare')->first();\n\n";
        $content .= "        if (!\$cloudflareApi) {\n";
        $content .= "            \$this->command->error('API do Cloudflare não encontrada. Execute o ExternalApiSeeder primeiro.');\n";
        $content .= "            return;\n        }\n\n";
        $content .= "        // Dados exportados dos domínios existentes\n";
        
        foreach ($domains as $domain) {
            $content .= "        CloudflareDomain::create([\n";
            $content .= "            'external_api_id' => \$cloudflareApi->id,\n";
            $content .= "            'zone_id' => '{$domain->zone_id}',\n";
            $content .= "            'name' => '{$domain->name}',\n";
            $content .= "            'status' => '{$domain->status}',\n";
            $content .= "            'is_ghost' => " . ($domain->is_ghost ? 'true' : 'false') . ",\n";
            
            // Tratar campos JSON
            $nameServers = json_encode($domain->name_servers);
            $content .= "            'name_servers' => '{$nameServers}',\n";
            $content .= "            'records_count' => {$domain->records_count},\n";
            $content .= "        ]);\n\n";
        }
        
        $content .= "        \$this->command->info('✓ {$domains->count()} domínios Cloudflare importados com sucesso!');\n";
        $content .= "    }\n}";
        
        // Salvar arquivo
        File::put($exportDir . '/CloudflareDomainExportSeeder.php', $content);
        $this->command->info("Exportados {$domains->count()} domínios Cloudflare.");
    }
    
    /**
     * Exporta os registros DNS para um arquivo seeder
     */
    private function exportDnsRecordsToSeeder($exportDir)
    {
        $dnsRecords = DnsRecord::all();
        
        if ($dnsRecords->isEmpty()) {
            $this->command->warn('Nenhum registro DNS encontrado para exportar.');
            return;
        }
        
        $content = "<?php\n\nnamespace Database\\Seeders\\Exports;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\DnsRecord;\n";
        $content .= "use App\\Models\\ExternalApi;\n\n";
        $content .= "class DnsRecordExportSeeder extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";
        $content .= "        \$cloudflareApi = ExternalApi::where('type', 'cloudflare')->first();\n\n";
        $content .= "        if (!\$cloudflareApi) {\n";
        $content .= "            \$this->command->error('API do Cloudflare não encontrada. Execute o ExternalApiSeeder primeiro.');\n";
        $content .= "            return;\n        }\n\n";
        $content .= "        // Dados exportados dos registros DNS existentes\n";
        
        foreach ($dnsRecords as $record) {
            $content .= "        DnsRecord::create([\n";
            $content .= "            'external_api_id' => \$cloudflareApi->id,\n";
            
            // Adicionar apenas os campos que existem no registro atual
            if ($record->user_id) $content .= "            'user_id' => {$record->user_id},\n";
            if ($record->bank_id) $content .= "            'bank_id' => {$record->bank_id},\n";
            if ($record->bank_template_id) $content .= "            'bank_template_id' => {$record->bank_template_id},\n";
            if ($record->link_group_id) $content .= "            'link_group_id' => {$record->link_group_id},\n";
            
            $content .= "            'record_type' => '{$record->record_type}',\n";
            $content .= "            'name' => '{$record->name}',\n";
            $content .= "            'content' => '{$record->content}',\n";
            $content .= "            'ttl' => {$record->ttl},\n";
            
            if ($record->priority) $content .= "            'priority' => {$record->priority},\n";
            
            $content .= "            'status' => '{$record->status}',\n";
            
            // Tratar campos JSON
            if ($record->extra_data) {
                $extraData = json_encode($record->extra_data);
                $content .= "            'extra_data' => '{$extraData}',\n";
            }
            
            $content .= "        ]);\n\n";
        }
        
        $content .= "        \$this->command->info('✓ {$dnsRecords->count()} registros DNS importados com sucesso!');\n";
        $content .= "    }\n}";
        
        // Salvar arquivo
        File::put($exportDir . '/DnsRecordExportSeeder.php', $content);
        $this->command->info("Exportados {$dnsRecords->count()} registros DNS.");
    }
    
    /**
     * Exporta as associações entre domínios e usuários
     */
    private function exportDomainUserAssociationsToSeeder($exportDir)
    {
        $associations = DB::table('cloudflare_domain_usuario')->get();
        
        if ($associations->isEmpty()) {
            $this->command->warn('Nenhuma associação entre domínios e usuários encontrada para exportar.');
            return;
        }
        
        $content = "<?php\n\nnamespace Database\\Seeders\\Exports;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\CloudflareDomain;\n";
        $content .= "use App\\Models\\Usuario;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
        $content .= "class DomainUserAssociationExportSeeder extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";
        $content .= "        // Verificar se existem domínios e usuários\n";
        $content .= "        \$domainsExist = CloudflareDomain::exists();\n";
        $content .= "        \$usersExist = Usuario::exists();\n\n";
        $content .= "        if (!\$domainsExist || !\$usersExist) {\n";
        $content .= "            \$this->command->error('Domínios ou usuários não encontrados. Execute os seeders CloudflareDomainExportSeeder e UsuarioSeeder primeiro.');\n";
        $content .= "            return;\n        }\n\n";
        $content .= "        // Dados exportados das associações existentes\n";
        
        foreach ($associations as $association) {
            $content .= "        DB::table('cloudflare_domain_usuario')->insert([\n";
            $content .= "            'cloudflare_domain_id' => {$association->cloudflare_domain_id},\n";
            $content .= "            'usuario_id' => {$association->usuario_id},\n";
            $content .= "            'status' => '{$association->status}',\n";
            
            // Tratar campos JSON e nullable
            if ($association->config) {
                $config = json_encode($association->config);
                $content .= "            'config' => '{$config}',\n";
            }
            
            if ($association->notes) {
                $notes = str_replace("'", "\\'", $association->notes); // Escapar aspas simples
                $content .= "            'notes' => '{$notes}',\n";
            }
            
            // Datas
            $createdAt = $association->created_at ?? now()->toDateTimeString();
            $updatedAt = $association->updated_at ?? now()->toDateTimeString();
            $content .= "            'created_at' => '{$createdAt}',\n";
            $content .= "            'updated_at' => '{$updatedAt}',\n";
            $content .= "        ]);\n\n";
        }
        
        $content .= "        \$this->command->info('✓ {$associations->count()} associações entre domínios e usuários importadas com sucesso!');\n";
        $content .= "    }\n}";
        
        // Salvar arquivo
        File::put($exportDir . '/DomainUserAssociationExportSeeder.php', $content);
        $this->command->info("Exportadas {$associations->count()} associações entre domínios e usuários.");
    }
}
