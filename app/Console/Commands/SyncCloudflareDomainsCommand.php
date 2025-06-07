<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExternalApi;
use App\Services\DnsService;

class SyncCloudflareDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudflare:sync-domains {api_id? : ID da API externa do Cloudflare}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza todos os domínios e registros DNS do Cloudflare';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiId = $this->argument('api_id');
        
        try {
            if ($apiId) {
                // Sincronizar apenas a API específica
                $api = ExternalApi::findOrFail($apiId);
                
                if ($api->type !== 'cloudflare') {
                    $this->error("A API #$apiId não é do tipo Cloudflare.");
                    return 1;
                }
                
                $this->syncApi($api);
            } else {
                // Sincronizar todas as APIs Cloudflare
                $apis = ExternalApi::where('type', 'cloudflare')->get();
                
                if ($apis->isEmpty()) {
                    $this->warn("Nenhuma API Cloudflare encontrada para sincronização.");
                    return 0;
                }
                
                $this->info("Encontradas " . $apis->count() . " APIs Cloudflare para sincronizar.");
                
                foreach ($apis as $api) {
                    $this->syncApi($api);
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Ocorreu um erro: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
    
    /**
     * Sincroniza domínios e registros DNS de uma API específica
     * 
     * @param ExternalApi $api
     * @return void
     */
    protected function syncApi(ExternalApi $api)
    {
        $this->info("Sincronizando domínios da API #{$api->id}: {$api->name}");
        
        $dnsService = app(DnsService::class);
        $result = $dnsService->syncAllRecords($api);
        
        if (!isset($result['success']) || !$result['success']) {
            $this->error("Falha na sincronização: " . ($result['message'] ?? 'Erro desconhecido'));
            return;
        }
        
        $this->info("Sincronização concluída: " . $result['message']);
        
        if (isset($result['stats'])) {
            $stats = $result['stats'];
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Domínios encontrados', $stats['domains_total'] ?? 0],
                    ['Domínios sincronizados', $stats['domains_synced'] ?? 0],
                    ['Registros DNS sincronizados', $stats['records_total'] ?? 0]
                ]
            );
        }
        
        if (isset($result['errors']) && count($result['errors']) > 0) {
            $this->warn("Alguns erros ocorreram durante a sincronização:");
            foreach ($result['errors'] as $error) {
                $this->line(" - $error");
            }
        }
    }
}
