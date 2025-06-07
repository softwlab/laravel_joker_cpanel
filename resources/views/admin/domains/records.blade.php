@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registros DNS: {{ $domainInfo->name }}</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.external-apis.index') }}">APIs Externas</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.external-apis.show', $api->id) }}">{{ $api->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.external-apis.domains', $api->id) }}">Domínios</a></li>
        <li class="breadcrumb-item active">Registros DNS de {{ $domainInfo->name }}</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif
    
    <!-- Card de estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $dnsRecords->total() }}</h4>
                            <div>Total de Registros DNS</div>
                        </div>
                        <div>
                            <i class="fas fa-dns fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $dnsRecords->where('record_type', 'A')->count() }}</h4>
                            <div>Registros A</div>
                        </div>
                        <div>
                            <i class="fas fa-globe fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $dnsRecords->where('record_type', 'MX')->count() }}</h4>
                            <div>Registros MX</div>
                        </div>
                        <div>
                            <i class="fas fa-envelope fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $dnsRecords->where('record_type', 'TXT')->count() }}</h4>
                            <div>Registros TXT</div>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Gerenciar registros DNS</h5>
                        </div>
                        <div class="btn-group">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#addRecordModal" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Novo Registro
                            </a>
                            <form action="{{ route('admin.domains.sync', ['apiId' => $api->id, 'zoneId' => $domainInfo->zone_id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info ms-2">
                                    <i class="fas fa-sync"></i> Sincronizar Registros
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabela de registros DNS -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Registros DNS para {{ $domainInfo->name }}
        </div>
        <div class="card-body">
            @if($dnsRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>Conteúdo</th>
                                <th>TTL</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dnsRecords as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td><span class="badge bg-secondary">{{ $record->record_type }}</span></td>
                                    <td>{{ $record->name }}</td>
                                    <td>{{ $record->content }}</td>
                                    <td>{{ $record->ttl }}</td>
                                    <td>
                                        @if($record->status == 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-primary edit-record" 
                                               data-record-id="{{ $record->id }}"
                                               data-record-type="{{ $record->record_type }}"
                                               data-record-name="{{ $record->name }}"
                                               data-record-content="{{ $record->content }}"
                                               data-record-ttl="{{ $record->ttl }}"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editRecordModal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.dns-records.destroy', $record->id) }}" method="POST" class="d-inline delete-record-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este registro?')">
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $dnsRecords->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nenhum registro DNS encontrado para este domínio.
                </div>
            @endif
        </div>
    </div>
    
    <!-- Informações do Domínio -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i> Informações do Domínio
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nome:</strong> {{ $domainInfo->name }}</p>
                    <p><strong>ID da Zona:</strong> {{ $domainInfo->zone_id }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        @if($domainInfo->status == 'active')
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-warning">{{ $domainInfo->status }}</span>
                        @endif
                    </p>
                    <p>
                        <strong>Ghost:</strong> 
                        @if($domainInfo->is_ghost)
                            <span class="badge bg-danger">Sim</span>
                        @else
                            <span class="badge bg-success">Não</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Registro -->
<div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecordModalLabel">Adicionar Registro DNS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="{{ route('admin.dns-records.store', $api->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="zone_id" value="{{ $domainInfo->zone_id }}">
                    
                    <div class="mb-3">
                        <label for="record_type" class="form-label">Tipo de Registro</label>
                        <select class="form-select" id="record_type" name="record_type" required>
                            <option value="">Selecione um tipo</option>
                            @foreach($recordTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="subdominio" required>
                            <span class="input-group-text">.{{ $domainInfo->name }}</span>
                        </div>
                        <small class="text-muted">Para o domínio raiz, use "@"</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Conteúdo</label>
                        <input type="text" class="form-control" id="content" name="content" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ttl" class="form-label">TTL (segundos)</label>
                        <input type="number" class="form-control" id="ttl" name="ttl" value="3600" min="60">
                    </div>
                    
                    <div class="mb-3" id="priority-field" style="display:none;">
                        <label for="priority" class="form-label">Prioridade</label>
                        <input type="number" class="form-control" id="priority" name="priority" value="10" min="0">
                        <small class="text-muted">Usado apenas para registros MX</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Registro -->
<div class="modal fade" id="editRecordModal" tabindex="-1" aria-labelledby="editRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRecordModalLabel">Editar Registro DNS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="editRecordForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_record_type" class="form-label">Tipo de Registro</label>
                        <input type="text" class="form-control" id="edit_record_type" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Conteúdo</label>
                        <input type="text" class="form-control" id="edit_content" name="content" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ttl" class="form-label">TTL (segundos)</label>
                        <input type="number" class="form-control" id="edit_ttl" name="ttl" min="60">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/esconder campo de prioridade baseado no tipo de registro
        const recordTypeSelect = document.getElementById('record_type');
        const priorityField = document.getElementById('priority-field');
        
        if (recordTypeSelect && priorityField) {
            recordTypeSelect.addEventListener('change', function() {
                if (this.value === 'MX') {
                    priorityField.style.display = 'block';
                } else {
                    priorityField.style.display = 'none';
                }
            });
        }
        
        // Configurar o modal de edição
        const editRecordButtons = document.querySelectorAll('.edit-record');
        
        editRecordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const recordId = this.getAttribute('data-record-id');
                const recordType = this.getAttribute('data-record-type');
                const recordName = this.getAttribute('data-record-name');
                const recordContent = this.getAttribute('data-record-content');
                const recordTtl = this.getAttribute('data-record-ttl');
                
                document.getElementById('edit_record_type').value = recordType;
                document.getElementById('edit_name').value = recordName;
                document.getElementById('edit_content').value = recordContent;
                document.getElementById('edit_ttl').value = recordTtl;
                
                // Atualizar a URL do formulário
                const editForm = document.getElementById('editRecordForm');
                editForm.action = `/admin/dns-records/${recordId}/update`;
            });
        });
    });
</script>
@endsection
