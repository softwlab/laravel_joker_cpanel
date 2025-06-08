@extends('layouts.admin')

@section('title', 'Gerenciamento de Chaves API Públicas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Chaves API Públicas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.api_keys.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus"></i> Nova Chave API
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
                            <th scope="col">Nome</th>
                            <th scope="col">Chave (Fragmento)</th>
                            <th scope="col">Status</th>
                            <th scope="col">Última Utilização</th>
                            <th scope="col">Logs</th>
                            <th scope="col">Criado em</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiKeys as $apiKey)
                            <tr>
                                <td>{{ $apiKey->name }}</td>
                                <td><code>{{ substr($apiKey->key, 0, 16) }}...</code></td>
                                <td>
                                    @if($apiKey->active)
                                        <span class="badge bg-success">Ativa</span>
                                    @else
                                        <span class="badge bg-danger">Inativa</span>
                                    @endif
                                </td>
                                <td>
                                    @if($apiKey->last_used_at)
                                        {{ $apiKey->last_used_at->format('d/m/Y H:i') }}
                                    @else
                                        <em class="text-muted">Nunca utilizada</em>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $apiKey->logs_count }}</span>
                                </td>
                                <td>{{ $apiKey->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.api_keys.show', $apiKey->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.api_keys.edit', $apiKey->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.api_keys.destroy', $apiKey->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta chave API?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma chave API pública encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
