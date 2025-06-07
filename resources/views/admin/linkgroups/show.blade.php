@extends('layouts.admin')

@section('title', 'Detalhes do Grupo Organizado')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalhes do Grupo Organizado: {{ $group->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.linkgroups.edit', $group->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.linkgroups.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <h5 class="mb-0">Informações do Grupo</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Nome:</div>
                        <div class="col-md-9">{{ $group->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Usuário:</div>
                        <div class="col-md-9">
                            <a href="{{ route('admin.users.show', $group->usuario->id) }}">
                                {{ $group->usuario->nome }} ({{ $group->usuario->email }})
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Status:</div>
                        <div class="col-md-9">
                            @if($group->active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Data de criação:</div>
                        <div class="col-md-9">{{ $group->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Última atualização:</div>
                        <div class="col-md-9">{{ $group->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    @if($group->description)
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Descrição:</div>
                        <div class="col-md-9">{{ $group->description }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Links Bancários no Grupo ({{ $group->items->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($group->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Posição</th>
                                        <th>Nome</th>
                                        <th>Instituição Bancária</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group->items->sortBy('position') as $item)
                                        <tr>
                                            <td>{{ $item->position }}</td>
                                            <td>{{ $item->bank->name }}</td>
                                            <td>{{ $item->bank->template->name ?? 'Sem instituição bancária' }}</td>
                                            <td>
                                                @if($item->bank->active)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.banks.show', $item->bank->id) }}" class="btn btn-sm btn-info">
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
                            Este grupo não contém links bancários.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.linkgroups.edit', $group->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Grupo
                        </a>
                        <form action="{{ route('admin.linkgroups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este grupo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Excluir Grupo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
