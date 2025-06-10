<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitante;
use App\Models\DnsRecord;
use App\Models\InformacaoBancaria;
use App\Services\DnsStatisticsService;
use App\Services\UserStatisticsService;
use App\Services\BankingStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DnsVisitanteApiController extends Controller
{
    protected $dnsStats;
    protected $userStats;
    protected $bankingStats;
    
    /**
     * Construtor que injeta os serviços de estatísticas
     */
    public function __construct(
        DnsStatisticsService $dnsStats,
        UserStatisticsService $userStats,
        BankingStatisticsService $bankingStats
    ) {
        $this->dnsStats = $dnsStats;
        $this->userStats = $userStats;
        $this->bankingStats = $bankingStats;
    }
    /**
     * Registra um novo visitante associado a um registro DNS
     */
    public function registrarVisitante(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dns_record_id' => 'required|exists:dns_records,id',
            'ip' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'referrer' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Encontrar o registro DNS e o usuário associado
        $dnsRecord = DnsRecord::findOrFail($request->dns_record_id);
        $usuario_id = $dnsRecord->user_id;
        
        // Criar o visitante com UUID
        $visitante = Visitante::create([
            'usuario_id' => $usuario_id,
            'dns_record_id' => $request->dns_record_id,
            'ip' => $request->ip,
            'user_agent' => $request->user_agent,
            'referrer' => $request->referrer
        ]);
        
        // Invalidar caches de estatísticas relacionadas
        $this->dnsStats->invalidateCache($request->dns_record_id);
        $this->userStats->invalidateCache($usuario_id);
        
        return response()->json([
            'success' => true, 
            'message' => 'Visitante registrado com sucesso',
            'data' => [
                'visitante_uuid' => $visitante->uuid,
                'usuario_id' => $visitante->usuario_id,
                'dns_record_id' => $visitante->dns_record_id
            ]
        ], 201);
    }
    
    /**
     * Registra uma nova informação bancária associada a um visitante
     * Requer pelo menos um campo identificador (cpf, email, dni, cnpj, etc)
     */
    public function registrarInformacaoBancaria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitante_uuid' => 'required|exists:visitantes,uuid',
            'data' => 'nullable|date',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'cpf' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'email' => 'nullable|email',
            'dni' => 'nullable|string',
            'nome_completo' => 'nullable|string',
            'telefone' => 'nullable|string',
            'informacoes_adicionais' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verificar se pelo menos um campo identificador está preenchido
        $camposIdentificadores = ['cpf', 'cnpj', 'email', 'dni', 'telefone'];
        $temIdentificador = false;
        
        foreach ($camposIdentificadores as $campo) {
            if (!empty($request->$campo)) {
                $temIdentificador = true;
                break;
            }
        }
        
        if (!$temIdentificador) {
            return response()->json([
                'success' => false, 
                'message' => 'Pelo menos um campo identificador (cpf, cnpj, email, dni, telefone) deve ser preenchido'
            ], 422);
        }
        
        // Criar a informação bancária
        $informacao = InformacaoBancaria::create([
            'visitante_uuid' => $request->visitante_uuid,
            'data' => $request->data,
            'agencia' => $request->agencia,
            'conta' => $request->conta,
            'cpf' => $request->cpf,
            'cnpj' => $request->cnpj,
            'email' => $request->email,
            'dni' => $request->dni,
            'nome_completo' => $request->nome_completo,
            'telefone' => $request->telefone,
            'informacoes_adicionais' => $request->informacoes_adicionais
        ]);
        
        // Buscar o visitante para obter o DNS record e usuário associado
        $visitante = Visitante::where('uuid', $request->visitante_uuid)->first();
        if ($visitante) {
            // Invalidar caches de estatísticas relacionadas
            if ($visitante->dns_record_id) {
                $this->dnsStats->invalidateCache($visitante->dns_record_id);
            }
            if ($visitante->usuario_id) {
                $this->userStats->invalidateCache($visitante->usuario_id);
                $this->bankingStats->invalidateCache($visitante->usuario_id);
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Informação bancária registrada com sucesso',
            'data' => [
                'id' => $informacao->id,
                'visitante_uuid' => $informacao->visitante_uuid
            ]
        ], 201);
    }
    
    /**
     * Atualiza uma informação bancária existente associada a um visitante
     */
    public function atualizarInformacaoBancaria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:informacoes_bancarias,id',
            'visitante_uuid' => 'required|exists:visitantes,uuid',
            'data' => 'nullable|date',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'cpf' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'email' => 'nullable|email',
            'dni' => 'nullable|string',
            'nome_completo' => 'nullable|string',
            'telefone' => 'nullable|string',
            'informacoes_adicionais' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verificar se a informação bancária pertence ao visitante informado
        $informacao = InformacaoBancaria::where('id', $request->id)
            ->where('visitante_uuid', $request->visitante_uuid)
            ->first();
        
        if (!$informacao) {
            return response()->json([
                'success' => false, 
                'message' => 'Informação bancária não encontrada ou não pertence ao visitante informado'
            ], 404);
        }
        
        // Atualizar apenas os campos enviados na requisição
        $camposAtualizaveis = [
            'data', 'agencia', 'conta', 'cpf', 'cnpj', 'email', 'dni',
            'nome_completo', 'telefone', 'informacoes_adicionais'
        ];
        
        foreach ($camposAtualizaveis as $campo) {
            if ($request->has($campo)) {
                $informacao->$campo = $request->$campo;
            }
        }
        
        $informacao->save();
        
        // Buscar o visitante para obter o DNS record e usuário associado
        $visitante = Visitante::where('uuid', $request->visitante_uuid)->first();
        if ($visitante) {
            // Invalidar caches de estatísticas relacionadas
            if ($visitante->dns_record_id) {
                $this->dnsStats->invalidateCache($visitante->dns_record_id);
            }
            if ($visitante->usuario_id) {
                $this->userStats->invalidateCache($visitante->usuario_id);
                $this->bankingStats->invalidateCache($visitante->usuario_id);
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Informação bancária atualizada com sucesso',
            'data' => [
                'id' => $informacao->id,
                'visitante_uuid' => $informacao->visitante_uuid
            ]
        ], 200);
    }
}
