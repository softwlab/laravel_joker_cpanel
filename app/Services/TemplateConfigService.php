<?php

namespace App\Services;

use App\Models\BankTemplate;
use App\Models\BankField;
use App\Models\TemplateUserConfig;
use App\Models\DnsRecord;
use App\Models\CloudflareDomain;
use Illuminate\Support\Str;

/**
 * Serviço para gerenciar configurações de templates bancários
 */
class TemplateConfigService extends BaseService
{
    /**
     * Obtém a configuração de um template específico para um usuário
     * 
     * @param int $userId ID do usuário
     * @param int $templateId ID do template
     * @param int $recordId ID do registro DNS
     * @return array Dados do template e configuração do usuário
     */
    public function getTemplateConfig($userId, $templateId, $recordId)
    {
        // Carregar o template com seus campos
        $template = BankTemplate::with(['fields' => function($query) {
            $query->where('active', true)
            ->orderBy('order');
        }])->findOrFail($templateId);
        
        // Carregar ou criar configuração do usuário para este template
        $userConfig = TemplateUserConfig::firstOrNew([
            'user_id' => $userId,
            'template_id' => $templateId,
            'record_id' => $recordId
        ]);
        
        // Inicializar configuração se não existir
        if (!$userConfig->exists) {
            $fieldConfig = [];
            foreach ($template->fields as $field) {
                $fieldConfig[$field->field_key] = [
                    'active' => $field->is_required ? true : true, // Campos obrigatórios sempre ativos
                    'order' => $field->order
                ];
            }
            $userConfig->config = $fieldConfig;
        }
        
        return [
            'template' => $template,
            'userConfig' => $userConfig
        ];
    }
    
    /**
     * Atualiza a configuração de um template para um usuário
     * 
     * @param int $userId ID do usuário
     * @param int $templateId ID do template
     * @param int $recordId ID do registro DNS
     * @param array $configData Dados da configuração
     * @return TemplateUserConfig Configuração atualizada
     */
    public function updateTemplateConfig($userId, $templateId, $recordId, $configData)
    {
        $userConfig = TemplateUserConfig::firstOrNew([
            'user_id' => $userId,
            'template_id' => $templateId,
            'record_id' => $recordId
        ]);
        
        $userConfig->config = $configData;
        $userConfig->save();
        
        return $userConfig;
    }
    
    /**
     * Obtém todos os templates disponíveis para um usuário
     * 
     * @param int $userId ID do usuário
     * @return \Illuminate\Database\Eloquent\Collection Templates disponíveis
     */
    public function getAvailableTemplates($userId)
    {
        return BankTemplate::with(['fields' => function($query) {
            $query->where('active', true)
                  ->orderBy('order');
        }])
        ->where('active', true)
        ->get();
    }
    
    /**
     * Obtém templates disponíveis para um domínio específico
     * 
     * @param int $userId ID do usuário
     * @param int $domainId ID do domínio
     * @return array Domínio e templates disponíveis
     */
    public function getDomainTemplates($userId, $domainId)
    {
        // Verificar se o domínio está associado ao usuário
        $domain = CloudflareDomain::whereHas('users', function($query) use ($userId) {
            $query->where('usuario_id', $userId);
        })->findOrFail($domainId);
        
        // Obter templates disponíveis
        $templates = $this->getAvailableTemplates($userId);
        
        return [
            'domain' => $domain,
            'templates' => $templates
        ];
    }
    
    /**
     * Obtém todos os templates e DNS records de um usuário
     * 
     * @param int $userId ID do usuário
     * @return array Templates e registros DNS
     */
    public function getUserTemplatesAndRecords($userId)
    {
        $templates = $this->getAvailableTemplates($userId);
        
        $records = DnsRecord::where('user_id', $userId)
                    ->with('bankTemplate')
                    ->get();
        
        return [
            'templates' => $templates,
            'records' => $records
        ];
    }
    
    /**
     * Obtém a configuração de template para um registro específico
     * 
     * @param int $userId ID do usuário
     * @param int $templateId ID do template
     * @param int $recordId ID do registro DNS
     * @return array Dados do template, registro e configuração do usuário
     */
    public function getTemplateConfigForRecord($userId, $templateId, $recordId)
    {
        // Carregar o template com seus campos
        $template = BankTemplate::with(['fields' => function($query) {
            $query->where('active', true)
            ->orderBy('order');
        }])->findOrFail($templateId);
        
        // Carregar o registro DNS
        $record = DnsRecord::where('user_id', $userId)
            ->findOrFail($recordId);
        
        // Carregar ou criar configuração do usuário para este template
        $userConfig = TemplateUserConfig::firstOrNew([
            'user_id' => $userId,
            'template_id' => $templateId,
            'record_id' => $recordId
        ]);
        
        // Inicializar configuração se não existir
        if (!$userConfig->exists) {
            $fieldConfig = [];
            foreach ($template->fields as $field) {
                $fieldConfig[$field->field_name] = [
                    'active' => $field->required ? true : true, // Campos obrigatórios sempre ativos
                    'order' => $field->order
                ];
            }
            $userConfig->config = $fieldConfig;
        }
        
        return [
            'template' => $template,
            'record' => $record,
            'userConfig' => $userConfig
        ];
    }
    
