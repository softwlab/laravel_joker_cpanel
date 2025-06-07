@extends('layouts.admin')

@section('title', 'Detalhes da API Externa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-cloud"></i> {{ $api->name }}</h1>
        <div>
            <a href="{{ route('admin.external-apis.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para a lista
            </a>
            <a href="{{ route('admin.external-apis.edit', $api->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('admin.external-apis.create-record', $api->id) }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Novo Registro DNS
            </a>
            <button type="button" class="btn btn-success" id="testConnection" data-api-id="{{ $api->id }}">
                <i class="fas fa-plug"></i> Testar Conexão
            </button>
            @if($api->type == 'cloudflare')
            <a href="{{ route('admin.external-apis.domains', $api->id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-globe"></i> Listar Domínios
            </a>
            <a href="{{ route('admin.external-apis.edit-config', $api->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-cog"></i> Configurar API
            </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>ID:</th>
                                <td>{{ $api->id }}</td>
                            </tr>
                            <tr>
                                <th>Nome:</th>
                                <td>{{ $api->name }}</td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    <span class="badge bg-info">{{ strtoupper($api->type) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if ($api->status === 'active')
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Descrição:</th>
                                <td>{{ $api->description ?? 'Nenhuma descrição fornecida' }}</td>
                            </tr>
                            <tr>
                                <th>Criado em:</th>
                                <td>{{ $api->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Atualizado em:</th>
                                <td>{{ $api->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configuração da API</h5>
                </div>
                <div class="card-body">
                    @if ($api->type === 'cloudflare')
                        <table class="table">
                            <tbody>
                                @if (isset($api->config['cloudflare_email']))
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $api->config['cloudflare_email'] }}</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['cloudflare_api_key']))
                                    <tr>
                                        <th>API Key:</th>
                                        <td>•••••••••••••••••</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['cloudflare_api_token']))
                                    <tr>
                                        <th>API Token:</th>
                                        <td>•••••••••••••••••</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['cloudflare_zone_id']))
                                    <tr>
                                        <th>Zone ID padrão:</th>
                                        <td>{{ $api->config['cloudflare_zone_id'] }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @elseif ($api->type === 'aws_route53')
                        <table class="table">
                            <tbody>
                                @if (isset($api->config['aws_access_key']))
                                    <tr>
                                        <th>Access Key ID:</th>
                                        <td>{{ $api->config['aws_access_key'] }}</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['aws_secret_key']))
                                    <tr>
                                        <th>Secret Key:</th>
                                        <td>•••••••••••••••••</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['aws_region']))
                                    <tr>
                                        <th>Região:</th>
                                        <td>{{ $api->config['aws_region'] }}</td>
                                    </tr>
                                @endif
                                
                                @if (isset($api->config['aws_zone_id']))
                                    <tr>
                                        <th>Hosted Zone ID:</th>
                                        <td>{{ $api->config['aws_zone_id'] }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">
                            Configurações personalizadas armazenadas em formato JSON.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estatísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h3>{{ $api->dns_records_count }}</h3>
                                <p>Registros DNS</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h3>{{ $api->related_items_count }}</h3>
                                <p>Itens Relacionados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Registros DNS</h5>
            <div>
                <a href="{{ route('admin.external-apis.create-record', $api->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Novo Registro
                </a>
                <form action="{{ route('admin.dns-records.sync', $api->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Sincronizar com API
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($dns_records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>Conteúdo</th>
                                <th>TTL</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dns_records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $record->record_type }}</span>
                                    </td>
                                    <td>{{ $record->name }}</td>
                                    <td>
                                        <code>{{ Str::limit($record->content, 30) }}</code>
                                    </td>
                                    <td>{{ $record->ttl ?? 'Auto' }}</td>
                                    <td>
                                        @if ($record->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.dns-records.show', $record->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.dns-records.edit', $record->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.external-apis.delete-record', [$api->id, $record->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este registro DNS?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $dns_records->links() }}
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nenhum registro DNS encontrado para esta API externa.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Teste de Conexão -->
<div class="modal fade" id="testConnectionModal" tabindex="-1" aria-labelledby="testConnectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testConnectionModalLabel">Teste de Conexão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status" id="testingSpinner">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div id="testResult" class="mt-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Teste de conexão com API
        const testBtn = document.getElementById('testConnection');
        const modal = new bootstrap.Modal(document.getElementById('testConnectionModal'));
        
        testBtn.addEventListener('click', function() {
            const apiId = this.getAttribute('data-api-id');
            const testResult = document.getElementById('testResult');
            const spinner = document.getElementById('testingSpinner');
            
            testResult.innerHTML = '';
            spinner.style.display = 'inline-block';
            modal.show();
            
            fetch(`{{ url('admin/external-apis') }}/${apiId}/test-connection`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                if (data.success) {
                    testResult.innerHTML = `<div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ${data.message}
                    </div>`;
                } else {
                    testResult.innerHTML = `<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${data.message}
                    </div>`;
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                testResult.innerHTML = `<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Erro ao testar conexão: ${error}
                </div>`;
            });
        });
    });
</script>
@endsection
