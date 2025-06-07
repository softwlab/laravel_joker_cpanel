@extends('layouts.admin')

@section('title', 'APIs Externas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-cloud"></i> APIs Externas</h1>
        <a href="{{ route('admin.external-apis.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova API
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Registros DNS</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apis as $api)
                            <tr>
                                <td>{{ $api->id }}</td>
                                <td>{{ $api->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ strtoupper($api->type) }}</span>
                                </td>
                                <td>
                                    @if ($api->status === 'active')
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $api->dns_records_count }} registros</span>
                                </td>
                                <td>{{ $api->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.external-apis.show', $api->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.external-apis.edit', $api->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.external-apis.create-record', $api->id) }}" class="btn btn-sm btn-primary" title="Adicionar Registro DNS">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success test-connection" data-bs-toggle="modal" 
                                                data-bs-target="#testConnectionModal" data-api-id="{{ $api->id }}" title="Testar Conexão">
                                            <i class="fas fa-plug"></i>
                                        </button>
                                        <form action="{{ route('admin.external-apis.destroy', $api->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta API externa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma API externa cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $apis->links() }}
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
        const buttons = document.querySelectorAll('.test-connection');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const apiId = this.getAttribute('data-api-id');
                const testResult = document.getElementById('testResult');
                const spinner = document.getElementById('testingSpinner');
                
                testResult.innerHTML = '';
                spinner.style.display = 'inline-block';
                
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
    });
</script>
@endsection
