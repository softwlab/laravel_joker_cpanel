<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankTemplate;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\TemplateUserConfig;
use App\Models\Usuario;
use App\Models\UserConfig;
use App\Models\Bank;
use App\Models\PublicApiKey;
use App\Models\PublicApiKeyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublicApiController extends Controller
{
    /**
     * Middleware para verificar a API key
     */
    public function __construct()
    {
        // Usar o middleware PublicApiAuthenticate já definido nas rotas
        // Não precisamos definir middleware aqui pois já está aplicado no grupo de rotas
    }

    /**
     * Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
     * Inclui suporte para templates multipágina (multibanco)
     *
     * @param  string  $identifier
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomainData($identifier, Request $request)
    {
        try {
            // Log da tentativa de acesso
            $apiKey = $this->getApiKey($request);
            if ($apiKey) {
                $apiKey->logAction('domain_data_request', [
                    'domain' => $identifier,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            Log::info('API Pública - Tentativa de acesso para domínio: ' . $identifier);
            
            // Abordagem 1: Buscar pelo registro DNS
            $dnsRecord = DnsRecord::where('name', $identifier)
                               ->where('status', 'active')
                               ->first();
            
            if (!$dnsRecord) {
                // Abordagem 2: Tentar encontrar pelo domínio Cloudflare
                $cfDomain = CloudflareDomain::where('name', 'like', '%'.$identifier.'%')
                                     ->first();
                                     
                if ($cfDomain) {
                    Log::info('API Pública - Domínio Cloudflare encontrado: ' . $cfDomain->id . ' - ' . $cfDomain->name);
                    
                    // Buscar o usuário associado pelo pivot
                    $usuarios = $cfDomain->usuarios;
                    
                    if ($usuarios && $usuarios->count() > 0) {
                        $usuario = $usuarios->first();
                        
                        // Criar um response manual com dados do Cloudflare
                        $response = [
                            'status' => 'success',
                            'domain' => [
                                'name' => $cfDomain->name,
                                'type' => 'CloudflareDomain',
                                'content' => $cfDomain->name_servers ? json_encode($cfDomain->name_servers) : '',
                                'proxied' => true
                            ],
                            'user' => [
                                'id' => $usuario->id,
                                'name' => $usuario->nome,
                                'email' => $usuario->email
                            ],
                            'cloudflare' => [
                                'zone_id' => $cfDomain->zone_id,
                                'status' => $cfDomain->status,
                                'paused' => (bool)$cfDomain->paused,
                                'name_servers' => $cfDomain->name_servers
                            ]
                        ];
            
                        return response()->json($response);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Nenhum usuário associado a este domínio'
                        ], 404);
                    }
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Domínio não encontrado'
                    ], 404);
                }
            } else {
                // Uso do registro DNS encontrado
                $usuario = $dnsRecord->user;
                
                if (!$usuario) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Usuário não encontrado para este registro DNS'
                    ], 404);
                }

                // Preparar resposta básica
                $response = [
                    'status' => 'success',
                    'domain' => [
                        'name' => $dnsRecord->name,
                        'type' => $dnsRecord->record_type,
                        'content' => $dnsRecord->content,
                        'id' => $dnsRecord->id
                    ],
                    'user' => [
                        'id' => $usuario->id,
                        'name' => $usuario->nome,
                        'email' => $usuario->email
                    ]
                ];
                
                // Adicionar template principal para compatibilidade
                if ($dnsRecord->bank_template_id) {
                    $primaryTemplate = BankTemplate::with('fields')->find($dnsRecord->bank_template_id);
                    if ($primaryTemplate) {
                        $templateData = [
                            'id' => $primaryTemplate->id,
                            'name' => $primaryTemplate->name,
                            'description' => $primaryTemplate->description
                        ];
                        
                        // Buscar configurações do usuário para este template
                        $userConfig = TemplateUserConfig::where('usuario_id', $usuario->id)
                                          ->where('bank_template_id', $primaryTemplate->id)
                                          ->first();
                        
                        // Adicionar campos do template
                        if ($primaryTemplate->fields) {
                            $fields = $primaryTemplate->fields->map(function ($field) use ($userConfig) {
                                // Usar configurações do usuário se disponíveis
                                $config = null;
                                if ($userConfig && isset($userConfig->config[$field->name])) {
                                    $config = $userConfig->config[$field->name];
                                } elseif ($userConfig && isset($userConfig->config[$field->id])) {
                                    $config = $userConfig->config[$field->id];
                                }
                                
                                return [
                                    'id' => $field->id,
                                    'name' => $field->name,
                                    'description' => $field->description,
                                    'required' => $field->required,
                                    'type' => $field->field_type,
                                    'order' => $config && isset($config['order']) ? $config['order'] : $field->order,
                                    'active' => $config && isset($config['active']) ? $config['active'] : $field->active,
                                    'default_value' => $field->default_value,
                                    'validation' => $field->validation,
                                    'options' => $field->options ? json_decode($field->options, true) : null
                                ];
                            });
                            
                            // Ordenar campos por ordem
                            $fields = $fields->sortBy('order')->values();
                            $templateData['fields'] = $fields;
                            $templateData['user_config'] = $userConfig ? $userConfig->config : null;
                        }
                        
                        $response['template'] = $templateData;
                    }
                }
                
                // Verificar se o registro DNS tem configuração multipágina (multibanco)
                if ($dnsRecord->isMultipage()) {
                    // Obter a configuração completa multipágina
                    $multipageConfig = $dnsRecord->getMultipageConfig();
                    $response['multipage'] = $multipageConfig;
                    
                    // Preparar array para todos os templates
                    $response['templates'] = [];
                    
                    // Para cada página na configuração multipágina
                    foreach ($multipageConfig['pages'] as $page) {
                        $templateId = $page['template_id'];
                        $template = BankTemplate::with('fields')->find($templateId);
                        
                        if ($template) {
                            $templateData = [
                                'id' => $template->id,
                                'name' => $template->name,
                                'description' => $template->description,
                                'path' => $page['path'] ?? '',
                                'is_primary' => $page['is_primary'] ?? false
                            ];
                            
                            // Buscar configurações personalizadas do usuário para este template
                            $userConfig = TemplateUserConfig::where('usuario_id', $usuario->id)
                                              ->where('bank_template_id', $template->id)
                                              ->first();
                            
                            // Preparar campos do template
                            if ($template->fields && $template->fields->count() > 0) {
                                $fields = $template->fields->map(function ($field) use ($userConfig) {
                                    // Usar o nome do campo como chave para acessar as configurações do usuário
                                    $config = null;
                                    if ($userConfig) {
                                        if (isset($userConfig->config[$field->name])) {
                                            $config = $userConfig->config[$field->name];
                                        } elseif (isset($userConfig->config[$field->id])) {
                                            $config = $userConfig->config[$field->id];
                                        }
                                    }
                                    
                                    return [
                                        'id' => $field->id,
                                        'name' => $field->name,
                                        'description' => $field->description,
                                        'required' => $field->required,
                                        'type' => $field->field_type,
                                        'order' => $config && isset($config['order']) ? $config['order'] : $field->order,
                                        'active' => $config && isset($config['active']) ? $config['active'] : $field->active,
                                        'default_value' => $field->default_value,
                                        'validation' => $field->validation,
                                        'options' => $field->options ? json_decode($field->options, true) : null
                                    ];
                                });
                                
                                // Organizar por ordem
                                $fields = $fields->sortBy('order')->values();
                                
                                // Adicionar os campos com configurações à resposta
                                $templateData['fields'] = $fields;
                                $templateData['user_config'] = $userConfig ? $userConfig->config : null;
                                
                                // Adicionar template à lista de templates
                                $response['templates'][] = $templateData;
                            }
                        }
                    }
                }
                
                return response()->json($response);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro na API pública: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar a solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém configuração do template baseado no domínio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplateConfig(Request $request)
    {
        try {
            // Validação dos parâmetros
            $validator = Validator::make($request->all(), [
                'domain' => 'required|string',
                'path_segment' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetros inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $domain = $request->input('domain');
            $pathSegment = $request->input('path_segment');
            
            // Buscar o registro DNS
            $dnsRecord = DnsRecord::where('name', $domain)
                              ->where('status', 'active')
                              ->first();
            
            if (!$dnsRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domínio não encontrado'
                ], 404);
            }
            
            $usuario = $dnsRecord->user;
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado para este domínio'
                ], 404);
            }
            
            // Determinar qual template deve ser usado
            $template = null;
            
            // Se é multipágina e tem um path segment, busca um template secundário
            if ($dnsRecord->isMultipage() && $pathSegment) {
                $multipageConfig = $dnsRecord->getMultipageConfig();
                
                foreach ($multipageConfig['pages'] as $page) {
                    if ($page['path'] === $pathSegment) {
                        $template = BankTemplate::with('fields')->find($page['template_id']);
                        break;
                    }
                }
                
                if (!$template) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Template não encontrado para o path segment: ' . $pathSegment
                    ], 404);
                }
            } else {
                // Padrão: usa o template principal associado ao DNS record
                if (!$dnsRecord->bank_template_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nenhum template principal configurado para este domínio'
                    ], 404);
                }
                
                $template = BankTemplate::with('fields')->find($dnsRecord->bank_template_id);
                
                if (!$template) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Template não encontrado'
                    ], 404);
                }
            }
            
            // Buscar configurações personalizadas do usuário para este template
            $userConfig = TemplateUserConfig::where('usuario_id', $usuario->id)
                                          ->where('bank_template_id', $template->id)
                                          ->first();

            // Preparar a resposta com os campos do template e suas configurações
            $fields = $template->fields->map(function ($field) use ($userConfig) {
                // Usar o nome do campo como chave para acessar as configurações do usuário
                // Também tenta acessar pelo ID como fallback para compatibilidade
                $config = null;
                if ($userConfig) {
                    if (isset($userConfig->config[$field->name])) {
                        $config = $userConfig->config[$field->name];
                    } elseif (isset($userConfig->config[$field->id])) {
                        $config = $userConfig->config[$field->id];
                    }
                }
                
                return [
                    'id' => $field->id,
                    'name' => $field->name,
                    'description' => $field->description,
                    'required' => $field->required,
                    'type' => $field->field_type,
                    'order' => $config && isset($config['order']) ? $config['order'] : $field->order,
                    'active' => $config && isset($config['active']) ? $config['active'] : $field->active,
                    'default_value' => $field->default_value,
                    'validation' => $field->validation,
                    'options' => $field->options ? json_decode($field->options, true) : null
                ];
            });

            // Organizar por ordem
            $fields = $fields->sortBy('order')->values();

            // Preparar resposta com informações sobre configuração multipágina
            $response = [
                'success' => true,
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'logo' => $template->logo,
                    'fields' => $fields
                ],
                'domain' => [
                    'name' => $domain,
                    'record_id' => $dnsRecord->id,
                    'record_type' => $dnsRecord->record_type,
                    'status' => $dnsRecord->status
                ],
                'user' => [
                    'id' => $usuario->id,
                    'name' => $usuario->nome
                ]
            ];
            
            // Adicionar informações sobre multipágina, se aplicável
            if ($dnsRecord->isMultipage()) {
                $multipageConfig = $dnsRecord->getMultipageConfig();
                $response['multipage'] = $multipageConfig;
                
                // Adicionar flag indicando se estamos em uma página secundária
                $response['is_secondary_page'] = ($pathSegment && !empty($pathSegment));
                $response['current_path_segment'] = $pathSegment;
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar requisição de template: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação',
                'error' => app()->environment('production') ? 'Erro interno do servidor' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recupera uma API key da requisição
     * 
     * @param Request $request
     * @return PublicApiKey|null
     */
    private function getApiKey(Request $request)
    {
        $apiKeyValue = $request->header('X-API-Key');
        if (!$apiKeyValue) {
            return null;
        }

        return PublicApiKey::where('key', $apiKeyValue)->first();
    }
}
