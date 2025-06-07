@extends('layouts.app')

@section('title', 'Informações Bancárias')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Informações Bancárias</h1>
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
                <h5 class="mb-0">Lista de Informações Bancárias</h5>
            </div>
        </div>
        <div class="card-body">
            @if($informacoes->isEmpty())
                <div class="alert alert-info">
                    Nenhuma informação bancária registrada.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Agência/Conta</th>
                                <th>Telefone</th>
                                <th>Visitante</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informacoes as $info)
                            <tr>
                                <td>{{ $info->id }}</td>
                                <td>{{ $info->data ? \Carbon\Carbon::parse($info->data)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $info->cpf ?: 'N/A' }}</td>
                                <td>{{ $info->nome_completo ?: 'N/A' }}</td>
                                <td>
                                    @if($info->agencia || $info->conta)
                                        {{ $info->agencia }} / {{ $info->conta }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $info->telefone ?: 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('cliente.visitantes.show', $info->visitante->id) }}">
                                        #{{ $info->visitante->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('cliente.informacoes.show', $info->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $informacoes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
