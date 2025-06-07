@extends('layouts.admin')

@section('title', 'Associações de Domínios Cloudflare')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Associações de Domínios Cloudflare</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.cloudflare.domain-associations.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus"></i> Nova Associação
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Domínio</th>
                            <th scope="col">Usuário</th>
                            <th scope="col">Status</th>
                            <th scope="col">Criado em</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($associations as $association)
                        <tr>
                            <td>{{ $association->domain->name }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $association->usuario->id) }}">
                                    {{ $association->usuario->nome }}
                                </a>
                            </td>
                            <td>
                                @if($association->pivot->status === 'active')
                                    <span class="badge bg-success">Ativo</span>
                                @elseif($association->pivot->status === 'paused')
                                    <span class="badge bg-warning">Pausado</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($association->pivot->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $association->pivot->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.cloudflare.domain-associations.show', [$association->domain->id, $association->usuario->id]) }}" class="btn btn-sm btn-info" title="Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.cloudflare.domain-associations.edit', [$association->domain->id, $association->usuario->id]) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$association->domain->id, $association->usuario->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja remover esta associação?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma associação encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($associations) && $associations->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $associations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
