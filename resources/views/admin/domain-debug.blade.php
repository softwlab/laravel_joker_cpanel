@extends('layouts.app')

@section('title', 'Diagnóstico de Domínios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Diagnóstico de Associações de Domínios</h2>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informações do Usuário</h5>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Nome:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Associações na Tabela Pivot</h5>
        </div>
        <div class="card-body">
            <h6>Consulta Direta na Tabela cloudflare_domain_usuario:</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CloudflareDomain ID</th>
                            <th>Usuário ID</th>
                            <th>Status</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pivotData->count() > 0)
                            @foreach($pivotData as $pivot)
                            <tr>
                                <td>{{ $pivot->id }}</td>
                                <td>{{ $pivot->cloudflare_domain_id }}</td>
                                <td>{{ $pivot->usuario_id }}</td>
                                <td>{{ $pivot->status }}</td>
                                <td>{{ $pivot->created_at }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Nenhum registro encontrado na tabela pivot</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Domínios Disponíveis</h5>
        </div>
        <div class="card-body">
            <h6>Todos os domínios Cloudflare no sistema:</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>API ID</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($allDomains->count() > 0)
                            @foreach($allDomains as $domain)
                            <tr>
                                <td>{{ $domain->id }}</td>
                                <td>{{ $domain->name }}</td>
                                <td>{{ $domain->external_api_id }}</td>
                                <td>{{ $domain->status }}</td>
                                <td>
                                    <form action="/admin/debug/associate-domain" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="usuario_id" value="{{ $user->id }}">
                                        <input type="hidden" name="cloudflare_domain_id" value="{{ $domain->id }}">
                                        <button type="submit" class="btn btn-sm btn-success">Associar</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Nenhum domínio Cloudflare encontrado no sistema</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Registros DNS</h5>
        </div>
        <div class="card-body">
            <h6>Registros DNS associados ao usuário {{ $user->id }}:</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Conteúdo</th>
                            <th>API ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($userDnsRecords->count() > 0)
                            @foreach($userDnsRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->record_type }}</td>
                                <td>{{ $record->name }}</td>
                                <td>{{ $record->content }}</td>
                                <td>{{ $record->external_api_id }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Nenhum registro DNS encontrado para este usuário</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
