@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalhes do Usuário: {{ $user->nome }}</h1>
    <div class="btn-toolbar">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit"></i> Editar
        </a>
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Excluir
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informações Básicas</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">ID:</span>
                        <span>{{ $user->id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Nome:</span>
                        <span>{{ $user->nome }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">E-mail:</span>
                        <span>{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Nível:</span>
                        <span>
                            <span class="badge bg-{{ $user->nivel === 'admin' ? 'danger' : 'info' }}">
                                {{ ucfirst($user->nivel) }}
                            </span>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Status:</span>
                        <span>
                            @if($user->ativo)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Data de Criação:</span>
                        <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Última Atualização:</span>
                        <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        @if($user->userConfig)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Configurações do Usuário</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Tema:</span>
                        <span>{{ $user->userConfig->theme ?? 'Padrão' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Receber Notificações:</span>
                        <span>
                            @if($user->userConfig->notifications ?? false)
                                <span class="badge bg-success">Sim</span>
                            @else
                                <span class="badge bg-secondary">Não</span>
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bancos do Usuário</h5>
                <span class="badge bg-primary">{{ $user->banks->count() }}</span>
            </div>
            <div class="card-body">
                @if($user->banks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Template</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->banks as $bank)
                                <tr>
                                    <td>{{ $bank->id }}</td>
                                    <td>{{ $bank->name }}</td>
                                    <td>{{ $bank->template->name ?? 'Sem template' }}</td>
                                    <td>
                                        @if($bank->active)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.banks.show', $bank->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Este usuário não possui bancos cadastrados.
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Histórico de Acessos</h5>
                <span class="badge bg-primary">{{ $user->acessos->count() }}</span>
            </div>
            <div class="card-body">
                @if($user->acessos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>IP</th>
                                    <th>Navegador</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->acessos->sortByDesc('created_at')->take(10) as $acesso)
                                <tr>
                                    <td>{{ $acesso->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $acesso->ip }}</td>
                                    <td>{{ Str::limit($acesso->user_agent, 40) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->acessos->count() > 10)
                        <div class="text-center mt-2">
                            <small class="text-muted">Exibindo os 10 acessos mais recentes</small>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info mb-0">
                        Nenhum registro de acesso para este usuário.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