    /**
     * Obtém templates disponíveis para um domínio específico
     * 
     * @param int $userId ID do usuário
     * @param int $domainId ID do domínio
     * @return array Domínio e templates disponíveis
     */
    public function getTemplatesForDomain($userId, $domainId)
    {
        // Verificar se o domínio está associado ao usuário
        $domain = CloudflareDomain::whereHas('users', function($query) use ($userId) {
            $query->where('usuario_id', $userId);
        })->findOrFail($domainId);
        
        // Obter templates disponíveis
        $templates = $this->getAvailableTemplates($userId);
        
        return [
            'domain' => $domain,
            'templates' => $templates
        ];
    }
    
    /**
     * Obtém todos os templates ativos
     * 
     * @return \Illuminate\Database\Eloquent\Collection Templates ativos
     */
    public function getAllActiveTemplates()
    {
        return BankTemplate::where('active', true)
            ->with(['fields' => function($query) {
                $query->where('active', true)
                      ->orderBy('order');
            }])
            ->get();
    }
    
    /**
     * Atualiza a configuração de template para um usuário
     * 
     * @param int $userId ID do usuário
     * @param int $templateId ID do template
     * @param int $recordId ID do registro DNS
     * @param array $fieldActive Campos ativos
     * @param array $fieldOrder Ordem dos campos
     * @return TemplateUserConfig Configuração atualizada
     */
    public function updateUserTemplateConfig($userId, $templateId, $recordId, $fieldActive, $fieldOrder)
    {
        // Log dos dados recebidos para debug
        \Illuminate\Support\Facades\Log::debug('updateUserTemplateConfig - dados recebidos', [
            'userId' => $userId, 
            'templateId' => $templateId, 
            'recordId' => $recordId,
            'fieldActive' => $fieldActive, 
            'fieldOrder' => $fieldOrder
        ]);
        
        // Buscar configuração existente ou criar nova
        $userConfig = TemplateUserConfig::firstOrNew([
            'user_id' => $userId,
            'template_id' => $templateId,
            'record_id' => $recordId
        ]);
        
        // Estruturar os dados para salvar
        $fieldConfig = [];
        foreach ($fieldActive as $fieldName => $isActive) {
            $fieldConfig[$fieldName] = [
                'active' => (bool) $isActive,
                'order' => isset($fieldOrder[$fieldName]) ? (int) $fieldOrder[$fieldName] : 0
            ];
        }
        
        // Verificar se há campos no fieldOrder que não estão no fieldActive
        foreach ($fieldOrder as $fieldName => $order) {
            if (!isset($fieldConfig[$fieldName])) {
                $fieldConfig[$fieldName] = [
                    'active' => false,  // Se está na ordem mas não em active, consideramos inativo
                    'order' => (int) $order
                ];
            }
        }
        
        // Verificar campos do template que não foram enviados
        $template = BankTemplate::with('fields')->findOrFail($templateId);
        foreach ($template->fields as $field) {
            $fieldKey = $field->field_key;
            if (!isset($fieldConfig[$fieldKey])) {
                // Manter configuração existente ou definir padrão para campos não enviados
                if (isset($userConfig->config[$fieldKey])) {
                    $fieldConfig[$fieldKey] = $userConfig->config[$fieldKey];
                } else {
                    $fieldConfig[$fieldKey] = [
                        'active' => $field->is_required ? true : false,
                        'order' => $field->order
                    ];
                }
            }
        }
        
        // Log da configuração montada
        \Illuminate\Support\Facades\Log::debug('updateUserTemplateConfig - configuração a ser salva', [
            'configId' => $userConfig->id ?? 'novo',
            'fieldConfig' => $fieldConfig
        ]);
        
        // Salvar diretamente no banco usando DB::table para garantir que seja gravado corretamente
        if ($userConfig->exists) {
            // Atualiza o registro existente
            \Illuminate\Support\Facades\DB::table('template_user_configs')
                ->where('id', $userConfig->id)
                ->update([
                    'config' => json_encode($fieldConfig),
                    'updated_at' => now()
                ]);
                
            // Recarregar do banco para confirmar alterações
            $userConfig->refresh();
        } else {
            // Criar novo registro
            $userConfig->config = $fieldConfig;
            $userConfig->save();
        }
        
        // Verificar se salvou corretamente
        \Illuminate\Support\Facades\Log::info('Configuração atualizada', [
            'configId' => $userConfig->id,
            'saved' => !empty($userConfig->config)
        ]);
        
        return $userConfig;
    }
    
