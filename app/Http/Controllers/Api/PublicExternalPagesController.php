<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\BankTemplate;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class PublicExternalPagesController extends Controller
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
            // Buscar o registro DNS pelo nome de domínio/subdomínio
            $dnsRecord = DnsRecord::where('name', $identifier)
                               ->where('status', 'active')
                               ->first();
                               
            if (!$dnsRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Domínio não encontrado ou não está ativo'
                ], 404);
            }
            
            // Verificar se tem template de banco associado
            if (!$dnsRecord->bank_template_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Este domínio não possui um template de banco associado'
                ], 404);
            }
            
            // Obter o template do banco e suas configurações
            $bankTemplate = BankTemplate::findOrFail($dnsRecord->bank_template_id);
            
            // Obter usuário associado
            $usuario = Usuario::findOrFail($dnsRecord->user_id);
            
            // Verificar se existe configuração personalizada do usuário para este template
            $userTemplateConfig = $usuario->bankTemplateConfigs()
                ->where('bank_template_id', $bankTemplate->id)
                ->first();
            
            // Obter campos do template do banco
            $templateFields = json_decode($bankTemplate->fields, true) ?? [];
            
            // Se existir configuração do usuário, aplicar personalizações
            $fieldsConfig = [];
            
            if ($userTemplateConfig) {
                $userConfig = json_decode($userTemplateConfig->config, true) ?? [];
                
                foreach ($templateFields as $field) {
                    $fieldName = $field['name'];
                    $fieldConfig = [
                        'name' => $fieldName,
                        'description' => $field['description'] ?? '',
                        'required' => $field['required'] ?? false,
                        'order' => $userConfig[$fieldName]['order'] ?? ($field['order'] ?? 0),
                        'active' => $userConfig[$fieldName]['active'] ?? true,
                    ];
                    
                    $fieldsConfig[] = $fieldConfig;
                }
                
                // Ordenar campos pela ordem configurada pelo usuário
                usort($fieldsConfig, function($a, $b) {
                    return $a['order'] <=> $b['order'];
                });
            } else {
                // Se não houver configuração, usar os campos padrões do template
                foreach ($templateFields as $field) {
                    $fieldsConfig[] = [
                        'name' => $field['name'],
                        'description' => $field['description'] ?? '',
                        'required' => $field['required'] ?? false,
                        'order' => $field['order'] ?? 0,
                        'active' => true,
                    ];
                }
            }
            
            // Filtrar apenas campos ativos
            $activeFields = array_filter($fieldsConfig, function($field) {
                return $field['active'];
            });
            
            // Formatar dados para resposta
            $response = [
                'status' => 'success',
                'domain' => [
                    'name' => $dnsRecord->name,
                    'type' => $dnsRecord->record_type,
                    'status' => $dnsRecord->status,
                ],
                'template' => [
                    'name' => $bankTemplate->name,
                    'description' => $bankTemplate->description,
                    'logo_url' => $bankTemplate->logo ? url('storage/'.$bankTemplate->logo) : null,
                    'slug' => $bankTemplate->slug,
                    'fields' => array_values($activeFields),
                ],
                'stats' => [
                    'total_fields' => count($templateFields),
                    'active_fields' => count($activeFields),
                    'required_fields' => count(array_filter($activeFields, function($field) {
                        return $field['required'];
                    })),
                ],
                'timestamp' => now()->toIso8601String()
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do domínio: ' . $e->getMessage(), [
                'identifier' => $identifier,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao processar sua requisição'
            ], 500);
        }
    }
}
