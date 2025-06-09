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
        $this->middleware('api.key');
    }

    /**
     * Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
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
                
                // Adicionar informações de template se disponíveis
                if ($dnsRecord->bank_template_id) {
                    $template = BankTemplate::find($dnsRecord->bank_template_id);
                    if ($template) {
                        $response['template'] = [
                            'id' => $template->id,
                            'name' => $template->name,
                            'description' => $template->description
                        ];
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parâmetros inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $domain = $request->input('domain');

            // Log da tentativa de acesso
            $apiKey = $this->getApiKey($request);
            if ($apiKey) {
                $apiKey->logAction('template_config_request', [
                    'domain' => $domain,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            Log::info("Requisição de configuração de template para domínio: {$domain}");

            // Buscar informações do DNS pelo domínio
            $dnsRecord = DnsRecord::where('name', 'like', "%{$domain}%")
                                ->where('status', 'active')
                                ->first();

            if (!$dnsRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domínio não encontrado ou inativo'
                ], 404);
            }

            // Buscar usuário associado ao registro DNS
            $usuario = $dnsRecord->user;
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado para este domínio'
                ], 404);
            }

            // Buscar template associado
            $template = null;
            if ($dnsRecord->bank_template_id) {
                $template = BankTemplate::with('fields')->find($dnsRecord->bank_template_id);
            } elseif ($dnsRecord->bank_id) {
                $bank = Bank::find($dnsRecord->bank_id);
                if ($bank && $bank->bank_template_id) {
                    $template = BankTemplate::with('fields')->find($bank->bank_template_id);
                }
            }

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template não encontrado para este domínio'
                ], 404);
            }

            // Buscar configurações personalizadas do usuário para este template
            $userConfig = TemplateUserConfig::where('usuario_id', $usuario->id)
                                          ->where('bank_template_id', $template->id)
                                          ->first();

            // Preparar a resposta com os campos do template e suas configurações
            $fields = $template->fields->map(function ($field) use ($userConfig) {
                $config = $userConfig && isset($userConfig->config[$field->id]) 
                    ? $userConfig->config[$field->id] 
                    : null;
                
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
                    'options' => $field->options ? json_decode($field->options, true) : null,
                ];
            });

            // Organizar por ordem
            $fields = $fields->sortBy('order')->values();

            return response()->json([
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
            ]);
            
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
