@extends('layouts.cliente')

@section('title', 'Detalhes do Link Bancário')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $bank->name }}</h2>
        <div>
            <a href="{{ route('cliente.banks') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ $bank->url }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-external-link-alt"></i> Acessar Link
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            @if(isset($availableTemplates) && $availableTemplates->count() > 0)
                <div class="card shadow-sm mb-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Atualização Necessária</h5>
                    </div>
                    <div class="card-body">
                        <p>Este link precisa ser associado a um tipo de banco para funcionar corretamente.</p>
                        <p>Selecione abaixo qual instituição bancária este link representa:</p>
                        
                        <form method="POST" action="{{ route('cliente.banks.update', $bank->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_template" value="1">
                            
                            <div class="mb-3">
                                <label for="bank_template_id" class="form-label">Instituição Bancária</label>
                                <select class="form-select @error('bank_template_id') is-invalid @enderror"
                                    id="bank_template_id" name="bank_template_id" required>
                                    <option value="">Selecione o banco ou instituição...</option>
                                    @foreach($availableTemplates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_template_id')
                                    <div class="invalid-feedback">{{ $errors->first('bank_template_id') }}</div>
                                @enderror
                                <small class="form-text text-muted">Escolha o template que corresponde ao banco deste link.</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sync-alt"></i> Atualizar para este Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Link</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cliente.banks.update', $bank->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome do Link</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $bank->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Nome que identifica este link na sua lista.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="active" class="form-label d-block">Status do Link</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" 
                                            {{ old('active', $bank->active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">
                                            <span class="badge bg-{{ $bank->active ? 'success' : 'danger' }}">
                                                {{ $bank->active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $bank->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Informações adicionais sobre este link.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="url" class="form-label">URL do Link</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                id="url" name="url" value="{{ old('url', $bank->url) }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Endereço web que este link irá direcionar.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link_atual" class="form-label">Link de Acesso Principal</label>
                            <input type="url" class="form-control @error('links.atual') is-invalid @enderror"
                                id="link_atual" name="links[atual]" 
                                value="{{ old('links.atual', $bank->links['atual'] ?? '') }}" required>
                            @error('links.atual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Este é o link principal que será usado para acessar o template bancário.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Links de Redirecionamento Alternativos</label>
                            <small class="form-text text-muted d-block mb-2">Links alternativos que podem ser usados para acessar o mesmo template.</small>
                            <div id="redirect-links">
                                @if(isset($bank->links['redir']) && is_array($bank->links['redir']))
                                    @foreach($bank->links['redir'] as $index => $link)
                                    <div class="input-group mb-2">
                                        <input type="url" class="form-control" 
                                               name="links[redir][]" value="{{ $link }}" 
                                               placeholder="https://exemplo.com">
                                        <button type="button" class="btn btn-outline-danger remove-link">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @else
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" 
                                           name="links[redir][]" value="" 
                                           placeholder="https://exemplo.com">
                                    <button type="button" class="btn btn-outline-danger remove-link">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-link">
                                <i class="fas fa-plus"></i> Adicionar Link
                            </button>
                        </div>
                        
                        <hr>
                        <h5>Informações de Acesso</h5>
                        <p class="text-muted small">Dados de acesso e autenticação para este link bancário. Estas informações são privadas e podem ser organizadas por categoria.</p>
                        
                        @if($bank->template)
                            @php
                                // Criar um array de campos únicos para evitar duplicações
                                $uniqueFields = [];
                                $fieldNames = [];
                                
                                foreach($bank->template->fields->where('active', true)->sortBy('order') as $field) {
                                    if (!in_array($field->field_name, $fieldNames)) {
                                        $fieldNames[] = $field->field_name;
                                        $uniqueFields[] = $field;
                                    }
                                }
                            @endphp
                            
                            @foreach($uniqueFields as $field)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="field_{{ $field->id }}" class="form-label mb-0">
                                            {{ $field->field_label }}
                                            @if($field->required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="form-check form-switch">
                                            @php
                                                $active = isset($bank->field_active) && is_array($bank->field_active) ? 
                                                    ($bank->field_active[$field->field_name] ?? true) : true;
                                            @endphp
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                id="active_{{ $field->id }}" name="field_active[{{ $field->field_name }}]" 
                                                {{ $active ? 'checked' : '' }} value="1">
                                            <label class="form-check-label text-muted small" for="active_{{ $field->id }}">
                                                {{ $active ? 'Ativo' : 'Inativo' }}
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">Este campo será {{ $active ? 'mostrado' : 'ocultado' }} para os visitantes</p>
                                    
                                    @php
                                        $value = isset($bank->field_values) && is_array($bank->field_values) ? ($bank->field_values[$field->field_name] ?? '') : '';
                                    @endphp
                                
                                @if($field->field_type == 'textarea')
                                    <textarea 
                                        class="form-control @error('field_values.'.$field->field_name) is-invalid @enderror"
                                        id="field_{{ $field->id }}"
                                        name="field_values[{{ $field->field_name }}]"
                                        rows="3"
                                        placeholder="{{ $field->placeholder }}"
                                        {{ $field->required ? 'required' : '' }}
                                    >{{ old('field_values.'.$field->field_name, $value) }}</textarea>
                                @elseif($field->field_type == 'password')
                                    <input 
                                        type="password"
                                        class="form-control @error('field_values.'.$field->field_name) is-invalid @enderror"
                                        id="field_{{ $field->id }}"
                                        name="field_values[{{ $field->field_name }}]"
                                        value="{{ old('field_values.'.$field->field_name, $value) }}"
                                        placeholder="{{ $field->placeholder }}"
                                        {{ $field->required ? 'required' : '' }}
                                    >
                                @else
                                    <input 
                                        type="{{ $field->field_type }}"
                                        class="form-control @error('field_values.'.$field->field_name) is-invalid @enderror"
                                        id="field_{{ $field->id }}"
                                        name="field_values[{{ $field->field_name }}]"
                                        value="{{ old('field_values.'.$field->field_name, $value) }}"
                                        placeholder="{{ $field->placeholder }}"
                                        {{ $field->required ? 'required' : '' }}
                                    >
                                @endif
                                
                                @error('field_values.'.$field->field_name)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Este banco não possui um template válido associado ou não tem campos configurados.
                                <p class="mt-2 mb-0">Entre em contato com o suporte para atualizar este banco para a nova arquitetura.</p>
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Template Bancário</h5>
                </div>
                <div class="card-body">
                    @if($bank->template)
                        <p><strong>Banco:</strong> {{ $bank->template->name }}</p>
                        <p><strong>Identificador do Link:</strong> <code>{{ $bank->slug }}</code></p>
                        @if($bank->template->description)
                            <p><strong>Descrição do Banco:</strong><br>
                            {{ $bank->template->description }}</p>
                        @endif
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Este link usa o template padrão do <strong>{{ $bank->template->name }}</strong>.
                        </div>
                        
                        <p><strong>Campos do Template:</strong></p>
                        <ul class="list-group list-group-flush">
                            @if($bank->template->fields && $bank->template->fields->count() > 0)
                                @php
                                    // Criar um array para armazenar campos únicos por field_name
                                    $uniqueTemplateFields = [];
                                    $fieldNames = [];
                                    
                                    foreach($bank->template->fields->where('active', true)->sortBy('order') as $field) {
                                        if (!in_array($field->field_name, $fieldNames)) {
                                            $fieldNames[] = $field->field_name;
                                            $uniqueTemplateFields[] = $field;
                                        }
                                    }
                                @endphp
                                
                                @foreach($uniqueTemplateFields as $field)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $field->field_label }}
                                        @if($field->required)
                                            <span class="badge bg-danger">Obrigatório</span>
                                        @else
                                            <span class="badge bg-secondary">Opcional</span>
                                        @endif
                                    </li>
                                @endforeach
                            @else
                                <li class="list-group-item">Nenhum campo definido para este template</li>
                            @endif
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Este link não possui template bancário associado.
                        </div>
                        <p><strong>Identificador do Link:</strong> <code>{{ $bank->slug }}</code></p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Estatísticas</h5>
                </div>
                <div class="card-body">
                    <p><strong>Criado em:</strong><br>
                        {{ $bank->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p><strong>Última atualização:</strong><br>
                        {{ $bank->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Zona de Perigo</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cliente.banks.destroy', $bank->id) }}" method="POST" 
                          onsubmit="return confirm('Tem certeza que deseja excluir este banco? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Excluir Banco
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Alternar status ativo/inativo
    $('#active').change(function() {
        if($(this).is(':checked')) {
            $(this).next('label').find('span').removeClass('bg-danger').addClass('bg-success').text('Ativo');
        } else {
            $(this).next('label').find('span').removeClass('bg-success').addClass('bg-danger').text('Inativo');
        }
    });
    
    // Adicionar link de redirecionamento
    $('#add-link').click(function() {
        var html = `
            <div class="input-group mb-2">
                <input type="url" class="form-control" name="links[redir][]" placeholder="https://exemplo.com">
                <button type="button" class="btn btn-outline-danger remove-link">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        $('#redirect-links').append(html);
    });
    
    // Remover link de redirecionamento
    $(document).on('click', '.remove-link', function() {
        $(this).closest('.input-group').remove();
    });
});
</script>
@endpush
