<?php

namespace App\Services;

use App\Models\DnsRecord;
use App\Models\ExternalApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    protected $api;
    protected $baseUrl = 'https://api.cloudflare.com/client/v4';
    protected $headers = [];

    public function __construct(ExternalApi $api)
    {
        $this->api = $api;
        
        if ($api->type !== 'cloudflare') {
            throw new \Exception('API fornecida não é do tipo Cloudflare');
        }
        
        // Configurar headers de autenticação
        $this->configureAuthentication();
    }

    /**
     * Configura os headers de autenticação com base nas configurações da API
     */
    protected function configureAuthentication()
    {
        $config = $this->api->config;
        
        // Log para depuração completa da configuração
        Log::debug('Configuração da API Cloudflare (dados brutos):', ['config' => $config]);
        
        // Forçar uso do token que foi configurado recentemente
        if (!empty($config['cloudflare_api_token'])) {
            // Método moderno: API Token (recomendado pela Cloudflare)
            $token = trim($config['cloudflare_api_token']);
            
            $this->headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ];
            
            Log::info('Usando autenticação com API Token', [
                'token_length' => strlen($token),
                'auth_type' => 'token'
            ]);
        } 
        // Fallback para o método tradicional com email + key
        elseif (!empty($config['cloudflare_api_key']) && !empty($config['cloudflare_email'])) {
            // Método legado: API Key + Email
            $email = trim($config['cloudflare_email']);
            $apiKey = trim($config['cloudflare_api_key']);
            
            // Garantir que não há espaços ou caracteres problemáticos
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            
            $this->headers = [
                'X-Auth-Email' => $email,
                'X-Auth-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ];
            
            Log::info('Usando autenticação com Email + API Key', [
                'email' => $email,
                'key_length' => strlen($apiKey),
                'auth_type' => 'api_key'
            ]);
        } else {
            // Tentativa final com valores estritamente determinados
            $email = 'andressaworking1707@gmail.com';
            $apiKey = 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7';
            $token = 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t';
            
            // Configurar primeiramente o método via token (preferido)
            $this->headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ];
            
            Log::info('Usando credenciais fixas para autenticação', [
                'auth_type' => 'token (fixed)',
                'email' => $email
            ]);
        }
        
        // Log dos headers montados
        Log::info('Headers configurados para API Cloudflare:', [
            'headers_keys' => array_keys($this->headers)
        ]);
    }

    /**
     * Testa a conexão com a API do Cloudflare
     * 
     * @return array
     */
    public function testConnection()
    {
        try {
            // Log do endpoint que será chamado
            Log::info('Testando conexão com Cloudflare', [
                'url' => $this->baseUrl . '/user',
                'header_keys' => array_keys($this->headers)
            ]);
            
            // Realizar a requisição
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/user');
            
            // Obter os dados da resposta
            $data = $response->json();
            $status = $response->status();
            
            Log::info('Resposta da API Cloudflare', [
                'status' => $status,
                'success' => $data['success'] ?? false,
                'response_headers' => $response->headers()
            ]);
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Conexão com a API do Cloudflare realizada com sucesso.',
                    'data' => $data
                ];
            } else {
                $errorMessage = 'Falha na conexão';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                        
                        // Se tiver chain de erros, adicionar
                        if (isset($error['error_chain'])) {
                            foreach ($error['error_chain'] as $chainError) {
                                $errors[] = '  - ' . $chainError['message'];
                            }
                        }
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro na conexão com Cloudflare', [
                    'status' => $status,
                    'errors' => $data['errors'] ?? [],
                    'message' => $errorMessage
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $data
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao conectar com a API Cloudflare: ' . $e->getMessage();
            Log::error($errorMessage, [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Obtém todas as zonas (domínios) disponíveis na conta Cloudflare
     * 
     * @return array
     */
    public function getZones()
    {
        try {
            // Log da requisição que estamos fazendo
            Log::info('Requisitando zonas da API Cloudflare', [
                'url' => $this->baseUrl . '/zones',
                'headers_keys' => array_keys($this->headers),
                'params' => ['per_page' => 50, 'status' => 'active']
            ]);
            
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/zones', [
                    'per_page' => 50,
                    'status' => 'active'
                ]);
            
            $data = $response->json();
            
            // Log da resposta bruta para depuração
            Log::info('Resposta bruta da API Cloudflare', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'has_success_key' => isset($data['success']),
                'success_value' => $data['success'] ?? null,
                'data_keys' => array_keys($data),
                'has_result' => isset($data['result']),
                'result_count' => isset($data['result']) ? count($data['result']) : 0
            ]);
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                $zones = [];
                
                if (!empty($data['result']) && is_array($data['result'])) {
                    foreach ($data['result'] as $zone) {
                        $zones[] = [
                            'id' => $zone['id'],
                            'name' => $zone['name'],
                            'status' => $zone['status'],
                            'name_servers' => $zone['name_servers'] ?? [],
                            'paused' => $zone['paused'] ?? false
                        ];
                    }
                } else {
                    Log::warning('API Cloudflare retornou um array de resultados vazio ou inválido');
                }
                
                Log::info('Zonas processadas com sucesso', [
                    'count' => count($zones),
                    'sample' => !empty($zones) ? $zones[0]['name'] : 'Nenhuma zona'
                ]);
                
                return [
                    'success' => true,
                    'domains' => $zones,  // Usa 'domains' como padrão em todo sistema
                    'zones' => $zones,    // Mantém 'zones' para compatibilidade
                    'total' => count($zones)
                ];
            } else {
                $errorMessage = 'Erro ao obter as zonas';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro ao obter zonas do Cloudflare', [
                    'errors' => $data['errors'] ?? []
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao obter as zonas do Cloudflare: ' . $e->getMessage();
            Log::error($errorMessage);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Obtém os registros DNS de uma zona específica
     * 
     * @param string $zoneId
     * @return array
     */
    public function getDnsRecords($zoneId)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/zones/' . $zoneId . '/dns_records', [
                    'per_page' => 100
                ]);
            
            $data = $response->json();
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                $records = [];
                
                foreach ($data['result'] as $record) {
                    $records[] = [
                        'id' => $record['id'],
                        'type' => $record['type'],
                        'name' => $record['name'],
                        'content' => $record['content'],
                        'ttl' => $record['ttl'],
                        'proxied' => $record['proxied'] ?? false
                    ];
                }
                
                return [
                    'success' => true,
                    'records' => $records,
                    'total' => count($records)
                ];
            } else {
                $errorMessage = 'Erro ao obter os registros DNS';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro ao obter registros DNS do Cloudflare', [
                    'zone_id' => $zoneId,
                    'errors' => $data['errors'] ?? []
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao obter os registros DNS: ' . $e->getMessage();
            Log::error($errorMessage, [
                'zone_id' => $zoneId
            ]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }
    
    /**
     * Cria um novo registro DNS em uma zona específica
     * 
     * @param string $zoneId
     * @param array $recordData
     * @return array
     */
    public function createDnsRecord($zoneId, $recordData)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/zones/' . $zoneId . '/dns_records', $recordData);
            
            $data = $response->json();
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Registro DNS criado com sucesso',
                    'record' => $data['result']
                ];
            } else {
                $errorMessage = 'Erro ao criar registro DNS';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro ao criar registro DNS no Cloudflare', [
                    'zone_id' => $zoneId,
                    'record_data' => $recordData,
                    'errors' => $data['errors'] ?? []
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao criar registro DNS: ' . $e->getMessage();
            Log::error($errorMessage, [
                'zone_id' => $zoneId,
                'record_data' => $recordData
            ]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }
    
    /**
     * Atualiza um registro DNS existente
     * 
     * @param \App\Models\DnsRecord $record O modelo de registro DNS a atualizar
     * @return array Resultado da operação
     */
    public function updateDnsRecord(\App\Models\DnsRecord $record)
    {
        // Extrair dados necessários do objeto DnsRecord
        $zoneId = $record->cloudflare_domain->zone_id;
        $recordId = $record->cloudflare_record_id;
        
        // Preparar dados para atualização
        $recordData = [
            'type' => $record->type,
            'name' => $record->name,
            'content' => $record->content,
            'ttl' => $record->ttl,
            'proxied' => $record->proxied
        ];
        
        try {
            $response = Http::withHeaders($this->headers)
                ->put($this->baseUrl . '/zones/' . $zoneId . '/dns_records/' . $recordId, $recordData);
            
            $data = $response->json();
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Registro DNS atualizado com sucesso',
                    'record' => $data['result']
                ];
            } else {
                $errorMessage = 'Erro ao atualizar registro DNS';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro ao atualizar registro DNS no Cloudflare', [
                    'zone_id' => $zoneId,
                    'record_id' => $recordId,
                    'record_data' => $recordData,
                    'dns_record_id' => $record->id,
                    'errors' => $data['errors'] ?? []
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao atualizar registro DNS: ' . $e->getMessage();
            Log::error($errorMessage, [
                'zone_id' => $zoneId,
                'record_id' => $recordId,
                'record_data' => $recordData,
                'dns_record_id' => $record->id
            ]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }
    
    /**
     * Remove um registro DNS
     * 
     * @param string $zoneId
     * @param string $recordId
     * @return array
     */
    public function deleteDnsRecord($zoneId, $recordId)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->delete($this->baseUrl . '/zones/' . $zoneId . '/dns_records/' . $recordId);
            
            $data = $response->json();
            
            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Registro DNS removido com sucesso'
                ];
            } else {
                $errorMessage = 'Erro ao remover registro DNS';
                
                if (isset($data['errors']) && !empty($data['errors'])) {
                    $errors = [];
                    foreach ($data['errors'] as $error) {
                        $errors[] = $error['message'];
                    }
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                Log::error('Erro ao remover registro DNS no Cloudflare', [
                    'zone_id' => $zoneId,
                    'record_id' => $recordId,
                    'errors' => $data['errors'] ?? []
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao remover registro DNS: ' . $e->getMessage();
            Log::error($errorMessage, [
                'zone_id' => $zoneId,
                'record_id' => $recordId
            ]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Sincroniza registros DNS do Cloudflare com o banco de dados local
     * 
     * @param string $zoneId ID da zona/domínio Cloudflare
     * @return array
     */
    public function syncRecordsFromCloudflare($zoneId)
    {
        try {
            Log::info('Iniciando sincronização de registros DNS com Cloudflare', [
                'zone_id' => $zoneId,
                'api_id' => $this->api->id
            ]);
            
            // Buscar registros DNS da zona no Cloudflare
            $cloudflareRecords = $this->getDnsRecords($zoneId);
            
            if (!isset($cloudflareRecords['success']) || $cloudflareRecords['success'] !== true) {
                return [
                    'success' => false,
                    'message' => 'Falha ao buscar registros DNS no Cloudflare: ' . ($cloudflareRecords['message'] ?? 'Erro desconhecido')
                ];
            }
            
            $recordsFromCloudflare = $cloudflareRecords['records'] ?? [];
            $recordsSynced = 0;
            
            // Buscar registros DNS existentes no banco de dados local por nome e tipo
            $existingRecords = \App\Models\DnsRecord::where('external_api_id', $this->api->id)
                ->get()
                ->mapWithKeys(function ($record) {
                    // Usar combinação de tipo e nome como chave única
                    return [$record->record_type . ':' . $record->name => $record];
                });
                
            foreach ($recordsFromCloudflare as $cfRecord) {
                // Criar uma chave única para buscar registros existentes
                $recordKey = $cfRecord['type'] . ':' . $cfRecord['name'];
                $localRecord = $existingRecords->get($recordKey);
                
                // Armazenar dados adicionais do Cloudflare em extra_data
                $extraData = [
                    'cloudflare_id' => $cfRecord['id'],
                    'cloudflare_zone_id' => $zoneId,
                    'proxied' => $cfRecord['proxied'] ?? false,
                    'locked' => $cfRecord['locked'] ?? false,
                    'created_on' => $cfRecord['created_on'] ?? null,
                    'modified_on' => $cfRecord['modified_on'] ?? null
                ];
                
                if (!$localRecord) {
                    // Se o registro não existe localmente, criar
                    $dnsRecord = new \App\Models\DnsRecord([
                        'external_api_id' => $this->api->id,
                        'record_type' => $cfRecord['type'],
                        'name' => $cfRecord['name'],
                        'content' => $cfRecord['content'],
                        'ttl' => $cfRecord['ttl'],
                        'priority' => $cfRecord['priority'] ?? 0,
                        'status' => 'active',
                        'extra_data' => json_encode($extraData)
                    ]);
                    
                    $dnsRecord->save();
                } else {
                    // Se o registro existe, atualizar
                    $localRecord->update([
                        'record_type' => $cfRecord['type'],
                        'name' => $cfRecord['name'],
                        'content' => $cfRecord['content'],
                        'ttl' => $cfRecord['ttl'],
                        'priority' => $cfRecord['priority'] ?? $localRecord->priority,
                        'extra_data' => json_encode($extraData)
                    ]);
                }
                
                $recordsSynced++;
            }
            
            // Atualizar a contagem de registros do domínio
            $domain = \App\Models\CloudflareDomain::where('external_api_id', $this->api->id)
                ->where('zone_id', $zoneId)
                ->first();
                
            if ($domain) {
                $domain->records_count = count($recordsFromCloudflare);
                $domain->save();
            }
            
            return [
                'success' => true,
                'message' => "Sincronizado com sucesso {$recordsSynced} registros DNS",
                'records_synced' => $recordsSynced
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registros DNS do Cloudflare', [
                'zone_id' => $zoneId,
                'api_id' => $this->api->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar registros DNS: ' . $e->getMessage()
            ];
        }
    }
}
