@extends('layouts.admin')

@section('title', 'Relatório de API Depreciada')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">
        <i class="fas fa-exclamation-triangle text-warning"></i> 
        Relatório de Uso de API Depreciada
    </h1>

    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i>
        <strong>Sistema em fase de depreciação:</strong> As APIs antigas de links estão programadas para serem desativadas em <strong>{{ config('deprecation.end_date') }}</strong>.
        Favor migrar para a nova API baseada em DNS.
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Visão Geral do Uso</h5>
                </div>
                <div class="card-body">
                    @if(!isset($report['error']))
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-primary h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Total de Chamadas</h5>
                                    <h2 class="display-4">{{ $report['total_calls'] }}</h2>
                                    <p class="card-text">Chamadas à API depreciada</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-warning h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Clientes Únicos</h5>
                                    <h2 class="display-4">{{ $report['unique_clients'] }}</h2>
                                    <p class="card-text">Clientes diferentes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-info h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Média Diária</h5>
                                    <h2 class="display-4">{{ $report['daily_average'] }}</h2>
                                    <p class="card-text">Chamadas por dia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        {{ $report['error'] }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Detalhamento por Endpoint</h5>
                </div>
                <div class="card-body">
                    @if(!isset($report['error']) && count($report['endpoints']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Endpoint</th>
                                    <th>Total de Chamadas</th>
                                    <th>Clientes Únicos</th>
                                    <th>Última Chamada</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['endpoints'] as $endpoint)
                                <tr>
                                    <td><code>{{ $endpoint['endpoint'] }}</code></td>
                                    <td>{{ $endpoint['calls'] }}</td>
                                    <td>{{ $endpoint['unique_clients'] }}</td>
                                    <td>{{ $endpoint['last_call'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $endpoint['calls'] > 100 ? 'danger' : 'warning' }}">
                                            {{ $endpoint['calls'] > 100 ? 'Alto Uso' : 'Uso Moderado' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @elseif(!isset($report['error']))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        Nenhum uso da API depreciada foi detectado no período selecionado!
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Plano de Depreciação</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h5>{{ config('deprecation.start_date') }}</h5>
                                <p>Início da fase de depreciação. Avisos são enviados em todas as chamadas à API antiga.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h5>{{ config('deprecation.warning_date') }}</h5>
                                <p>Intensificação dos avisos. Emails são enviados aos clientes que ainda utilizam a API antiga.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h5>{{ config('deprecation.end_date') }}</h5>
                                <p>Desativação completa da API antiga. Todas as chamadas retornarão erro 410 Gone.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline-item {
    display: flex;
    margin-bottom: 30px;
}
.timeline-marker {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 15px;
    margin-top: 5px;
}
.timeline-content h5 {
    margin-bottom: 5px;
}
</style>
@endsection
