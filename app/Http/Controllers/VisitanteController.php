<?php

namespace App\Http\Controllers;

use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitanteController extends Controller
{
    /**
     * Exibe a lista de visitantes do usuário atual
     */
    public function index()
    {
        $usuario = Auth::user();
        $visitantes = Visitante::where('usuario_id', $usuario->id)
                            ->with(['dnsRecord'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        return view('cliente.visitantes.index', compact('visitantes'));
    }
    
    /**
     * Exibe detalhes de um visitante específico
     */
    public function show($id)
    {
        $usuario = Auth::user();
        $visitante = Visitante::where('usuario_id', $usuario->id)
                        ->where('id', $id)
                        ->with(['informacoes', 'dnsRecord'])
                        ->firstOrFail();
        
        return view('cliente.visitantes.show', compact('visitante'));
    }
    
    /**
     * Exibe a lista de informações bancárias do usuário atual
     */
    public function informacoes()
    {
        $usuario = Auth::user();
        $informacoes = InformacaoBancaria::whereHas('visitante', function($query) use ($usuario) {
                                $query->where('usuario_id', $usuario->id);
                            })
                            ->with('visitante')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        return view('cliente.informacoes.index', compact('informacoes'));
    }
    
    /**
     * Exibe detalhes de uma informação bancária específica
     */
    public function showInformacao($id)
    {
        $usuario = Auth::user();
        $informacao = InformacaoBancaria::whereHas('visitante', function($query) use ($usuario) {
                            $query->where('usuario_id', $usuario->id);
                        })
                        ->where('id', $id)
                        ->with('visitante')
                        ->firstOrFail();
        
        return view('cliente.informacoes.show', compact('informacao'));
    }
}
