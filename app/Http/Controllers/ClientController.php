<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\DnsRecord;
use App\Models\TemplateUserConfig;
use App\Models\UserConfig;
use App\Models\Usuario;
use App\Services\TemplateConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    protected $templateService;
    
    /**
     * Construtor do controller
     * 
     * @param TemplateConfigService $templateService
     */
    public function __construct(TemplateConfigService $templateService)
    {
        $this->templateService = $templateService;
    }
    /**
     * Exibe a página de configuração de templates para o cliente
     */
    public function configTemplates(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Verificar se foi fornecido um template_id e record_id específico
        if ($request->has('template_id') && $request->has('record_id')) {
            $templateId = $request->input('template_id');
            $recordId = $request->input('record_id');
            $isPrimary = $request->input('is_primary', true);
            
            $record = DnsRecord::where('user_id', $userId)->findOrFail($recordId);
            
            // Carregar os templates secundários com os dados do pivot
            $secondaryTemplates = DB::table('dns_record_templates')
                ->where('dns_record_id', $recordId)
                ->join('bank_templates', 'dns_record_templates.bank_template_id', '=', 'bank_templates.id')
                ->select(
                    'bank_templates.*', 
                    'dns_record_templates.path_segment',
                    'dns_record_templates.is_primary'
                )
                ->get();
                
            // Adicionar propriedade pivot para compatibilidade com a view
            foreach ($secondaryTemplates as $secTemplate) {
                $secTemplate->pivot = (object)[
                    'path_segment' => $secTemplate->path_segment,
                    'is_primary' => $secTemplate->is_primary
                ];
            }
            
            // Verificar registro e template principal ou secundário
            if ($isPrimary === 'true' || $isPrimary === true) {
                // É o template principal
                $template = \App\Models\BankTemplate::findOrFail($templateId);
                
                if ($record->bank_template_id != $template->id) {
                    return redirect()->route('cliente.dashboard')->with('error', 'Template não associado como template principal.');
                }
            } else {
                // É um template secundário
                // Verificar se o template está na tabela de relacionamentos
                $templateRelation = \DB::table('dns_record_templates')
                    ->where('dns_record_id', $recordId)
                    ->where('bank_template_id', $templateId)
                    ->first();
                
                if (!$templateRelation) {
                    return redirect()->route('cliente.dashboard')
                        ->with('error', 'Template secundário não associado a este registro DNS.');
                }
                
                $template = \App\Models\BankTemplate::findOrFail($templateId);
            }
            
            $userConfig = $this->templateService->getUserTemplateConfig($userId, $templateId, $recordId);
            
            return view('cliente.template-config', compact('template', 'record', 'userConfig', 'isPrimary', 'secondaryTemplates'));
        }
        
        // Verificar se foi fornecido apenas um domain_id
        elseif ($request->has('domain_id')) {
            $domainId = $request->input('domain_id');
            
            // Usar o serviço para buscar domínio e templates disponíveis
            $result = $this->templateService->getTemplatesForDomain($userId, $domainId);
            
            $domain = $result['domain'];
            $templates = $result['templates'];
            
            return view('cliente.domain-templates', compact('domain', 'templates'));
        }
        
        // Caso não tenha parâmetros, mostrar todos os templates configuráveis
        else {
            $templates = $this->templateService->getAllActiveTemplates();
            
            return view('cliente.all-templates', compact('templates'));
        }
    }
    
    /**
     * Atualiza a configuração de um template para o cliente
     */
    public function updateTemplateConfig(Request $request, $templateId)
    {
        $recordId = $request->input('record_id');
        $isPrimary = $request->input('is_primary', 'true');
        
        // Obter arrays de campos ativos e ordem enviados pelo formulário
        $fieldActiveInput = $request->input('field_active', []);
        $fieldOrderInput = $request->input('field_order', []);
        
        $request->validate([
            'record_id' => 'required|exists:dns_records,id',
            'is_primary' => 'required|string',
        ]);

        // Verificar se o registro pertence ao usuário
        $user = Auth::user();
        $userId = $user->id;
        $record = DnsRecord::where('user_id', $userId)->findOrFail($recordId);

        // Verificar se o template está associado ao registro DNS
        $isTemplateAssociated = false;
        
        // Debug para verificar valores recebidos
        Log::debug('Verificando associação de template', [
            'isPrimary' => $isPrimary,
            'templateId' => $templateId,
            'recordId' => $recordId,
            'bank_template_id' => $record->bank_template_id,
            'tipo_isPrimary' => gettype($isPrimary)
        ]);
        
        // NOVA LÓGICA: Considerar válida a associação se:
        // 1. O template estiver na tabela de associações dns_record_templates
        // 2. O template for o template principal (bank_template_id)
        // 3. Já existir uma configuração salva para este usuário+template+record (mesmo sem associação direta)
        
        // Verificar se é template principal pelo campo bank_template_id
        if ($record->bank_template_id == $templateId) {
            Log::info('Template encontrado como template principal direto (bank_template_id)', [
                'template_id' => $templateId,
                'record_id' => $recordId
            ]);
            $isTemplateAssociated = true;
            $isPrimary = 'true';
        } else {
            // Verificar se está na tabela de associações
            $templateRelation = DB::table('dns_record_templates')
                ->where('dns_record_id', $recordId)
                ->where('bank_template_id', $templateId)
                ->first();
                
            if ($templateRelation) {
                Log::info('Template encontrado na tabela de associações dns_record_templates', [
                    'template_id' => $templateId,
                    'record_id' => $recordId,
                    'is_primary' => $templateRelation->is_primary
                ]);
                $isTemplateAssociated = true;
                $isPrimary = $templateRelation->is_primary ? 'true' : 'false';
            } else {
                // Verificar se já existe uma configuração para este template (permitir editar configurações existentes)
                $existingConfig = \App\Models\TemplateUserConfig::where([
                    'user_id' => $userId,
                    'template_id' => $templateId,
                    'record_id' => $recordId
                ])->first();
                
                if ($existingConfig) {
                    Log::info('Encontrada configuração salva anteriormente para o template', [
                        'template_id' => $templateId,
                        'record_id' => $recordId,
                        'config_id' => $existingConfig->id
                    ]);
                    $isTemplateAssociated = true;
                    // Manter o isPrimary enviado pelo formulário
                }
            }
        }
        
        if (!$isTemplateAssociated) {
            Log::error('Template não associado ao registro DNS', [
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId,
                'is_primary' => $isPrimary
            ]);
            return redirect()->route('cliente.dashboard')->with('error', 'Template não está associado a este registro DNS.');
        }
        
        try {
            // Preparar arrays para o serviço
            $fieldActive = [];
            $fieldOrder = [];
            
            // Converter os dados do formulário para o formato esperado pelo serviço
            foreach ($fieldActiveInput as $fieldKey => $isActive) {
                $fieldActive[$fieldKey] = (bool)$isActive;
            }
            
            foreach ($fieldOrderInput as $fieldKey => $order) {
                $fieldOrder[$fieldKey] = (int)$order;
            }
            
            // Log de debug antes de salvar
            Log::debug('Salvando configuração do template', [
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId,
                'field_active' => $fieldActive,
                'field_order' => $fieldOrder,
                'count_active' => count($fieldActive),
                'count_order' => count($fieldOrder)
            ]);
            
            $this->templateService->updateUserTemplateConfig(
                $userId,
                $templateId,
                $recordId,
                $fieldActive,
                $fieldOrder
            );
            
            // Log após o salvamento bem-sucedido
            Log::info('Configuração do template atualizada com sucesso', [
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId
            ]);
            
            return redirect()->back()->with('success', 'Configuração do template atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configuração do template: ' . $e->getMessage(), [
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId,
                'exception' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Erro ao atualizar configuração do template: ' . $e->getMessage());
        }
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Buscar os dados relacionados ao domínio
        $dnsRecords = \App\Models\DnsRecord::where('user_id', $userId)
            ->with(['externalApi', 'bankTemplate'])
            ->get();
        
        // Buscar bancos do usuário
        $banks = Bank::where('usuario_id', $userId)->get();
        
        return view('cliente.dashboard', compact('banks', 'user', 'dnsRecords'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('cliente.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,'.$user->id,
        ]);

        // Usar DB::table diretamente para atualizar
        DB::table('usuarios')
            ->where('id', $user->id)
            ->update([
                'nome' => $validated['nome'],
                'email' => $validated['email']
            ]);

        return redirect()->route('cliente.profile')
            ->with('success', 'Perfil atualizado com sucesso');
    }

    public function banks(Request $request)
    {
        $user = Auth::user();
        
        // Verificar se há parâmetros template_id e record_id
        // Se existirem, redirecionar para a página de configuração de template
        if ($request->has('template_id') && $request->has('record_id')) {
            // Usar o nome correto da rota com o prefixo cliente.
            return redirect()->to('/cliente/templates/config?template_id=' . $request->input('template_id') . '&record_id=' . $request->input('record_id'));
        }
        
        // Carregar todos os templates bancários disponíveis
        $templates = BankTemplate::where('active', true)
                ->withCount('banks')
                ->orderBy('name')
                ->get();
        
        return view('cliente.banks', compact('templates'));
    }
    
    public function createBank()
    {
        $templates = BankTemplate::where('active', true)
                    ->with(['fields' => function($query) {
                        $query->where('active', true)->orderBy('order');
                    }])
                    ->get();
                    
        return view('cliente.create-bank', compact('templates'));
    }
    
    public function storeBank(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'bank_template_id' => 'required|exists:bank_templates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'field_values' => 'required|array',
            'links' => 'required|array',
            'links.atual' => 'required|string',
            'links.redir' => 'nullable|array',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = true;
        $validated['usuario_id'] = $user->id;
        
        // Criar o banco
        $bank = Bank::create($validated);
        
        return redirect()->route('cliente.banks')
            ->with('success', 'Banco criado com sucesso');
    }

    public function showBank($id)
    {
        $user = Auth::user();
        
        // Buscar o banco com template e externalApi, se existirem
        $bank = Bank::where('id', $id)
            ->where('usuario_id', $user->id)
            ->with(['template', 'template.fields' => function($query) {
                $query->where('active', true)->orderBy('order', 'asc');
            }, 'externalApi'])
            ->firstOrFail();
        
        return view('cliente.bank-details', compact('bank'));
    }

    public function updateBank(Request $request, $id)
    {
        $user = Auth::user();
        $bank = Bank::where('usuario_id', $user->id)->findOrFail($id);
        
        // Verificar se é uma atualização para associar um template a um link existente
        if ($request->has('update_template') && $request->filled('bank_template_id')) {
            // Validar apenas o ID do template
            $validated = $request->validate([
                'bank_template_id' => 'required|exists:bank_templates,id',
            ]);
            
            // Obter o template para criar os valores de campo
            $template = BankTemplate::with(['fields' => function($query) {
                $query->where('active', true);
            }])->findOrFail($validated['bank_template_id']);
            
            // Inicializar os valores de campo com valores vazios
            $fieldValues = [];
            foreach ($template->fields as $field) {
                $fieldValues[$field->field_name] = '';
            }
            
            // Atualizar o link com o novo template e estrutura de campos
            $bank->update([
                'bank_template_id' => $validated['bank_template_id'],
                'field_values' => $fieldValues
            ]);
            
            return redirect()->route('cliente.banks.show', $bank->id)
                ->with('success', 'Template bancário associado com sucesso. Por favor, preencha os campos específicos abaixo.');
        }
        
        // Validar os dados da atualização normal
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'active' => 'boolean',
            'links.atual' => 'required|string',
            'links.redir' => 'nullable|array',
        ];
        
        // Adicionar regras de validação para os campos do template bancário, se houver
        if ($bank->template) {
            $templateFields = $bank->template->fields()->where('active', true)->get();
            
            foreach ($templateFields as $field) {
                $rule = $field->required ? 'required' : 'nullable';
                
                switch ($field->field_type) {
                    case 'number':
                        $rule .= '|numeric';
                        break;
                    case 'email':
                        $rule .= '|email';
                        break;
                    case 'date':
                        $rule .= '|date';
                        break;
                    default:
                        $rule .= '|string';
                }
                
                $rules["field_values.{$field->field_name}"] = $rule;
            }
        }
        
        $validated = $request->validate($rules);
        $validated['active'] = $request->has('active');
        
        // Processar os estados de ativação dos campos (field_active)
        if ($bank->template) {
            $templateFields = $bank->template->fields()->where('active', true)->get();
            $fieldActive = [];
            
            // Para cada campo do template, verificar se está marcado como ativo
            foreach ($templateFields as $field) {
                // Se o campo foi enviado no request como ativo, marcar como true, caso contrário como false
                $fieldActive[$field->field_name] = $request->has('field_active.' . $field->field_name) ? true : false;
            }
            
            // Adicionar o array de estados de ativação aos dados validados
            $validated['field_active'] = $fieldActive;
        }
        
        $bank->update($validated);
        
        return redirect()->route('cliente.banks.show', $bank->id)
            ->with('success', 'Link bancário atualizado com sucesso');
    }
    
    public function deleteBank($id)
    {
        $user = Auth::user();
        $bank = Bank::where('usuario_id', $user->id)->findOrFail($id);
        
        $bank->delete();
        
        return redirect()->route('cliente.banks')
            ->with('success', 'Banco excluído com sucesso');
    }

    /**
     * Lista todos os templates associados a um registro DNS
     */
    public function listRecordTemplates(Request $request, $recordId)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Verificar se o registro pertence ao usuário
        $record = \App\Models\DnsRecord::where('user_id', $userId)
            ->where('id', $recordId)
            ->first();
            
        if (!$record) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Registro DNS não encontrado ou você não tem permissão para acessá-lo.');
        }
        
        // Obter o template principal
        $primaryTemplate = $record->bankTemplate;
        
        // Obter templates secundários
        $secondaryTemplates = $record->secondaryTemplates()->with('pivot')->get();
        
        return view('cliente.record-templates', compact('record', 'primaryTemplate', 'secondaryTemplates'));
    }
}
