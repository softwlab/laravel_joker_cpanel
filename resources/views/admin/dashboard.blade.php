@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Admin</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalUsers }}</h4>
                        <p class="mb-0">Total de Usuários</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users') }}" class="text-white text-decoration-none">
                    Ver detalhes <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $recentAccess->count() }}</h4>
                        <p class="mb-0">Acessos Recentes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.logs') }}" class="text-white text-decoration-none">
                    Ver logs <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ \App\Models\Usuario::where('ativo', true)->count() }}</h4>
                        <p class="mb-0">Usuários Ativos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Acessos Recentes</h5>
            </div>
            <div class="card-body">
                @if($recentAccess->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>IP</th>
                                <th>Data/Hora</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAccess as $access)
                            <tr>
                                <td>{{ $access->usuario->nome }}</td>
                                <td><code>{{ $access->ip }}</code></td>
                                <td>{{ $access->data_acesso->format('d/m/Y H:i:s') }}</td>
                                <td class="text-truncate" style="max-width: 200px;">
                                    {{ $access->user_agent }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">Nenhum acesso registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection