@extends('layouts.cliente')

@section('title', 'Detalhes do Link Bancário')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ $bank->name }}</h1>
        <div>
            <a href="{{ route('cliente.banks') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Link Bancário</h5>
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="active" class="form-label d-block">Estado do Link</label>
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
                                id="description" name="description" rows="2">{{ old('description', $bank->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="url" class="form-label">URL da Página de Login</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror"
                                id="url" name="url" value="{{ old('url', $bank->url) }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="link_atual" class="form-label">Link Atual</label>
                            <input type="url" class="form-control @error('links.atual') is-invalid @enderror"
                                id="link_atual" name="links[atual]" 
                                value="{{ old('links.atual', $bank->links['atual'] ?? '') }}" required>
                            @error('links.atual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Redirecionamentos Alternativos</label>
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
                        <p class="text-muted small">Dados específicos para esta instituição bancária</p>
                        
                        @foreach($bank->template->fields as $field)
                            <div class="mb-3">
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->field_label }}
                                    @if($field->required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                @php
                                    $value = $bank->field_values[$field->field_name] ?? '';
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
                    <h5 class="mb-0">Detalhes da Instituição Bancária</h5>
                </div>
                <div class="card-body">
                    <p><strong>Instituição:</strong> {{ $bank->template->name }}</p>
                    <p><strong>Identificador Único:</strong> <code>{{ $bank->slug }}</code></p>
                    @if($bank->template->description)
                        <p><strong>Descrição da Instituição:</strong><br>
                        {{ $bank->template->description }}</p>
                    @endif
                    
                    <p><strong>Campos Necessários:</strong></p>
                    <ul class="list-group list-group-flush">
                        @foreach($bank->template->fields as $field)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $field->field_label }}
                                @if($field->required)
                                    <span class="badge bg-danger">Obrigatório</span>
                                @else
                                    <span class="badge bg-secondary">Opcional</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
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