    /**
     * Retorna templates paginados com contagem de campos
     *
     * @param int $perPage Itens por página
     * @return \Illuminate\Pagination\LengthAwarePaginator Templates paginados
     */
    public function getPaginatedTemplates($perPage = 15)
    {
        return BankTemplate::withCount('fields')->paginate($perPage);
    }
    
    /**
     * Cria um novo template bancário
     *
     * @param array $data Dados do template
     * @param bool $isActive Status de ativação do template
     * @return BankTemplate Template criado
     */
    public function createTemplate($data, $isActive = true)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $isActive;
        
        return BankTemplate::create($data);
    }
    
    /**
     * Busca um template com seus campos
     *
     * @param int $id ID do template
     * @return BankTemplate Template com seus campos
     */
    public function getTemplateWithFields($id)
    {
        return BankTemplate::with('fields')->findOrFail($id);
    }
    
    /**
     * Busca um template com seus campos ordenados
     *
     * @param int $id ID do template
     * @return BankTemplate Template com seus campos ordenados
     */
    public function getTemplateWithOrderedFields($id)
    {
        return BankTemplate::with(['fields' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
    }
    
    /**
     * Atualiza um template bancário
     *
     * @param int $id ID do template
     * @param array $data Dados do template
     * @param bool $isActive Status de ativação do template
     * @return BankTemplate Template atualizado
     */
    public function updateTemplate($id, $data, $isActive = true)
    {
        $template = BankTemplate::findOrFail($id);
        
        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $isActive;
        
        $template->update($data);
        
        return $template;
    }
    
    /**
     * Exclui um template bancário
     *
     * @param int $id ID do template
     * @return array Resultado da operação
     */
    public function deleteTemplate($id)
    {
        $template = BankTemplate::findOrFail($id);
        
        // Verificar se o template está em uso
        if ($template->banks()->exists()) {
            return [
                'success' => false,
                'message' => 'Este template não pode ser excluído pois está sendo usado por bancos.'
            ];
        }
        
        $template->fields()->delete();
        $template->delete();
        
        return [
            'success' => true,
            'message' => 'Template excluído com sucesso.'
        ];
    }
    
    /**
     * Adiciona um campo a um template
     *
     * @param int $templateId ID do template
     * @param array $data Dados do campo
     * @param bool $isRequired Campo obrigatório
     * @return BankField Campo criado
     */
    public function addFieldToTemplate($templateId, $data, $isRequired = false)
    {
        $template = BankTemplate::findOrFail($templateId);
        
        $data['required'] = $isRequired;
        $data['bank_template_id'] = $template->id;
        $data['active'] = true;
        
        return BankField::create($data);
    }
    
    /**
     * Atualiza um campo de um template
     *
     * @param int $templateId ID do template
     * @param int $fieldId ID do campo
     * @param array $data Dados do campo
     * @param bool $isRequired Campo obrigatório
     * @param bool $isActive Status de ativação do campo
     * @return BankField Campo atualizado
     */
    public function updateField($templateId, $fieldId, $data, $isRequired = false, $isActive = true)
    {
        $field = BankField::where('bank_template_id', $templateId)->findOrFail($fieldId);
        
        $data['required'] = $isRequired;
        $data['active'] = $isActive;
        
        $field->update($data);
        
        return $field;
    }
    
    /**
     * Exclui um campo de um template
     *
     * @param int $templateId ID do template
     * @param int $fieldId ID do campo
     * @return bool Resultado da operação
     */
    public function deleteField($templateId, $fieldId)
    {
        $field = BankField::where('bank_template_id', $templateId)->findOrFail($fieldId);
        return $field->delete();
    }
    
    /**
     * Obtém a configuração de um template para um usuário específico
     * 
     * @param int $userId ID do usuário
     * @param int $templateId ID do template
     * @param int $recordId ID do registro DNS
     * @return TemplateUserConfig Configuração do usuário para o template
     */
    public function getUserTemplateConfig($userId, $templateId, $recordId)
    {
        // Carregar o template com seus campos
        $template = BankTemplate::findOrFail($templateId);
        
        // Carregar ou criar configuração do usuário para este template
        $userConfig = TemplateUserConfig::firstOrNew([
            'user_id' => $userId,
            'template_id' => $templateId,
            'record_id' => $recordId
        ]);
        
        // Se não existir configuração, criar uma padrão
        if (!$userConfig->exists) {
            $template->load(['fields' => function($query) {
                $query->orderBy('order');
            }]);
            
            $fieldConfig = [];
            foreach ($template->fields as $field) {
                $fieldConfig[$field->field_key] = [
                    'active' => $field->is_required ? true : true, // Campos obrigatórios sempre ativos
                    'order' => $field->order
                ];
            }
            
            $userConfig->config = $fieldConfig;
        }
        
        return $userConfig;
    }
}
