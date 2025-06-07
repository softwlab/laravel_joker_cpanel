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
                                <th>Link</th>
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
                                    @if($visitante->linkItem)
                                        <a href="{{ $visitante->linkItem->url }}" target="_blank">
                                            {{ $visitante->linkItem->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">Link removido</span>
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
