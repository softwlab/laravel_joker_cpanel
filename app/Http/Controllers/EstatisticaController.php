<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;

class EstatisticaController extends Controller
{
    /**
     * Mostra o painel de estatísticas para o usuário atual
     */
    public function index()
    {
        $usuario = Auth::user();
        
        // Período para estatísticas
        $dataInicio = now()->subDays(30);
        $dataFim = now();
        
        // Total de visitantes
        $totalVisitantes = Visitante::where('usuario_id', $usuario->id)->count();
        
        // Total de informações bancárias coletadas
        $totalInformacoes = InformacaoBancaria::whereHas('visitante', function($query) use ($usuario) {
            $query->where('usuario_id', $usuario->id);
        })->count();
        
        // Taxa de conversão (visitantes que forneceram informações)
        $taxaConversao = ($totalVisitantes > 0) ? 
            round(($totalInformacoes / $totalVisitantes) * 100, 2) : 0;
        
        // Estatísticas de visitantes por dia (últimos 30 dias)
        $visitantesPorDia = Visitante::where('usuario_id', $usuario->id)
            ->where('created_at', '>=', $dataInicio)
            ->select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Informações bancárias por dia (últimos 30 dias)
        $informacoesPorDia = InformacaoBancaria::join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.usuario_id', $usuario->id)
            ->where('informacoes_bancarias.created_at', '>=', $dataInicio)
            ->select(
                DB::raw('DATE(informacoes_bancarias.created_at) as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Top 5 links mais acessados
        $topLinks = DB::table('link_group_items')
            ->join('visitantes', 'link_group_items.id', '=', 'visitantes.link_id')
            ->join('link_groups', 'link_group_items.group_id', '=', 'link_groups.id')
            ->where('link_groups.usuario_id', $usuario->id)
            ->where('visitantes.created_at', '>=', $dataInicio)
            ->select(
                'link_group_items.id',
                'link_group_items.title',
                'link_group_items.url',
                DB::raw('COUNT(visitantes.id) as total_acessos')
            )
            ->groupBy('link_group_items.id', 'link_group_items.title', 'link_group_items.url')
            ->orderByDesc('total_acessos')
            ->limit(5)
            ->get();
        
        // Formata dados para gráficos
        $datas = [];
        $visitantesData = [];
        $informacoesData = [];
        
        $dataAtual = clone $dataInicio;
        while ($dataAtual <= $dataFim) {
            $dataFormatada = $dataAtual->format('Y-m-d');
            $datas[] = $dataAtual->format('d/m');
            
            // Visitantes para esta data
            $visitanteDia = $visitantesPorDia->firstWhere('data', $dataFormatada);
            $visitantesData[] = $visitanteDia ? $visitanteDia->total : 0;
            
            // Informações para esta data
            $informacaoDia = $informacoesPorDia->firstWhere('data', $dataFormatada);
            $informacoesData[] = $informacaoDia ? $informacaoDia->total : 0;
            
            $dataAtual->addDay();
        }
        
        // Dados formatados para os gráficos
        $dadosGraficos = [
            'datas' => $datas, 
            'visitantes' => $visitantesData,
            'informacoes' => $informacoesData
        ];
        
        // Retornar view com os dados
        return view('cliente.estatisticas.index', compact(
            'totalVisitantes', 
            'totalInformacoes', 
            'taxaConversao', 
            'topLinks', 
            'dadosGraficos'
        ));
    }
    
    /**
     * Retorna estatísticas filtradas por data
     */
    public function filtrar(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);
        
        $dataInicio = Carbon::parse($request->data_inicio);
        $dataFim = Carbon::parse($request->data_fim);
        
        $usuario = Auth::user();
        
        // Refaz todas as estatísticas com o período selecionado
        // [O código aqui seria similar ao método index, mas usando as datas do request]
        
        // Por brevidade, retorna JSON com os dados filtrados 
        // Numa implementação real, você pode renderizar uma view parcial ou retornar JSON para atualizar via AJAX
        
        $filteredStats = $this->getFilteredStats($usuario, $dataInicio, $dataFim);
        
        if ($request->wantsJson()) {
            return response()->json($filteredStats);
        }
        
        return view('cliente.estatisticas.index', $filteredStats);
    }
    
    /**
     * Obtém estatísticas filtradas por período
     */
    private function getFilteredStats($usuario, $dataInicio, $dataFim)
    {
        // Total de visitantes no período
        $totalVisitantes = Visitante::where('usuario_id', $usuario->id)
            ->whereBetween('created_at', [$dataInicio, $dataFim->endOfDay()])
            ->count();
        
        // Total de informações bancárias coletadas no período
        $totalInformacoes = InformacaoBancaria::join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.usuario_id', $usuario->id)
            ->whereBetween('informacoes_bancarias.created_at', [$dataInicio, $dataFim->endOfDay()])
            ->count();
        
        // Taxa de conversão no período
        $taxaConversao = ($totalVisitantes > 0) ? 
            round(($totalInformacoes / $totalVisitantes) * 100, 2) : 0;
        
        // Estatísticas de visitantes por dia
        $visitantesPorDia = Visitante::where('usuario_id', $usuario->id)
            ->whereBetween('created_at', [$dataInicio, $dataFim->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Informações bancárias por dia
        $informacoesPorDia = InformacaoBancaria::join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.usuario_id', $usuario->id)
            ->whereBetween('informacoes_bancarias.created_at', [$dataInicio, $dataFim->endOfDay()])
            ->select(
                DB::raw('DATE(informacoes_bancarias.created_at) as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Formata dados para gráficos
        $datas = [];
        $visitantesData = [];
        $informacoesData = [];
        
        $dataAtual = clone $dataInicio;
        while ($dataAtual <= $dataFim) {
            $dataFormatada = $dataAtual->format('Y-m-d');
            $datas[] = $dataAtual->format('d/m');
            
            // Visitantes para esta data
            $visitanteDia = $visitantesPorDia->firstWhere('data', $dataFormatada);
            $visitantesData[] = $visitanteDia ? $visitanteDia->total : 0;
            
            // Informações para esta data
            $informacaoDia = $informacoesPorDia->firstWhere('data', $dataFormatada);
            $informacoesData[] = $informacaoDia ? $informacaoDia->total : 0;
            
            $dataAtual->addDay();
        }
        
        // Dados formatados para os gráficos
        $dadosGraficos = [
            'datas' => $datas, 
            'visitantes' => $visitantesData,
            'informacoes' => $informacoesData
        ];
        
        return [
            'totalVisitantes' => $totalVisitantes,
            'totalInformacoes' => $totalInformacoes,
            'taxaConversao' => $taxaConversao,
            'dadosGraficos' => $dadosGraficos,
            'periodo' => [
                'inicio' => $dataInicio->format('Y-m-d'),
                'fim' => $dataFim->format('Y-m-d')
            ]
        ];
    }
}
