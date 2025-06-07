<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Models\LinkGroupItem;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitanteApiController extends Controller
{
    /**
     * Registra um novo visitante a partir de uma requisição API
     */
    public function registrarVisitante(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link_id' => 'required|exists:link_group_items,id',
            'ip' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'referrer' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Encontrar o link e o usuário associado
        $link = LinkGroupItem::findOrFail($request->link_id);
        $group = $link->group;
        $usuario_id = $group->usuario_id;
        
        // Criar o visitante com UUID
        $visitante = Visitante::create([
            'usuario_id' => $usuario_id,
            'link_id' => $request->link_id,
            'ip' => $request->ip,
            'user_agent' => $request->user_agent,
            'referrer' => $request->referrer
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Visitante registrado com sucesso',
            'data' => [
                'visitante_uuid' => $visitante->uuid,
                'usuario_id' => $visitante->usuario_id
            ]
        ], 201);
    }
    
    /**
     * Registra uma nova informação bancária associada a um visitante
     */
    public function registrarInformacaoBancaria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitante_uuid' => 'required|exists:visitantes,uuid',
            'data' => 'nullable|date',
            'agencia' => 'nullable|string',
            'conta' => 'nullable|string',
            'cpf' => 'nullable|string',
            'nome_completo' => 'nullable|string',
            'telefone' => 'nullable|string',
            'informacoes_adicionais' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Criar a informação bancária
        $informacao = InformacaoBancaria::create([
            'visitante_uuid' => $request->visitante_uuid,
            'data' => $request->data,
            'agencia' => $request->agencia,
            'conta' => $request->conta,
            'cpf' => $request->cpf,
            'nome_completo' => $request->nome_completo,
            'telefone' => $request->telefone,
            'informacoes_adicionais' => $request->informacoes_adicionais
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Informação bancária registrada com sucesso',
            'data' => [
                'id' => $informacao->id
            ]
        ], 201);
    }
}
