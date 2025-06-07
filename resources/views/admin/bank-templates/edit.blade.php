@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Editar Template de Banco</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar {{ $template->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.bank-templates.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Nome do Template *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="template_url">URL do Template</label>
                            <input type="url" class="form-control @error('template_url') is-invalid @enderror" id="template_url" name="template_url" value="{{ old('template_url', $template->template_url) }}">
                            <small class="form-text text-muted">URL para a página de login do banco</small>
                            @error('template_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="text" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" value="{{ old('logo', $template->logo) }}">
                            <small class="form-text text-muted">URL da imagem ou nome do ícone</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" {{ $template->active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="active">Ativo</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <a href="{{ route('admin.bank-templates.index') }}" class="btn btn-secondary">Voltar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Campos do Template</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addFieldModal">
                        <i class="fas fa-plus"></i> Adicionar Campo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Label</th>
                                    <th>Tipo</th>
                                    <th>Req.</th>
                                    <th>Status</th>
                                    <th>Ordem</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($template->fields->sortBy('order') as $field)
                                    <tr>
                                        <td>{{ $field->field_name }}</td>
                                        <td>{{ $field->field_label }}</td>
                                        <td>{{ $field->field_type }}</td>
                                        <td>
                                            @if($field->required)
                                                <span class="badge badge-success">Sim</span>
                                            @else
                                                <span class="badge badge-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($field->active)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>{{ $field->order }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary edit-field-btn" 
                                                    data-field="{{ json_encode($field) }}"
                                                    data-toggle="modal" data-target="#editFieldModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.bank-templates.delete-field', ['id' => $template->id, 'fieldId' => $field->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este campo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum campo definido</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar campo -->
<div class="modal fade" id="addFieldModal" tabindex="-1" role="dialog" aria-labelledby="addFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFieldModalLabel">Adicionar Campo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.bank-templates.add-field', $template->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="field_name">Nome do Campo *</label>
                        <input type="text" class="form-control" id="field_name" name="field_name" required>
                        <small class="form-text text-muted">Nome técnico do campo (sem espaços, ex: agencia, conta, email)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="field_label">Label do Campo *</label>
                        <input type="text" class="form-control" id="field_label" name="field_label" required>
                        <small class="form-text text-muted">Texto exibido para o usuário</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="field_type">Tipo de Campo *</label>
                        <select class="form-control" id="field_type" name="field_type" required>
                            <option value="text">Texto</option>
                            <option value="email">Email</option>
                            <option value="number">Número</option>
                            <option value="password">Senha</option>
                            <option value="date">Data</option>
                            <option value="tel">Telefone</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="placeholder">Placeholder</label>
                        <input type="text" class="form-control" id="placeholder" name="placeholder">
                    </div>
                    
                    <div class="form-group">
                        <label for="order">Ordem</label>
                        <input type="number" class="form-control" id="order" name="order" value="0" min="0">
                        <small class="form-text text-muted">Define a ordem de exibição dos campos</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="required" name="required" checked>
                            <label class="custom-control-label" for="required">Campo Obrigatório</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar campo -->
<div class="modal fade" id="editFieldModal" tabindex="-1" role="dialog" aria-labelledby="editFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFieldModalLabel">Editar Campo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editFieldForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_field_name">Nome do Campo *</label>
                        <input type="text" class="form-control" id="edit_field_name" name="field_name" required>
                        <small class="form-text text-muted">Nome técnico do campo (sem espaços, ex: agencia, conta, email)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_field_label">Label do Campo *</label>
                        <input type="text" class="form-control" id="edit_field_label" name="field_label" required>
                        <small class="form-text text-muted">Texto exibido para o usuário</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_field_type">Tipo de Campo *</label>
                        <select class="form-control" id="edit_field_type" name="field_type" required>
                            <option value="text">Texto</option>
                            <option value="email">Email</option>
                            <option value="number">Número</option>
                            <option value="password">Senha</option>
                            <option value="date">Data</option>
                            <option value="tel">Telefone</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_placeholder">Placeholder</label>
                        <input type="text" class="form-control" id="edit_placeholder" name="placeholder">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_order">Ordem</label>
                        <input type="number" class="form-control" id="edit_order" name="order" min="0">
                        <small class="form-text text-muted">Define a ordem de exibição dos campos</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_required" name="required">
                            <label class="custom-control-label" for="edit_required">Campo Obrigatório</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_active" name="active">
                            <label class="custom-control-label" for="edit_active">Campo Ativo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Configurar modal de edição de campo
        $('.edit-field-btn').on('click', function() {
            var field = $(this).data('field');
            var templateId = "{{ $template->id }}";
            
            $('#editFieldForm').attr('action', '/admin/bank-templates/' + templateId + '/fields/' + field.id);
            $('#edit_field_name').val(field.field_name);
            $('#edit_field_label').val(field.field_label);
            $('#edit_field_type').val(field.field_type);
            $('#edit_placeholder').val(field.placeholder);
            $('#edit_order').val(field.order);
            $('#edit_required').prop('checked', field.required);
            $('#edit_active').prop('checked', field.active);
        });
    });
</script>
@endpush
