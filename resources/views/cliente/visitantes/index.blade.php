@extends('layouts.app')

@section('title', 'Visitantes')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Visitantes</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Painel de estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Visitantes</h5>
                    <h2 class="mb-0">{{ number_format($estatisticas['totalVisitantes'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Registros DNS</h5>
                    <h2 class="mb-0">{{ number_format($estatisticas['totalDnsRecords'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Informações Bancárias</h5>
                    <h2 class="mb-0">{{ number_format($estatisticas['totalInfoBancarias'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h5 class="card-title">Taxa de Conversão</h5>
                    <h2 class="mb-0">{{ number_format(($estatisticas['totalInfoBancarias'] / max($estatisticas['totalVisitantes'], 1)) * 100, 1, ',', '.') }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de visitantes por mês -->
    @if(isset($estatisticas['visitantesPorMes']) && !empty($estatisticas['visitantesPorMes']))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Visitantes por Mês</h5>
        </div>
        <div class="card-body">
            <canvas id="visitantesChart" height="100"></canvas>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Visitantes</h5>
            </div>
        </div>
        <div class="card-body">
            @if($visitantes->isEmpty())
                <div class="alert alert-info">
                    Nenhum visitante registrado.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DNS Record</th>
                                <th>IP</th>
                                <th>Data</th>
                                <th>Informações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitantes as $visitante)
                            <tr>
                                <td>{{ $visitante->id }}</td>
                                <td>
                                    @if($visitante->dnsRecord)
                                        <span>{{ $visitante->dnsRecord->name }}</span>
                                    @else
                                        <span class="text-muted">DNS Record removido</span>
                                    @endif
                                </td>
                                <td>{{ $visitante->ip }}</td>
                                <td>{{ $visitante->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $visitante->informacoes->count() }}
                                </td>
                                <td>
                                    <a href="{{ route('cliente.visitantes.show', $visitante->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $visitantes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($estatisticas['visitantesPorMes']) && !empty($estatisticas['visitantesPorMes']))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitantesChart');
        const context = ctx ? ctx.getContext('2d') : null;
        
        // Extrair dados mensais de visitantes
        const visitantesMensais = {!! json_encode(isset($estatisticas['visitantesPorMes']) ? $estatisticas['visitantesPorMes'] : []) !!};
        const meses = Object.keys(visitantesMensais);
        const valores = Object.values(visitantesMensais);
        
        if (ctx && context) {
            new Chart(context, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Visitantes por Mês',
                    data: valores,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Tendência de Visitantes Mensais'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        }
    });
</script>
@endif
@endsection
