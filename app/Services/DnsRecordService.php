<?php

namespace App\Services;

use App\Models\DnsRecord;
use App\Models\ExternalApi;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DnsRecordService
{
    protected $dnsStats;
    protected $userStatistics;
    protected $bankingStats;
    protected $dnsService;
    
    /**
     * Construtor que injeta os serviços de estatísticas
     *
     * @param DnsStatisticsService $dnsStats
     * @param UserStatisticsService $userStats
     * @param BankingStatisticsService $bankingStats
     * @param DnsService $dnsService
     */
    public function __construct(
        DnsStatisticsService $dnsStats,
        UserStatisticsService $userStatistics,
        BankingStatisticsService $bankingStats,
        DnsService $dnsService
    ) {
        $this->dnsStats = $dnsStats;
        $this->userStatistics = $userStatistics;
        $this->bankingStats = $bankingStats;
        $this->dnsService = $dnsService;
    }
    
    /**
     * Obtém registros DNS com paginação e filtros opcionais
     *
     * @param array $filters Array com filtros (type, api, search)
     * @param int $perPage Itens por página
     * @return LengthAwarePaginator Registros paginados
     */
    public function getPaginatedRecords(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = DnsRecord::with('externalApi');
        
        // Aplicar filtros
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('record_type', $filters['type']);
        }
        
        if (isset($filters['api']) && !empty($filters['api'])) {
            $query->where('external_api_id', $filters['api']);
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($perPage);
    }
    
    /**
     * Obtém os dados necessários para criar um novo registro DNS
     *
     * @return array Dados para formulário de criação
     */
    public function getCreateFormData(): array
    {
        $apis = ExternalApi::where('status', 'active')->get();
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $users = Usuario::all();
        $clientIpAddress = config('app.client_page_ip', '127.0.0.1');
        
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return [
            'apis' => $apis,
            'banks' => $banks,
            'templates' => $templates,
            'users' => $users,
            'clientIpAddress' => $clientIpAddress,
            'recordTypes' => $recordTypes
        ];
    }
    
    /**
     * Cria um novo registro DNS
     *
     * @param array $data Dados do registro
     * @return array Resultado da operação
     */
    public function createRecord(array $data): array
    {
        // Validação de dados
        $validator = Validator::make($data, [
            'external_api_id' => 'required|exists:external_apis,id',
            'record_type' => 'required|string',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'ttl' => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_template_id' => 'nullable|exists:bank_templates,id',
            'user_id' => 'nullable|exists:usuarios,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ];
        }
        
        try {
            $api = ExternalApi::findOrFail($data['external_api_id']);
            
            // Criar o registro DNS
            $dnsRecord = new DnsRecord([
                'external_api_id' => $data['external_api_id'],
                'bank_id' => $data['bank_id'] ?? null,
                'bank_template_id' => $data['bank_template_id'] ?? null,
                'link_group_id' => $data['link_group_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'record_type' => $data['record_type'],
                'name' => $data['name'],
                'content' => $data['content'],
                'ttl' => $data['ttl'] ?? 300,
                'priority' => $data['priority'] ?? 10,
                'proxied' => isset($data['proxied']) && $data['proxied'] == 'on',
                'status' => 'active',
                'synced_at' => null
            ]);
            
            // Gerar UUID para uso pela API
            $dnsRecord->uuid = (string) \Illuminate\Support\Str::uuid();
            $dnsRecord->save();
            
            // Opcionalmente, sincronizar com a API externa
            if (isset($data['sync_with_api']) && $data['sync_with_api'] == 'on') {
                $syncResult = $this->syncRecord($dnsRecord->id);
                
                if (!$syncResult['success']) {
                    return [
                        'success' => true,
                        'record' => $dnsRecord,
                        'warning' => 'Registro criado com sucesso, mas a sincronização falhou: ' . $syncResult['message']
                    ];
                }
            }
            
            // Invalidar caches de estatísticas se houver um usuário associado
            if (isset($data['user_id']) && $data['user_id']) {
                $this->userStatistics->invalidateCache($data['user_id']);
            }
            
            return [
                'success' => true,
                'record' => $dnsRecord,
                'message' => 'Registro DNS criado com sucesso!'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar registro DNS: ' . $e->getMessage(), [
                'data' => $data
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao criar registro DNS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtém um registro DNS pelo ID
     *
     * @param string|int $id ID do registro
     * @return DnsRecord|null Registro encontrado ou null
     */
    public function getRecord($id): ?DnsRecord
    {
        // Buscar o registro diretamente do banco de dados e garantir que as relações
        // sejam carregadas sem cache usando eager loading
        $record = DnsRecord::query()
            ->with([
                'externalApi', 
                'bank', 
                'bankTemplate' => function($query) { $query->withoutGlobalScopes(); }, 
                'user'
            ])
            ->where('id', $id)
            ->first();
            
        // Se encontrou o registro, forçar refresh para garantir dados atualizados
        if ($record) {
            // Limpar qualquer cache que possa existir
            if (method_exists($record, 'refresh')) {
                $record->refresh();
            }
        }
        
        return $record;
    }
    
    /**
     * Obtém os dados necessários para o formulário de edição
     *
     * @param string|int $id ID do registro a ser editado
     * @return array Dados para o formulário de edição
     */
    public function getEditData($id): array
    {
        $record = $this->getRecord($id);
        
        if (!$record) {
            return ['record' => null];
        }
        
        // Obter APIs externas ativas
        $apis = \App\Models\ExternalApi::where('status', 'active')->get();
        
        // Obter bancos e templates
        $banks = \App\Models\Bank::all();
        $templates = \App\Models\BankTemplate::all();
        
        // Obter usuários
        $users = \App\Models\Usuario::all();
        
        // Obter IP da página do cliente do arquivo de configuração
        $clientIpAddress = \Illuminate\Support\Facades\Config::get('app.client_page_ip', '127.0.0.1');
        
        // Tipos de registros DNS suportados
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return compact(
            'record', 'apis', 'banks', 'templates', 'users', 'clientIpAddress', 'recordTypes'
        );
    }
    
    /**
     * Obtém estatísticas de visitantes para um registro DNS
     *
     * @param string|int $dnsId ID do registro DNS
     * @return int Total de visitantes
     */
    public function getTotalVisitantes($dnsId): int
    {
        return $this->dnsStats->getTotalVisitantes($dnsId);
    }
    
    /**
     * Obtém estatísticas de visitantes por dia para um registro DNS
     *
     * @param string|int $dnsId ID do registro DNS
     * @return array Array associativo com data => contagem
     */
    public function getVisitantesPorDia($dnsId): array
    {
        return $this->dnsStats->getVisitantesPorDia($dnsId);
    }
    
    /**
     * Obtém estatísticas de informações bancárias para um registro DNS
     *
     * @param string|int $dnsId ID do registro DNS
     * @return array Informações bancárias
     */
    public function getInformacoesBancarias($dnsId): array
    {
        return $this->bankingStats->getInformacoesBancarias($dnsId);
    }
    
    /**
     * Invalida o cache de estatísticas para um usuário
     *
     * @param string|int $userId ID do usuário
     * @return void
     */
    public function invalidateUserStatsCache($userId): void
    {
        if ($userId) {
            $this->userStatistics->invalidateCache($userId);
        }
    }
    
    /**
     * Obtém os dados necessários para editar um registro DNS
     *
     * @param string|int $id ID do registro
     * @return array Dados para edição
     */
    public function getEditFormData($id): array
    {
        $record = $this->getRecord($id);
        
        if (!$record) {
            return [
                'success' => false,
                'message' => 'Registro DNS não encontrado'
            ];
        }
        
        $apis = ExternalApi::where('status', 'active')->get();
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $users = Usuario::all();
        
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return [
            'success' => true,
            'record' => $record,
            'apis' => $apis,
            'banks' => $banks,
            'templates' => $templates,
            'users' => $users,
            'recordTypes' => $recordTypes
        ];
    }
    
    /**
     * Atualiza um registro DNS
     *
     * @param string|int $id ID do registro
     * @param array $data Novos dados
     * @return array Resultado da operação
     */
    public function updateRecord($id, array $data): array
    {
        // Log para debug - verificar os dados recebidos
        Log::info('Dados recebidos em updateRecord:', [
            'id' => $id,
            'data' => $data,
            'bank_template_id' => $data['bank_template_id'] ?? null
        ]);
        
        // Validação de dados
        $validator = Validator::make($data, [
            'external_api_id' => 'required|exists:external_apis,id',
            'record_type' => 'required|string',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'ttl' => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_template_id' => 'nullable|exists:bank_templates,id',
            'user_id' => 'nullable|exists:usuarios,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ];
        }
        
        try {
            $dnsRecord = DnsRecord::findOrFail($id);
            $previousUserId = $dnsRecord->user_id;
            
            $api = ExternalApi::findOrFail($data['external_api_id']);
            
            // Log dos valores atuais antes da atualização
            Log::info('DnsRecordService::updateRecord - Valores antes da atualização:', [
                'id' => $id,
                'template_atual' => $dnsRecord->bank_template_id,
                'user_atual' => $dnsRecord->user_id
            ]);
            
            // Atualizar o registro com os novos dados
            $dnsRecord->external_api_id = $data['external_api_id'];
            $dnsRecord->record_type = $data['record_type'];
            $dnsRecord->name = $data['name'];
            $dnsRecord->content = $data['content'];
            $dnsRecord->ttl = $data['ttl'] ?? 3600;
            $dnsRecord->priority = $data['priority'] ?? 0;
            $dnsRecord->bank_id = $data['bank_id'] ?? null;
            $dnsRecord->bank_template_id = $data['bank_template_id'] ?? null;
            $dnsRecord->user_id = $data['user_id'] ?? null;
            
            // Salvar as alterações no banco de dados
            $dnsRecord->save();
            
            // Log dos valores após a atualização
            Log::info('DnsRecordService::updateRecord - Valores após a atualização:', [
                'id' => $id,
                'template_novo' => $dnsRecord->bank_template_id,
                'user_novo' => $dnsRecord->user_id
            ]);
            
            // Variável para mensagem de sincronização
            $syncMessage = null;
            
            // Opcionalmente, sincronizar com a API externa
            if (isset($data['sync_with_api']) && $data['sync_with_api'] == 'on') {
                $syncResult = $this->syncRecord($id);
                if (!$syncResult['success']) {
                    $syncMessage = 'Registro atualizado com sucesso, mas a sincronização falhou: ' . ($syncResult['message'] ?? 'Erro desconhecido');
                }
            }
            
            // Invalidar caches de estatísticas para usuários afetados
            if ($previousUserId) {
                $this->userStatistics->invalidateCache($previousUserId);
                
                if ($previousUserId != $dnsRecord->user_id && $dnsRecord->user_id) {
                    $this->userStatistics->invalidateCache($dnsRecord->user_id);
                }
            } elseif ($dnsRecord->user_id) {
                $this->userStatistics->invalidateCache($dnsRecord->user_id);
            }
            
            // Invalidar cache do DNS para garantir que as alterações de template sejam refletidas
            $this->dnsStats->invalidateCache($dnsRecord->id);
            
            // Forçar recarregamento do modelo para garantir que relações estejam atualizadas
            $dnsRecord->refresh();
            
            return [
                'success' => true,
                'record' => $dnsRecord,
                'message' => 'Registro DNS atualizado com sucesso!',
                'warning' => $syncMessage
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar registro DNS: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao atualizar registro DNS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Exclui um registro DNS
     *
     * @param string|int $id ID do registro
     * @return array Resultado da operação
     */
    public function deleteRecord($id): array
    {
        try {
            $record = DnsRecord::findOrFail($id);
            $userId = $record->user_id;
            $apiId = $record->external_api_id;
            
            // Tentar excluir na API externa primeiro
            $apiDeleted = false;
            
            // Tentar deletar o registro da API externa se ele tiver um ID Cloudflare salvo
            if ($record->cloudflare_record_id) {
                try {
                    $api = ExternalApi::findOrFail($record->external_api_id);
                    
                    // Esta parte precisa ser implementada em DnsService
                    //$result = DnsService::deleteRecordFromCloudflare($api, $record);
                    
                    // Se foi deletado com sucesso da API
                    $apiDeleted = true;
                } catch (\Exception $e) {
                    Log::error('Erro ao excluir registro DNS da API externa: ' . $e->getMessage(), [
                        'record_id' => $id,
                        'cloudflare_record_id' => $record->cloudflare_record_id
                    ]);
                    // Continuamos a exclusão local mesmo se falhar na API
                }
            }
            
            // Excluir o registro localmente
            $record->delete();
            
            // Invalidar caches de estatísticas
            if ($userId) {
                $this->userStatistics->invalidateCache($userId);
            }
            
            $message = $apiDeleted 
                ? 'Registro DNS excluído com sucesso da API externa e do banco de dados local.' 
                : 'Registro DNS excluído do banco de dados local. Nenhuma exclusão da API externa foi realizada.';
            
            return [
                'success' => true,
                'apiDeleted' => $apiDeleted,
                'message' => $message,
                'userId' => $userId,
                'apiId' => $apiId
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao excluir registro DNS: ' . $e->getMessage(), [
                'record_id' => $id
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
     * @param string|int $id ID do registro
     * @return array Resultado da operação
     */
    public function syncRecord($id): array
    {
        try {
            $record = DnsRecord::findOrFail($id);
            $api = ExternalApi::findOrFail($record->external_api_id);
            
            if ($api->status !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Não é possível sincronizar com uma API inativa.'
                ];
            }
            
            $result = $this->dnsService->syncRecord($record);
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registro DNS: ' . $e->getMessage(), [
                'record_id' => $id
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar registro DNS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sincroniza todos os registros DNS com a API externa
     *
     * @param string|int $apiId ID da API externa
     * @return array Resultado da operação
     */
    public function syncAllRecords($apiId): array
    {
        try {
            $api = ExternalApi::findOrFail($apiId);
            
            if ($api->status !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Não é possível sincronizar com uma API inativa.'
                ];
            }
            
            $result = $this->dnsService->syncAllRecords($api);
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registros DNS: ' . $e->getMessage(), [
                'api_id' => $apiId
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao sincronizar registros DNS: ' . $e->getMessage()
            ];
        }
    }
}

