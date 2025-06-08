@extends('layouts.admin')

@section('title', 'Detalhes da Chave API')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalhes da Chave API: {{ $apiKey->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.api_keys.index') }}" class="btn btn-sm btn-outline-secondary">
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
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    Informações da Chave
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $apiKey->name }}</dd>
                        
                        <dt class="col-sm-4">Chave Completa:</dt>
                        <dd class="col-sm-8">
                            <div class="input-group">
                                <input type="text" class="form-control" id="apiKeyValue" value="{{ $apiKey->key }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">Clique no botão para copiar</small>
                        </dd>
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($apiKey->active)
                                <span class="badge bg-success">Ativa</span>
                            @else
                                <span class="badge bg-danger">Inativa</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4">Criada em:</dt>
                        <dd class="col-sm-8">{{ $apiKey->created_at->format('d/m/Y H:i:s') }}</dd>
                        
                        <dt class="col-sm-4">Última utilização:</dt>
                        <dd class="col-sm-8">
                            @if($apiKey->last_used_at)
                                {{ $apiKey->last_used_at->format('d/m/Y H:i:s') }}
                            @else
                                <em class="text-muted">Nunca utilizada</em>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4">Descrição:</dt>
                        <dd class="col-sm-8">
                            @if($apiKey->description)
                                {{ $apiKey->description }}
                            @else
                                <em class="text-muted">Sem descrição</em>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.api_keys.edit', $apiKey->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateKeyModal">
                                <i class="fas fa-sync"></i> Regenerar Chave
                            </button>
                            
                            <form action="{{ route('admin.api_keys.destroy', $apiKey->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta chave API?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ms-2">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    Como usar esta chave
                </div>
                <div class="card-body">
                    <p>Para autenticar em endpoints da API, adicione o seguinte cabeçalho HTTP em suas requisições:</p>
                    
                    <pre><code>X-API-KEY: {{ $apiKey->key }}</code></pre>
                    
                    <p>Exemplo de uso com cURL:</p>
                    
                    <pre><code>curl -X GET \
    -H "X-API-KEY: {{ $apiKey->key }}" \
    "{{ url('/api/public/domain_external/exemplo.com') }}"</code></pre>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span>Histórico de Atividades</span>
                    <span class="badge bg-light text-dark">{{ $logs->total() }} registros</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Ação</th>
                                    <th>Administrador</th>
                                    <th>Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <span class="badge {{ $log->action == 'created' ? 'bg-success' : ($log->action == 'deleted' ? 'bg-danger' : 'bg-info') }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->admin)
                                                {{ $log->admin->name }}
                                            @else
                                                <em>Sistema</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->details)
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#logDetailsModal" 
                                                        data-log-details="{{ $log->details }}">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                            @else
                                                <em>-</em>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum registro de atividade encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para regenerar chave -->
<div class="modal fade" id="regenerateKeyModal" tabindex="-1" aria-labelledby="regenerateKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="regenerateKeyModalLabel">Regenerar Chave API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <strong>ATENÇÃO!</strong>
                    <p>Regenerar a chave irá invalidar a chave atual. Todos os sistemas que utilizam esta chave precisarão ser atualizados.</p>
                    <p>Esta ação não pode ser desfeita.</p>
                </div>
                <p>Deseja realmente regenerar a chave API <strong>"{{ $apiKey->name }}"</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('admin.api_keys.regenerate', $apiKey->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning">Regenerar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir detalhes de logs -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="logDetailsModalLabel">Detalhes do Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="logDetailsContent" class="bg-light p-3"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function copyToClipboard() {
        var copyText = document.getElementById("apiKeyValue");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Feedback visual
        var button = copyText.nextElementSibling;
        var originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        setTimeout(function(){
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }
    
    // Configurar modal de detalhes
    document.getElementById('logDetailsModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var logDetails = button.getAttribute('data-log-details');
        var detailsContent = document.getElementById('logDetailsContent');
        
        try {
            // Tentar formatar como JSON
            var formattedDetails = JSON.stringify(JSON.parse(logDetails), null, 2);
            detailsContent.textContent = formattedDetails;
        } catch (e) {
            // Se não for JSON, exibir como texto simples
            detailsContent.textContent = logDetails;
        }
    });
</script>
@endpush
