@extends('layouts.admin')

@section('title', 'Detalhes da Associação de Domínio Cloudflare')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalhes da Associação de Domínio Cloudflare</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.cloudflare.domain-associations.edit', [$domain->id, $usuario->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.cloudflare.domain-associations.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações da Associação</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Domínio:</div>
                        <div class="col-md-9">{{ $domain->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Usuário:</div>
                        <div class="col-md-9">
                            <a href="{{ route('admin.users.show', $usuario->id) }}">
                                {{ $usuario->nome }} ({{ $usuario->email }})
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Status:</div>
                        <div class="col-md-9">
                            @if($usuario->pivot->status === 'active')
                                <span class="badge bg-success">Ativo</span>
                            @elseif($usuario->pivot->status === 'paused')
                                <span class="badge bg-warning">Pausado</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($usuario->pivot->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Data de criação:</div>
                        <div class="col-md-9">{{ $usuario->pivot->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Última atualização:</div>
                        <div class="col-md-9">{{ $usuario->pivot->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Observações:</div>
                        <div class="col-md-9">{{ $usuario->pivot->notes ?? 'Sem observações' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 fw-bold">Configurações:</div>
                        <div class="col-md-9">
                            <pre class="mb-0 bg-light p-2 rounded"><code>{{ json_encode($usuario->pivot->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registros DNS ({{ $domain->records_count }})</h5>
                    <a href="{{ route('admin.domains.records', [$domain->external_api_id, $domain->zone_id]) }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if($domain->records && $domain->records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome</th>
                                        <th>Conteúdo</th>
                                        <th>TTL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($domain->records->take(5) as $record)
                                        <tr>
                                            <td><span class="badge bg-info">{{ $record->type }}</span></td>
                                            <td>{{ $record->name }}</td>
                                            <td class="text-truncate" style="max-width: 200px;">{{ $record->content }}</td>
                                            <td>{{ $record->ttl === 1 ? 'Auto' : $record->ttl }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($domain->records->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">Exibindo 5 de {{ $domain->records->count() }} registros</small>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            Nenhum registro DNS encontrado para este domínio.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.cloudflare.domain-associations.edit', [$domain->id, $usuario->id]) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Associação
                        </a>
                        <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$domain->id, $usuario->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta associação?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Excluir Associação
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações de Acesso</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Status atual:</strong>
                        @if($usuario->pivot->status === 'active')
                            <span class="badge bg-success">Ativo</span>
                            <br><small class="text-muted">O usuário pode visualizar e editar registros DNS.</small>
                        @elseif($usuario->pivot->status === 'paused')
                            <span class="badge bg-warning">Pausado</span>
                            <br><small class="text-muted">O usuário pode apenas visualizar registros DNS.</small>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($usuario->pivot->status) }}</span>
                            <br><small class="text-muted">O usuário não tem acesso aos registros DNS.</small>
                        @endif
                    </p>
                    <hr>
                    <p class="card-text">
                        <i class="fas fa-info-circle text-primary"></i> Os registros DNS serão sincronizados automaticamente conforme as configurações do sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
