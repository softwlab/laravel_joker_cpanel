@extends('layouts.admin')

@section('title', 'Gerenciar Assinaturas')


@section('content')
<div class="d-flex justify-content-between">
        <h1>Gerenciar Assinaturas</h1>
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Nova Assinatura
        </a>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Assinaturas</h3>
            <div class="card-tools">
                <form action="{{ route('admin.subscriptions.index') }}" method="GET" class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Buscar" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>UUID</th>
                        <th>Usuário</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Dias Restantes</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr class="{{ $subscription->hasExpired() ? 'table-danger' : ($subscription->isActive() ? 'table-success' : 'table-warning') }}">
                            <td>{{ $subscription->id }}</td>
                            <td>{{ $subscription->uuid }}</td>
                            <td>{{ $subscription->user->name ?? 'N/A' }}</td>
                            <td>{{ $subscription->name }}</td>
                            <td>{{ number_format($subscription->value, 2, ',', '.') }}</td>
                            <td>
                                @if ($subscription->status === 'active')
                                    <span class="badge badge-success">Ativo</span>
                                @elseif ($subscription->status === 'inactive')
                                    <span class="badge badge-warning">Inativo</span>
                                @elseif ($subscription->status === 'expired')
                                    <span class="badge badge-danger">Expirado</span>
                                @endif
                            </td>
                            <td>{{ $subscription->start_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $subscription->end_date->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($subscription->hasExpired())
                                    <span class="text-danger">Expirado</span>
                                @else
                                    {{ $subscription->getRemainingDays() }} dias
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="event.preventDefault(); 
                                        if(confirm('Tem certeza que deseja excluir esta assinatura?')) {
                                            document.getElementById('delete-form-{{ $subscription->id }}').submit();
                                        }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $subscription->id }}" action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Nenhuma assinatura encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $subscriptions->links() }}
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td, .table th {
            vertical-align: middle;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Página de assinaturas carregada!');
    </script>
@stop
