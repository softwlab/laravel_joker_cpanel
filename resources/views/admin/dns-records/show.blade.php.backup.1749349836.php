@extends('layouts.admin')

@section('title', 'Detalhes do Registro DNS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-globe"></i> Registro DNS: {{ $record->record_type }}</h1>
        <div>
            <a href="{{ route('admin.dns-records.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para lista
            </a>
            <a href="{{ route('admin.dns-records.edit', $record->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('admin.dns-records.destroy', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este registro DNS?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Registro DNS</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th width="30%">ID:</th>
                                        <td>{{ $record->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
                                        <td><span class="badge bg-secondary">{{ $record->record_type }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Nome:</th>
                                        <td>{{ $record->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Conteúdo:</th>
                                        <td>
                                            <code class="d-block p-2 bg-light">{{ $record->content }}</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>TTL:</th>
                                        <td>{{ $record->ttl ?? 'Auto' }} segundos</td>
                                    </tr>
                                    @if($record->priority)
                                        <tr>
                                            <th>Prioridade:</th>
                                            <td>{{ $record->priority }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th width="30%">API:</th>
                                        <td>
                                            <a href="{{ route('admin.external-apis.show', $record->externalApi->id) }}" class="badge bg-info text-decoration-none">
                                                {{ $record->externalApi->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tipo de API:</th>
                                        <td>{{ strtoupper($record->externalApi->type) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if ($record->status === 'active')
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-danger">Inativo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Criado em:</th>
                                        <td>{{ $record->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Atualizado em:</th>
                                        <td>{{ $record->updated_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($record->externalApi->type === 'cloudflare' && !empty($record->extra_data))
                        <div class="mt-4">
                            <h5>Dados específicos do Cloudflare</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @if(isset($record->extra_data['zone_id']))
                                            <tr>
                                                <th width="20%">Zone ID:</th>
                                                <td>{{ $record->extra_data['zone_id'] }}</td>
                                            </tr>
                                        @endif
                                        @if(isset($record->extra_data['record_id']))
                                            <tr>
                                                <th>Record ID:</th>
                                                <td>{{ $record->extra_data['record_id'] }}</td>
                                            </tr>
                                        @endif
                                        @if(isset($record->extra_data['proxied']))
                                            <tr>
                                                <th>Proxiado:</th>
                                                <td>
                                                    @if($record->extra_data['proxied'])
                                                        <span class="badge bg-success">Sim</span>
                                                    @else
                                                        <span class="badge bg-secondary">Não</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Associações</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @if($record->bank)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="fas fa-link"></i> Link Bancário:</strong>
                                    </div>
                                    <a href="{{ route('admin.banks.show', $record->bank->id) }}" class="btn btn-sm btn-outline-primary">
                                        {{ $record->bank->name }}
                                    </a>
                                </div>
                            </li>
                        @endif
                        
                        @if($record->bankTemplate)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="fas fa-landmark"></i> Template:</strong>
                                    </div>
                                    <a href="{{ route('admin.templates.edit', $record->bankTemplate->id) }}" class="btn btn-sm btn-outline-info">
                                        {{ $record->bankTemplate->name }}
                                    </a>
                                </div>
                            </li>
                        @endif
                        
                        @if($record->linkGroup)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="fas fa-object-group"></i> Grupo:</strong>
                                    </div>
                                    <span class="btn btn-sm btn-outline-warning">
                                        {{ $record->linkGroup->name }}
                                    </span>
                                </div>
                            </li>
                        @endif
                        
                        @if($record->user)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="fas fa-user"></i> Usuário:</strong>
                                    </div>
                                    <a href="{{ route('admin.users.show', $record->user->id) }}" class="btn btn-sm btn-outline-dark">
                                        {{ $record->user->nome }}
                                    </a>
                                </div>
                            </li>
                        @endif
                        
                        @if(!$record->bank && !$record->bankTemplate && !$record->linkGroup && !$record->user)
                            <li class="list-group-item text-center text-muted">
                                <i class="fas fa-info-circle"></i> Nenhuma associação encontrada
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.dns-records.edit', $record->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Registro
                        </a>
                        <a href="{{ route('admin.external-apis.show', $record->externalApi->id) }}" class="btn btn-info">
                            <i class="fas fa-cloud"></i> Ver API {{ $record->externalApi->name }}
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#dnsInfoModal">
                            <i class="fas fa-info-circle"></i> Informações do Registro
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal com informações de DNS -->
<div class="modal fade" id="dnsInfoModal" tabindex="-1" aria-labelledby="dnsInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dnsInfoModalLabel">Informações sobre Registros DNS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <h5>Registro Tipo {{ $record->record_type }}</h5>
                
                @switch($record->record_type)
                    @case('A')
                        <p>O registro <strong>A</strong> (Address) mapeia um nome de domínio para um endereço IPv4.</p>
                        <p>Exemplo: <code>example.com → 192.168.1.1</code></p>
                        <p>Este registro está apontando <code>{{ $record->name }}</code> para o IP <code>{{ $record->content }}</code>.</p>
                        @break
                        
                    @case('CNAME')
                        <p>O registro <strong>CNAME</strong> (Canonical Name) mapeia um nome de domínio para outro nome de domínio.</p>
                        <p>Exemplo: <code>www.example.com → example.com</code></p>
                        <p>Este registro está apontando <code>{{ $record->name }}</code> para <code>{{ $record->content }}</code>.</p>
                        @break
                        
                    @case('MX')
                        <p>O registro <strong>MX</strong> (Mail Exchange) especifica o servidor de email responsável pelo domínio.</p>
                        <p>Exemplo: <code>example.com → mail.example.com</code> com prioridade 10</p>
                        <p>Este registro está configurando <code>{{ $record->name }}</code> para usar o servidor de email <code>{{ $record->content }}</code> com prioridade {{ $record->priority ?? 'não definida' }}.</p>
                        @break
                        
                    @case('TXT')
                        <p>O registro <strong>TXT</strong> (Text) armazena texto que pode ser usado para vários propósitos, incluindo verificação de domínio e configurações SPF.</p>
                        <p>Este registro contém o texto: <code>{{ $record->content }}</code></p>
                        @break
                        
                    @default
                        <p>Registro DNS do tipo <strong>{{ $record->record_type }}</strong>.</p>
                        <p>Nome: <code>{{ $record->name }}</code></p>
                        <p>Conteúdo: <code>{{ $record->content }}</code></p>
                @endswitch
                
                <hr>
                
                <h5>TTL (Time To Live)</h5>
                <p>TTL determina por quanto tempo (em segundos) os servidores DNS devem armazenar em cache este registro.</p>
                <p>Este registro tem um TTL de {{ $record->ttl ?? 'Auto' }} segundos ({{ $record->ttl ? round($record->ttl / 60) . ' minutos' : 'automático' }}).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection
