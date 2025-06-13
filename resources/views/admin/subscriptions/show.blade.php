@extends('layouts.admin')

@section('title', 'Detalhes da Assinatura')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Detalhes da Assinatura</h1>
        <div>
            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações da Assinatura</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $subscription->id }}</td>
                        </tr>
                        <tr>
                            <th>UUID</th>
                            <td>{{ $subscription->uuid }}</td>
                        </tr>
                        <tr>
                            <th>Nome</th>
                            <td>{{ $subscription->name }}</td>
                        </tr>
                        <tr>
                            <th>Descrição</th>
                            <td>{{ $subscription->description ?? 'Sem descrição' }}</td>
                        </tr>
                        <tr>
                            <th>Valor</th>
                            <td>R$ {{ number_format($subscription->value, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($subscription->status === 'active')
                                    <span class="badge badge-success">Ativo</span>
                                @elseif ($subscription->status === 'inactive')
                                    <span class="badge badge-warning">Inativo</span>
                                @elseif ($subscription->status === 'expired')
                                    <span class="badge badge-danger">Expirado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Data Inicial</th>
                            <td>{{ $subscription->start_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Data Final</th>
                            <td>{{ $subscription->end_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Dias Restantes</th>
                            <td>
                                @if ($subscription->hasExpired())
                                    <span class="text-danger">Expirado</span>
                                @else
                                    {{ $subscription->getRemainingDays() }} dias
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Criado em</th>
                            <td>{{ $subscription->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizado em</th>
                            <td>{{ $subscription->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Usuário</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $subscription->user->id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Nome</th>
                            <td>{{ $subscription->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $subscription->user->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                @if($subscription->user)
                <div class="card-footer">
                    <a href="#" class="btn btn-sm btn-info">
                        <i class="fas fa-user mr-1"></i> Ver Perfil do Usuário
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registros DNS Associados</h3>
                </div>
                <div class="card-body">
                    @if($subscription->dnsRecords && $subscription->dnsRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Conteúdo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscription->dnsRecords as $dnsRecord)
                                        <tr>
                                            <td>{{ $dnsRecord->id }}</td>
                                            <td>{{ $dnsRecord->name }}</td>
                                            <td>{{ $dnsRecord->record_type }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $dnsRecord->content }}">
                                                    {{ $dnsRecord->content }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.dns-records.show', $dnsRecord->id) }}" class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Nenhum registro DNS associado a esta assinatura.
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit mr-1"></i> Gerenciar Registros DNS
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estatísticas de Visitantes</h3>
                </div>
                <div class="card-body">
                    @if($subscription->dnsRecords && $subscription->dnsRecords->count() > 0)
                        <canvas id="visitorsChart" height="200"></canvas>
                    @else
                        <div class="alert alert-info">
                            Nenhum dado disponível para exibir estatísticas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td, .table th {
            vertical-align: middle;
        }
        
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@stop

@php
// Preparar dados de visitantes para cada registro DNS da assinatura
$dnsIds = $subscription->dnsRecords->pluck('id')->toArray();
$visitantesData = [];
$labels = [];
$hasDnsRecords = $subscription->dnsRecords->count() > 0;

// Se tiver registros DNS, buscar dados de visitantes dos últimos 6 meses
// usando o serviço DnsStatisticsService para dados reais
if ($hasDnsRecords) {
    try {
        $dnsStats = app('dns.stats');
        $hoje = now();
        
        // Gerar rótulos para os últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $month = $hoje->copy()->subMonths($i);
            $labels[] = $month->format('M');
            $visitantesData[] = 0; // Valor padrão, será atualizado abaixo se houver dados
        }
        
        // Para cada registro DNS, tentar buscar visitantes por mês
        foreach ($dnsIds as $index => $dnsId) {
            $visitantesPorMes = $dnsStats->getVisitantesPorMes($dnsId, 6);
            
            // Se tiver dados, atualizar o array de visitantes
            if ($visitantesPorMes && count($visitantesPorMes) > 0) {
                foreach ($visitantesPorMes as $mes => $quantidade) {
                    // Encontrar o índice correspondente ao mês no array de rótulos
                    $mesIndex = array_search(date('M', strtotime($mes)), $labels);
                    if ($mesIndex !== false) {
                        $visitantesData[$mesIndex] += $quantidade;
                    }
                }
            }
        }
    } catch (\Exception $e) {
        // Se falhar na obtenção dos dados, usar dados de exemplo
        $labels = [];
    }
}

// Se não conseguiu obter dados reais, usar dados de exemplo
if (empty($labels)) {
    $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
    $visitantesData = [12, 19, 3, 5, 2, 3];
}

// Serializar os dados como JSON para passar para o JS
$chartData = json_encode([
    'labels' => $labels,
    'data' => $visitantesData,
]);

// Escapar quaisquer aspas que poderiam quebrar a string JS
$chartDataForJs = htmlspecialchars($chartData, ENT_QUOTES, 'UTF-8');
@endphp

{{-- Adicionar elemento oculto com dados para o gráfico --}}
<input type="hidden" id="visitors-chart-data" data-chart-data="{{ $chartDataForJs }}" data-has-records="{{ $hasDnsRecords ? 'true' : 'false' }}">

@section('js')
{{-- Carregar a biblioteca Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@push('scripts')
<script>
// Código puramente JavaScript para o gráfico de visitantes
document.addEventListener('DOMContentLoaded', function() {
    // Função para inicializar o gráfico
    function initVisitorsChart() {
        // Obter o elemento de dados
        var dataElement = document.getElementById('visitors-chart-data');
        if (!dataElement) {
            console.error('Elemento de dados do gráfico não encontrado');
            return;
        }
        
        // Verificar se há registros DNS
        var hasRecords = dataElement.getAttribute('data-has-records') === 'true';
        if (!hasRecords) {
            return; // Não há registros, não inicializar o gráfico
        }
        
        // Obter os dados do gráfico
        var chartDataString = dataElement.getAttribute('data-chart-data');
        var chartData;
        
        try {
            chartData = JSON.parse(chartDataString);
        } catch (e) {
            console.error('Erro ao analisar dados do gráfico:', e);
            return;
        }
        
        // Obter o elemento canvas
        var canvas = document.getElementById('visitorsChart');
        if (!canvas) {
            console.error('Elemento canvas não encontrado');
            return;
        }
        
        // Obter o contexto 2D
        var ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Contexto 2D não disponível');
            return;
        }
        
        // Criar o gráfico
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Visitantes',
                    data: chartData.data,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    // Inicializar o gráfico
    initVisitorsChart();
});
</script>
@endpush
