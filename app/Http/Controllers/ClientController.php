<?php

namespace App\Http\Controllers;

use App\Models\LinkGroup;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\TemplateUserConfig;
use App\Models\UserConfig;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientController extends Controller
{
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
            
            // Carregar o registro DNS específico que pertence ao usuário
            $record = \App\Models\DnsRecord::where('id', $recordId)
                ->where('user_id', $userId)
                ->with('bankTemplate')
                ->firstOrFail();
            
            $template = BankTemplate::with(['fields' => function($query) {
                $query->orderBy('order');
            }])->findOrFail($templateId);
            
            // Buscar configuração existente ou criar uma nova
            $userConfig = TemplateUserConfig::firstOrNew([
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId
            ]);
            
            // Se não existir configuração, inicializar com os valores padrão
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
            
            return view('cliente.template-config', compact('template', 'record', 'userConfig'));
        }
        
        // Verificar se foi fornecido apenas um domain_id
        elseif ($request->has('domain_id')) {
            $domainId = $request->input('domain_id');
            
            // Verificar se o domínio está associado ao usuário
            $domain = \App\Models\CloudflareDomain::whereHas('users', function($query) use ($userId) {
                $query->where('usuario_id', $userId);
            })->findOrFail($domainId);
            
            // Carregar todos os templates disponíveis
            $templates = BankTemplate::with(['fields' => function($query) {
                $query->orderBy('order');
            }])->where('active', true)->get();
            
            return view('cliente.domain-templates', compact('domain', 'templates'));
        }
        
        // Caso não tenha parâmetros, mostrar todos os templates configuráveis
        else {
            $templates = BankTemplate::with(['fields' => function($query) {
                $query->orderBy('order');
            }])->where('active', true)->get();
            
            return view('cliente.all-templates', compact('templates'));
        }
    }
    
    /**
     * Atualiza a configuração de um template para o cliente
     */
    public function updateTemplateConfig(Request $request, $templateId)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        $request->validate([
            'record_id' => 'required|exists:dns_records,id',
            'field_active' => 'required|array',
            'field_order' => 'required|array'
        ]);
        
        $recordId = $request->input('record_id');
        
        // Verificar se o registro pertence ao usuário
        $record = \App\Models\DnsRecord::where('id', $recordId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        // Obter o template com seus campos
        $template = BankTemplate::with('fields')->findOrFail($templateId);
        
        // Preparar a configuração
        $fieldConfig = [];
        foreach ($template->fields as $field) {
            $fieldName = $field->field_name;
            
            // Campos obrigatórios sempre são ativos
            $isActive = $field->required ? true : 
                        (isset($request->field_active[$fieldName]) ? true : false);
            
            $order = isset($request->field_order[$fieldName]) ? 
                    (int)$request->field_order[$fieldName] : $field->order;
            
            $fieldConfig[$fieldName] = [
                'active' => $isActive,
                'order' => $order
            ];
        }
        
        // Salvar ou atualizar a configuração
        TemplateUserConfig::updateOrCreate(
            [
                'user_id' => $userId,
                'template_id' => $templateId,
                'record_id' => $recordId
            ],
            ['config' => $fieldConfig]
        );
        
        return redirect()->route('cliente.templates.config', ['template_id' => $templateId, 'record_id' => $recordId])
            ->with('success', 'Configuração do template salva com sucesso!');
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Carregando os domínios Cloudflare associados ao usuário
        $user = Usuario::with([
            'cloudflareDomains' => function($query) use ($userId) {
                // Carrega apenas a contagem de registros DNS associados a este usuário
                $query->withCount(['dnsRecords' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }]);
            },
            // Carrega apenas os registros DNS que pertencem a este usuário
            'cloudflareDomains.dnsRecords' => function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->with(['bankTemplate', 'bank.template', 'externalApi']);
            }
        ])->findOrFail($userId);
        
        // Carregando os registros DNS associados diretamente ao usuário (sem relacionamento com domínio)
        $dnsRecords = \App\Models\DnsRecord::where('user_id', $userId)
            ->with(['bankTemplate', 'bank.template', 'externalApi'])
            ->get();
        
        $linkGroups = LinkGroup::where('usuario_id', $userId)
            ->where('active', true)
            ->with('items')
            ->get();
        
        $banks = Bank::where('usuario_id', $userId)->with('template')->get();
        
        return view('cliente.dashboard', compact('linkGroups', 'banks', 'user', 'dnsRecords'));
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

    public function banks()
    {
        $user = Auth::user();
        
        // Carregar os links bancários com seus templates associados
        $banks = Bank::where('usuario_id', $user->id)
                ->with('template')
                ->get();
        
        // Carregar os grupos de links para contextualizar os links bancários
        $linkGroups = LinkGroup::where('usuario_id', $user->id)
                ->with(['banks' => function($query) {
                    $query->with('template');
                }])
                ->get();
        
        return view('cliente.banks', compact('banks', 'linkGroups'));
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
        $bank = Bank::where('usuario_id', $user->id)
                ->with(['template', 'template.fields' => function($query) {
                    $query->where('active', true)->orderBy('order');
                }])
                ->findOrFail($id);
        
        // Verificar se o link bancário tem template associado e adicionar aviso se não tiver
        if (!$bank->template) {
            session()->flash('warning', 'Este link bancário não possui um template associado e precisa ser atualizado para a nova arquitetura.');
            
            // Obter todos os templates disponíveis para sugerir ao usuário
            $availableTemplates = BankTemplate::where('active', true)->get();
            return view('cliente.bank-details', compact('bank', 'availableTemplates'));
        }
        
        // Verificar a qual grupo de links este banco pertence
        $linkGroups = LinkGroup::whereHas('banks', function($query) use ($id) {
            $query->where('bank_id', $id);
        })->with('items')->get();
        
        return view('cliente.bank-details', compact('bank', 'linkGroups'));
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
}
