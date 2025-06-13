@extends('layouts.cliente')

@section('title', 'Minhas Assinaturas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Minhas Assinaturas</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <span>Lista de Assinaturas</span>
                <div>
                    <form action="{{ route('cliente.subscriptions.index') }}" method="GET" class="d-flex">
                        <select name="status" class="form-select me-2" onchange="this.form.submit()">
                            <option value="all" {{ request()->status == 'all' || !request()->status ? 'selected' : '' }}>Todos os status</option>
                            <option value="active" {{ request()->status == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request()->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            <option value="expired" {{ request()->status == 'expired' ? 'selected' : '' }}>Expirado</option>
                        </select>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request()->search }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Data de Início</th>
                                <th>Data de Término</th>
                                <th>Registros DNS</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->name }}</td>
                                    <td>
                                        @if($subscription->status == 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($subscription->status == 'inactive')
                                            <span class="badge bg-warning">Inativo</span>
                                        @else
                                            <span class="badge bg-danger">Expirado</span>
                                        @endif
                                    </td>
                                    <td>R$ {{ number_format($subscription->value, 2, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d/m/Y') }}</td>
                                    <td>{{ $subscription->dnsRecords->count() }}</td>
                                    <td>
                                        <a href="{{ route('cliente.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $subscriptions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Você ainda não possui nenhuma assinatura.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
