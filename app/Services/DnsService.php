<?php

namespace App\Services;

use App\Models\DnsRecord;
use App\Models\ExternalApi;
use App\Services\CloudflareService;
use Illuminate\Support\Facades\Log;

class DnsService
{
    /**
     * Obtém o serviço específico para o tipo de API
     * 
     * @param ExternalApi $api
     * @return mixed
     */
    public function getApiService(ExternalApi $api)
    {
        switch ($api->type) {
            case 'cloudflare':
                return new CloudflareService($api);
            case 'route53':
                // Implementação futura
                throw new \Exception('API Route53 não implementada ainda');
            default:
                throw new \Exception('Tipo de API não suportado: ' . $api->type);
        }
    }

    /**
     * Testa a conexão com uma API externa
     * 
     * @param ExternalApi $api
     * @return array
     */
    public function testConnection(ExternalApi $api)
    {
        try {
            $service = $this->getApiService($api);
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtém os domínios/zonas de uma API externa
     * 
     * @param ExternalApi $api
     * @return array
     */
    public function getDomainsForApi(ExternalApi $api)
    {
        try {
            // Teste de conexão para verificar se a API está funcionando
            $connectionTest = $this->testConnection($api);
            
            if (!$connectionTest['success']) {
                return [
                    'success' => false,
                    'message' => 'Erro na conexão com a API: ' . $connectionTest['message']
                ];
            }
            
            // Obter o serviço específico para o tipo de API
            $apiService = $this->getApiService($api);
            
            // Obter as zonas/domínios
            $result = $apiService->getZones();
            
            // Log para depuração da estrutura de dados retornada
            Log::info('Resposta obtida da API:', [
                'api_type' => $api->type,
                'success' => $result['success'] ?? false,
                'has_domains_key' => isset($result['domains']),
                'has_zones_key' => isset($result['zones']),  // Manter compatibilidade com ambos os nomes
                'keys' => array_keys($result),
                'result_structure' => json_encode($result)
            ]);
            
            if (!$result['success']) {
                return [
                    'success' => false,
                    'message' => 'Erro ao obter domínios: ' . ($result['message'] ?? 'Erro desconhecido')
                ];
            }
            
            // Verificar se existe a chave 'domains' ou 'zones' no resultado
            $hasDomainsData = isset($result['domains']) && is_array($result['domains']);
            $hasZonesData = isset($result['zones']) && is_array($result['zones']);
            
            // Se não temos nem domains nem zones nos dados
            if (!$hasDomainsData && !$hasZonesData) {
                Log::error('Chaves "domains" e "zones" ausentes ou não são arrays na resposta da API', [
                    'api_type' => $api->type,
                    'result_keys' => array_keys($result)
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Formato de resposta inválido da API: Informação de domínios não encontrada'
                ];
            }
            
            // Dar preferência para a chave 'domains', mas usar 'zones' se 'domains' não estiver disponivel
            $domainsData = $hasDomainsData ? $result['domains'] : $result['zones'];
            
            // Log informando qual chave estamos usando
            Log::info('Usando dados de domínios da chave: ' . ($hasDomainsData ? 'domains' : 'zones'), [
                'data_count' => count($domainsData)
            ]);
            
            return [
                'success' => true,
                'domains' => $domainsData
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao obter domínios: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao obter domínios: ' . $e->getMessage(),
                'domains' => []
            ];
        }
    }

    /**
     * Cria um registro DNS na API externa
     * 
     * @param DnsRecord $record
     * @return array
     */
    public function createRecord(DnsRecord $record)
    {
        try {
            $api = $record->externalApi;
            $service = $this->getApiService($api);
            return $service->createDnsRecord($record);
        } catch (\Exception $e) {
            Log::error('Erro ao criar registro DNS na API externa', [
                'record_id' => $record->id,
                'api_id' => $record->external_api_id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao criar registro DNS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Atualiza um registro DNS na API externa
     * 
     * @param DnsRecord $record
     * @return array
     */
    public function updateRecord(DnsRecord $record)
    {
        try {
            $api = $record->externalApi;
            $service = $this->getApiService($api);
            return $service->updateDnsRecord($record);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar registro DNS na API externa', [
                'record_id' => $record->id,
                'api_id' => $record->external_api_id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao atualizar registro DNS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Exclui um registro DNS na API externa
     * 
     * @param DnsRecord $record
     * @return array
     */
    public function deleteRecord(DnsRecord $record)
    {
        try {
            $api = $record->externalApi;
            $service = $this->getApiService($api);
            return $service->deleteDnsRecord($record);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir registro DNS na API externa', [
                'record_id' => $record->id,
                'api_id' => $record->external_api_id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao excluir registro DNS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sincroniza um registro DNS com a API externa
     * 
     * @param DnsRecord $record
     * @return array
     */
    public function syncRecord(DnsRecord $record)
    {
        try {
            $api = $record->externalApi;
            $service = $this->getApiService($api);
            return $service->syncRecord($record);
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registro DNS com API externa', [
                'record_id' => $record->id,
                'api_id' => $record->external_api_id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar registro DNS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sincroniza todos os registros DNS de uma API externa
     * 
     * @param ExternalApi $api
     * @return array
     */
    public function syncAllRecords(ExternalApi $api)
    {
        try {
            $service = $this->getApiService($api);
            
            if ($api->type === 'cloudflare') {
                // Buscar todos os domínios disponíveis na conta Cloudflare
                $zones = $service->getZones();
                
                \Illuminate\Support\Facades\Log::info('Buscando todas as zonas/domínios disponíveis no Cloudflare', [
                    'api_id' => $api->id,
                    'success' => $zones['success'] ?? false,
                    'count' => count($zones['zones'] ?? [])
                ]);
                
                if (!isset($zones['success']) || $zones['success'] !== true) {
                    return [
                        'success' => false,
                        'message' => 'Falha ao buscar domínios do Cloudflare: ' . ($zones['message'] ?? 'Erro desconhecido')
                    ];
                }
                
                // Armazenar os domínios encontrados localmente
                $this->storeCloudflareZones($api, $zones['zones']);
                
                // Para cada domínio, sincronizar os registros DNS
                $totalZones = count($zones['zones']);
                $syncedZones = 0;
                $totalRecords = 0;
                $errors = [];
                
                foreach ($zones['zones'] as $zone) {
                    $zoneId = $zone['id'];
                    $result = $service->syncRecordsFromCloudflare($zoneId);
                    
                    if (isset($result['success']) && $result['success'] === true) {
                        $syncedZones++;
                        $totalRecords += $result['records_synced'] ?? 0;
                    } else {
                        $errors[] = "Falha ao sincronizar {$zone['name']}: " . ($result['message'] ?? 'Erro desconhecido');
                    }
                }
                
                // Construir mensagem de estatísticas
                $stats = [
                    'domains_total' => $totalZones,
                    'domains_synced' => $syncedZones,
                    'records_total' => $totalRecords
                ];
                
                if ($syncedZones == $totalZones) {
                    return [
                        'success' => true,
                        'message' => "Sincronização completa! {$syncedZones} domínios e {$totalRecords} registros DNS sincronizados.",
                        'stats' => $stats
                    ];
                } else {
                    return [
                        'success' => true,
                        'message' => "Sincronização parcial: {$syncedZones}/{$totalZones} domínios e {$totalRecords} registros DNS. Alguns erros ocorreram.",
                        'stats' => $stats,
                        'errors' => $errors
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Sincronização não implementada para o tipo de API: ' . $api->type
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registros DNS com API externa', [
                'api_id' => $api->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar registros DNS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Armazena ou atualiza informações dos domínios (zones) do Cloudflare no banco de dados
     *
     * @param ExternalApi $api API externa do Cloudflare
     * @param array $zones Lista de zonas/domínios do Cloudflare
     * @return void
     */
    protected function storeCloudflareZones(ExternalApi $api, array $zones)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Armazenando informações de ' . count($zones) . ' domínios do Cloudflare');
            
            // Verificar se existe uma tabela para armazenar os domínios
            if (!\Illuminate\Support\Facades\Schema::hasTable('cloudflare_domains')) {
                \Illuminate\Support\Facades\Log::warning('Tabela cloudflare_domains não encontrada. Criando...');
                
                \Illuminate\Support\Facades\Schema::create('cloudflare_domains', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('external_api_id')->constrained()->onDelete('cascade');
                    $table->string('zone_id')->index();
                    $table->string('name')->index();
                    $table->string('status')->default('active');
                    $table->boolean('paused')->default(false);
                    $table->jsonb('meta')->nullable();
                    $table->timestamps();
                    
                    $table->unique(['external_api_id', 'zone_id']);
                });
            }
            
            foreach ($zones as $zone) {
                // Buscar domínio existente ou criar novo
                $domain = \App\Models\CloudflareDomain::updateOrCreate(
                    [
                        'external_api_id' => $api->id,
                        'zone_id' => $zone['id']
                    ],
                    [
                        'name' => $zone['name'],
                        'status' => $zone['status'],
                        'paused' => $zone['paused'] ?? false,
                        'meta' => json_encode([
                            'created_on' => $zone['created_on'] ?? null,
                            'modified_on' => $zone['modified_on'] ?? null,
                            'activated_on' => $zone['activated_on'] ?? null,
                            'plan' => $zone['plan'] ?? [],
                            'owner' => $zone['owner'] ?? [],
                            'account' => $zone['account'] ?? [],
                            'name_servers' => $zone['name_servers'] ?? []
                        ])
                    ]
                );
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao armazenar domínios do Cloudflare: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTrace()
            ]);
        }
    }
    
    /**
     * Obtém as estatísticas de uma API externa
     * 
     * @param ExternalApi $api
     * @return array
     */
    public function getApiStats(ExternalApi $api)
    {
        $totalRecords = DnsRecord::where('external_api_id', $api->id)->count();
        $activeRecords = DnsRecord::where('external_api_id', $api->id)->where('status', 'active')->count();
        $inactiveRecords = $totalRecords - $activeRecords;
        
        // Contagem por tipo de registro
        $recordTypes = DnsRecord::where('external_api_id', $api->id)
            ->selectRaw('record_type, count(*) as total')
            ->groupBy('record_type')
            ->pluck('total', 'record_type')
            ->toArray();
        
        return [
            'total_records' => $totalRecords,
            'active_records' => $activeRecords,
            'inactive_records' => $inactiveRecords,
            'record_types' => $recordTypes,
        ];
    }
    
    /**
     * Obtém todos os domínios disponíveis para uma API externa
     *
     * @param ExternalApi $api
     * @return array
     */
    public function getDomainsForApi(ExternalApi $api)
    {
        try {
            if ($api->type === 'cloudflare') {
                $service = $this->getApiService($api);
                
                // Verificar primeiro se temos domínios armazenados no banco
                $savedDomains = \App\Models\CloudflareDomain::where('external_api_id', $api->id)
                    ->orderBy('name')
                    ->get();
                    
                if ($savedDomains->count() > 0) {
                    // Se temos domínios salvos, vamos usar os dados salvos
                    $formattedDomains = [];
                    foreach ($savedDomains as $domain) {
                        // Calcular número de registros de cada domínio
                        $recordsCount = DnsRecord::where('external_api_id', $api->id)
                            ->whereRaw("JSON_EXTRACT(extra_data, '$.cloudflare_zone_id') = ?", [$domain->zone_id])
                            ->count();
                            
                        $data = [
                            'id' => $domain->zone_id,
                            'name' => $domain->name,
                            'status' => $domain->status,
                            'paused' => $domain->paused,
                            'records_count' => $recordsCount,
                            'is_ghost' => $domain->is_ghost ?? false,
                            'name_servers' => $domain->name_servers ?? []
                        ];
                        
                        // Se temos metadados armazenados, adicionar ao resultado
                        if (!empty($domain->meta)) {
                            $meta = $domain->meta;
                            if (is_string($meta)) {
                                $meta = json_decode($meta, true);
                            }
                            
                            $data['created_on'] = $meta['created_on'] ?? null;
                            $data['modified_on'] = $meta['modified_on'] ?? null;
                            $data['activated_on'] = $meta['activated_on'] ?? null;
                            $data['owner'] = $meta['owner'] ?? [];
                        }
                        
                        $formattedDomains[] = $data;
                    }
                    
                    return [
                        'success' => true, 
                        'domains' => $formattedDomains,
                        'source' => 'database'
                    ];
                }
                
                // Se não temos domínios salvos, consultar a API e salvá-los
                $result = $service->getZones();
                
                if (!isset($result['success']) || $result['success'] !== true) {
                    return [
                        'success' => false,
                        'message' => 'Falha ao buscar domínios: ' . ($result['message'] ?? 'Erro desconhecido')
                    ];
                }
                
                // Salvar os domínios retornados para consultas futuras
                $this->storeCloudflareZones($api, $result['zones']);
                
                return [
                    'success' => true,
                    'domains' => $result['zones'],
                    'source' => 'api'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Tipo de API não suportada para listagem de domínios'
            ];
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao buscar domínios: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao buscar domínios: ' . $e->getMessage()
            ];
        }
    }
}
