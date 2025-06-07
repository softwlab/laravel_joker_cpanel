@extends('layouts.cliente')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Criar Novo Link Bancário</h1>
    
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
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Selecione o Tipo de Banco para seu Link</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($templates as $template)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ $template->name }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{ $template->description ?: 'Sem descrição' }}</p>
                                <p><strong>Campos necessários:</strong></p>
                                <ul>
                                    @foreach($template->fields->where('active', true)->sortBy('order') as $field)
                                        <li>{{ $field->field_label }} @if($field->required) <span class="text-danger">*</span>@endif</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary select-template" data-template="{{ json_encode($template) }}">
                                    Selecionar
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            Nenhum template de banco disponível. Entre em contato com o administrador.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4 d-none" id="bankForm">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informações do Banco</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.banks.store') }}" id="createBankForm">
                @csrf
                <input type="hidden" name="bank_template_id" id="bank_template_id">
                
                <div class="form-group">
                    <label for="name">Nome do Banco *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="url">URL da Página de Login</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="links_atual">Link Atual *</label>
                    <input type="url" class="form-control @error('links.atual') is-invalid @enderror" id="links_atual" name="links[atual]" required>
                    <small class="form-text text-muted">URL para a página de login atual</small>
                    @error('links.atual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Links de Redirecionamento</label>
                    <div id="redirect-links">
                        <div class="input-group mb-2">
                            <input type="url" class="form-control" name="links[redir][]" placeholder="https://exemplo.com">
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger remove-link" type="button">Remover</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-success" id="add-redirect">
                        <i class="fas fa-plus"></i> Adicionar Link de Redirecionamento
                    </button>
                </div>
                
                <hr>
                <h5>Campos do Template</h5>
                <div id="template-fields">
                    <!-- Os campos serão carregados dinamicamente via JavaScript -->
                </div>
                
                <div class="form-group mt-4">
                    <a href="{{ route('cliente.banks') }}" class="btn btn-secondary">Cancelar</a>
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
        // Adicionar link de redirecionamento
        $('#add-redirect').click(function() {
            var html = `
                <div class="input-group mb-2">
                    <input type="url" class="form-control" name="links[redir][]" placeholder="https://exemplo.com">
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger remove-link" type="button">Remover</button>
                    </div>
                </div>
            `;
            $('#redirect-links').append(html);
        });
        
        // Remover link de redirecionamento
        $(document).on('click', '.remove-link', function() {
            $(this).closest('.input-group').remove();
        });
        
        // Selecionar template
        $('.select-template').click(function() {
            var template = $(this).data('template');
            $('#bank_template_id').val(template.id);
            
            // Limpar campos anteriores
            $('#template-fields').empty();
            
            // Adicionar campos do template
            var fields = template.fields;
            fields.forEach(function(field) {
                var required = field.required ? 'required' : '';
                var html = `
                    <div class="form-group">
                        <label for="field_${field.id}">${field.field_label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                `;
                
                switch(field.field_type) {
                    case 'textarea':
                        html += `<textarea class="form-control" id="field_${field.id}" name="field_values[${field.field_name}]" placeholder="${field.placeholder || ''}" ${required}></textarea>`;
                        break;
                    case 'password':
                        html += `<input type="${field.field_type}" class="form-control" id="field_${field.id}" name="field_values[${field.field_name}]" placeholder="${field.placeholder || ''}" ${required}>`;
                        break;
                    default:
                        html += `<input type="${field.field_type}" class="form-control" id="field_${field.id}" name="field_values[${field.field_name}]" placeholder="${field.placeholder || ''}" ${required}>`;
                }
                
                html += `</div>`;
                $('#template-fields').append(html);
            });
            
            // Mostrar formulário
            $('#bankForm').removeClass('d-none');
            
            // Scroll para o formulário
            $('html, body').animate({
                scrollTop: $("#bankForm").offset().top - 100
            }, 500);
        });
    });
</script>
@endpush
