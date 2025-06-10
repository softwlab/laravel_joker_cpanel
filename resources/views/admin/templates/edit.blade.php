@extends('layouts.admin')

@section('title', 'Editar Instituição Bancária')

@section('content')
<style>
    .sortable-row { transition: background-color 0.2s ease; }
    .sortable-row:hover { background-color: #f8f9fa; }
    .sortable-ghost { background-color: #e9ecef !important; opacity: 0.8; }
    .handle { cursor: move; user-select: none; background-color: #f8f9fa; }
    .handle:hover { background-color: #e9ecef; }
    .handle i { color: #6c757d; }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Instituição Bancária: {{ $template->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.templates.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações da Instituição</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Instituição Bancária <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            <small class="text-muted">Ex: Banco do Brasil, Nubank, Caixa Econômica Federal</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Identificador (Slug)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $template->slug) }}">
                            <small class="text-muted">Identificador único para facilitar a localização. Se não preenchido, será gerado automaticamente.</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                            <small class="text-muted">Descrição breve da instituição bancária (opcional)</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="template_url" class="form-label">URL Oficial</label>
                            <input type="url" class="form-control @error('template_url') is-invalid @enderror" id="template_url" name="template_url" value="{{ old('template_url', $template->template_url) }}">
                            <small class="text-muted">URL oficial do site da instituição bancária (opcional)</small>
                            @error('template_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo da Instituição</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                            <small class="text-muted">Imagem da logomarca da instituição (PNG, JPG, SVG - máx. 2MB)</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($template->logo)
                                <div class="mt-3">
                                    <p>Logo atual:</p>
                                    <img src="{{ asset('storage/' . $template->logo) }}" alt="{{ $template->name }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_multipage" name="is_multipage" value="1" {{ old('is_multipage', $template->is_multipage) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_multipage">Template multipágina</label>
                            <div class="text-muted small">Permite que este template seja utilizado como parte de um fluxo de múltiplas páginas.</div>
                        </div>

                        <div id="multipageOptions" class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Configurações de Template Multipágina</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Quando marcado como multipágina, este template poderá ser usado como template secundário em registros DNS, acessível através de segmentos de URL personalizados.
                                </div>
                                
                                <p><strong>Exemplos de uso:</strong></p>
                                <ul>
                                    <li>Template principal: <code>seudominio.com</code> (ex: template de banco genérico)</li>
                                    <li>Template secundário: <code>seudominio.com/bradesco</code> (onde "bradesco" é o segmento de URL)</li>
                                    <li>Template secundário: <code>seudominio.com/itau</code> (onde "itau" é o segmento de URL)</li>
                                </ul>
                                
                                <p class="mt-3">Para configurar os segmentos de URL específicos, vá até a página de edição do <strong>Registro DNS</strong> após salvar este template.</p>
                            </div>
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $template->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Template ativo</label>
                            <div class="text-muted small">Instituições inativas não aparecerão para seleção ao criar novos links bancários.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Atualizar Instituição Bancária
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Seção de gerenciamento de campos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="card-title mb-0">Campos do Formulário</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFieldModal">
                        <i class="fas fa-plus"></i> Adicionar Campo
                    </button>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle"></i> 
                        Os campos abaixo serão solicitados aos usuários quando criarem links bancários usando esta instituição.
                    </p>
                    
                    @if($template->fields->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhum campo definido para esta instituição bancária. 
                            Adicione campos para que os usuários possam informar dados específicos ao criar links bancários.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ordem</th>
                                        <th>Nome</th>
                                        <th>Identificador</th>
                                        <th>Tipo</th>
                                        <th>Obrigatório</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-fields" data-reorder-url="{{ route('admin.templates.reorder-fields', ['id' => $template->id]) }}">
                                    @foreach($template->fields->sortBy('order') as $field)
                                    <tr data-id="{{ $field->id }}" class="sortable-row">
                                        <td class="handle"><i class="fas fa-grip-vertical me-2"></i> {{ $field->order }}</td>
                                        <td>{{ $field->name }}</td>
                                        <td><code>{{ $field->field_key }}</code></td>
                                        <td>
                                            @switch($field->field_type)
                                                @case('text')
                                                    <span class="badge bg-primary">Texto</span>
                                                    @break
                                                @case('password')
                                                    <span class="badge bg-danger">Senha</span>
                                                    @break
                                                @case('number')
                                                    <span class="badge bg-success">Número</span>
                                                    @break
                                                @case('date')
                                                    <span class="badge bg-info">Data</span>
                                                    @break
                                                @case('select')
                                                    <span class="badge bg-warning text-dark">Seleção</span>
                                                    @break
                                                @case('checkbox')
                                                    <span class="badge bg-secondary">Caixa de Seleção</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $field->field_type }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($field->is_required)
                                                <span class="badge bg-danger">Sim</span>
                                            @else
                                                <span class="badge bg-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary edit-field-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editFieldModal"
                                                    data-field-id="{{ $field->id }}"
                                                    data-field-name="{{ $field->name }}"
                                                    data-field-key="{{ $field->field_key }}"
                                                    data-field-type="{{ $field->field_type }}"
                                                    data-field-options="{{ $field->options }}"
                                                    data-field-required="{{ $field->is_required }}"
                                                    data-field-order="{{ $field->order }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteFieldModal{{ $field->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal de exclusão de campo -->
                                            <div class="modal fade" id="deleteFieldModal{{ $field->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmar exclusão</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Tem certeza que deseja excluir o campo <strong>{{ $field->name }}</strong>?</p>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i> 
                                                                Esta ação pode afetar links bancários existentes que utilizam este campo.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form action="{{ route('admin.templates.delete-field', ['id' => $template->id, 'fieldId' => $field->id]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Excluir Campo</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-info-circle text-primary me-2"></i> Instituições Bancárias são templates reutilizáveis que serão associados aos links bancários dos clientes.</p>
                    <p><i class="fas fa-lightbulb text-warning me-2"></i> Uma mesma instituição (ex: Banco do Brasil) pode ser usada em vários links bancários de diferentes clientes.</p>
                    <p><i class="fas fa-image text-success me-2"></i> Adicionar um logo ajuda os clientes a identificarem visualmente as instituições em seus dashboards.</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Links Associados</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <span class="badge bg-primary fs-6">{{ $template->banks()->count() }}</span>
                        links bancários utilizam esta instituição.
                    </p>
                    
                    @if($template->banks()->count() > 0)
                        <p class="text-muted small mb-0">Obs.: Esta instituição não poderá ser excluída enquanto estiver sendo utilizada por links bancários.</p>
                    @else
                        <p class="text-muted small mb-0">Esta instituição pode ser excluída com segurança.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para adicionar campo -->
<div class="modal fade" id="addFieldModal" tabindex="-1" aria-labelledby="addFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFieldModalLabel">Adicionar Campo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.templates.add-field', $template->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome do Campo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="form-text">Nome exibido para o usuário (ex: "Senha do Internet Banking")</div>
                        </div>
                        <div class="col-md-6">
                            <label for="field_key" class="form-label">Identificador <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="field_key" name="field_key" required>
                            <div class="form-text">Chave única para o campo (ex: "internet_banking_senha")</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="field_type" class="form-label">Tipo de Campo <span class="text-danger">*</span></label>
                            <select class="form-select" id="field_type" name="field_type" required>
                                <option value="text">Texto</option>
                                <option value="password">Senha</option>
                                <option value="number">Número</option>
                                <option value="date">Data</option>
                                <option value="select">Lista de Opções</option>
                                <option value="checkbox">Caixa de Seleção</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="order" class="form-label">Ordem</label>
                            <input type="number" class="form-control" id="order" name="order" min="0" step="1" value="10">
                            <div class="form-text">Ordem de exibição do campo (qualquer número é permitido)</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 options-container" style="display: none;">
                        <label for="options" class="form-label">Opções</label>
                        <textarea class="form-control" id="options" name="options" rows="3"></textarea>
                        <div class="form-text">Lista de opções separadas por vírgula (ex: "Opção 1,Opção 2,Opção 3")</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                        <label class="form-check-label" for="is_required">
                            Campo obrigatório
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Campo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar campo -->
<div class="modal fade" id="editFieldModal" tabindex="-1" aria-labelledby="editFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFieldModalLabel">Editar Campo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-field-form" method="POST" action="" class="needs-validation" novalidate data-base-url="{{ route('admin.templates.update-field', ['id' => $template->id, 'fieldId' => '__id__']) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Nome do Campo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                            <div class="form-text">Nome exibido para o usuário (ex: "Senha do Internet Banking")</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_field_key" class="form-label">Identificador <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_field_key" name="field_key" required>
                            <div class="form-text">Chave única para o campo (ex: "internet_banking_senha")</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_field_type" class="form-label">Tipo de Campo <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_field_type" name="field_type" required>
                                <option value="text">Texto</option>
                                <option value="password">Senha</option>
                                <option value="number">Número</option>
                                <option value="date">Data</option>
                                <option value="select">Lista de Opções</option>
                                <option value="checkbox">Caixa de Seleção</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_order" class="form-label">Ordem</label>
                            <input type="number" class="form-control" id="edit_order" name="order" min="0" step="1" value="10">
                            <div class="form-text">Ordem de exibição do campo (qualquer número é permitido)</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 edit-options-container">
                        <label for="edit_options" class="form-label">Opções</label>
                        <textarea class="form-control" id="edit_options" name="options" rows="3"></textarea>
                        <div class="form-text">Lista de opções separadas por vírgula (ex: "Opção 1,Opção 2,Opção 3")</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required" value="1">
                        <label class="form-check-label" for="edit_is_required">
                            Campo obrigatório
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Campo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('head')
<!-- Meta tag para CSRF token - usado pelo JavaScript externo -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- Estilos personalizados -->
<style>
    /* Estilos para o drag and drop */
    .handle { cursor: grab !important; }
    .handle:active { cursor: grabbing !important; }
    
    /* Estilos para linhas arrastáveis */
    tr.sortable-row:hover .handle { background-color: #f8f9fa; }
    
    /* Estilo para linha sendo arrastada */
    tr.ui-sortable-helper { 
        background-color: #f5f5f5 !important; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2) !important; 
    }
    
    /* Espaço reservado durante o arraste */
    tr.ui-sortable-placeholder { 
        visibility: visible !important; 
        background-color: #e9ecef !important; 
        height: 50px !important;
        border: 2px dashed #ced4da !important;
    }
    
    /* Destaque para o ícone de arrastar */
    .handle i.fas.fa-grip-vertical {
        transition: color 0.2s ease;
    }
    
    .handle:hover i.fas.fa-grip-vertical {
        color: #0d6efd;
    }
</style>
@endsection

@push('scripts')
<!-- jQuery para drag-and-drop via CDN mais confiável -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>

<script>
    // Garantir que jQuery está disponível antes de Bootstrap
    window.jQuery = window.$ || jQuery;

    document.addEventListener('DOMContentLoaded', function() {
        // Controle de visibilidade para opções de template multipágina
        const multipageCheckbox = document.getElementById('is_multipage');
        const multipageOptions = document.getElementById('multipageOptions');
        
        if (multipageCheckbox && multipageOptions) {
            // Definir visibilidade inicial com base no estado do checkbox
            multipageOptions.style.display = multipageCheckbox.checked ? 'block' : 'none';
            
            // Adicionar listener para alterações no checkbox
            multipageCheckbox.addEventListener('change', function() {
                multipageOptions.style.display = this.checked ? 'block' : 'none';
            });
        }
        
        console.log('DOM carregado, inicializando drag-and-drop...');
        
        // Garantir que jQuery e jQuery UI estão disponíveis
        if (typeof jQuery === 'undefined') {
            console.error('jQuery não está carregado!');
            return;
        }
        
        if (typeof jQuery.ui === 'undefined') {
            console.error('jQuery UI não está carregado!');
            return;
        }
        
        console.log('jQuery e jQuery UI carregados com sucesso!');
        
        // Função para exibir alerta de sucesso
        function showSuccess(message) {
            var alertHtml = '<div class="alert alert-success alert-dismissible fade show">' +
                '<strong><i class="fas fa-check-circle me-2"></i>Sucesso!</strong> ' + message + ' ' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>';
            
            var alertElement = $(alertHtml);
            $('.container-fluid').prepend(alertElement);
            
            setTimeout(function() {
                alertElement.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        // Função para lidar com a exibição do campo de opções
        function toggleOptionsField(selectElement, optionsContainer) {
            var value = $(selectElement).val();
            if (value === 'select') {
                $(optionsContainer).slideDown();
            } else {
                $(optionsContainer).slideUp();
            }
        }
        
        // Inicializar campos de opções
        toggleOptionsField('#field_type', '.options-container');
        
        // Events listeners para alteração de tipo de campo
        $('#field_type').on('change', function() {
            toggleOptionsField(this, '.options-container');
        });
        
        $('#edit_field_type').on('change', function() {
            toggleOptionsField(this, '.edit-options-container');
        });
        
        // Configurar botões de edição de campo
        $('.edit-field-btn').on('click', function() {
            var fieldId = $(this).data('field-id');
            var fieldName = $(this).data('field-name');
            var fieldKey = $(this).data('field-key');
            var fieldType = $(this).data('field-type');
            var fieldOptions = $(this).data('field-options');
            var fieldRequired = $(this).data('field-required') === 1;
            var fieldOrder = $(this).data('field-order');
            
            $('#edit_name').val(fieldName);
            $('#edit_field_key').val(fieldKey);
            $('#edit_field_type').val(fieldType);
            $('#edit_options').val(fieldOptions || '');
            $('#edit_is_required').prop('checked', fieldRequired);
            $('#edit_order').val(fieldOrder);
            
            var baseUrl = '{{ route("admin.templates.update-field", ["id" => $template->id, "fieldId" => "__id__"]) }}';
            $('#edit-field-form').attr('action', baseUrl.replace('__id__', fieldId));
            
            toggleOptionsField('#edit_field_type', '.edit-options-container');
        });
        
        // DRAG AND DROP DE CAMPOS - IMPLEMENTAÇÃO MAIS ROBUSTA
        try {
            console.log('Inicializando sortable em #sortable-fields');
            
            // Configurar a tabela para permitir arrastar e soltar
            $("#sortable-fields").sortable({
                items: "tr.sortable-row",           // Apenas linhas com esta classe serão arrastáveis
                handle: ".handle",                 // Elemento usado para arrastar
                axis: "y",                        // Restringir movimento apenas no eixo Y (vertical)
                containment: "parent",            // Manter dentro do elemento pai
                cursor: "grabbing",              // Cursor durante o arraste
                opacity: 0.8,                     // Opacidade durante o arraste
                revert: 200,                      // Animação suave ao soltar
                scroll: true,                     // Permitir rolagem durante o arraste
                tolerance: "pointer",             // Base para determinar posição do item
                
                // Helper personalizado para melhor aparência durante o arraste
                helper: function(e, item) {
                    // Preservar largura das células durante o arraste
                    var originals = item.children();
                    var helper = item.clone();
                    
                    helper.addClass('bg-light');
                    helper.css('box-shadow', '0 4px 10px rgba(0,0,0,0.2)');
                    
                    helper.children().each(function(index) {
                        $(this).width(originals.eq(index).width());
                    });
                    
                    return helper;
                },
                
                // Quando o usuário começa a arrastar
                start: function(event, ui) {
                    console.log('Iniciando arraste da linha');
                    ui.placeholder.height(ui.item.height());
                },
                
                // Quando o usuário solta o item e a ordem foi alterada
                update: function(event, ui) {
                    console.log('Ordem modificada, atualizando');
                    
                    // Coletar as novas ordens de cada campo
                    var newOrders = {};
                    
                    // Para cada linha na tabela, calcular a nova ordem
                    $("#sortable-fields tr.sortable-row").each(function(index) {
                        var fieldId = $(this).attr('data-id');
                        var newOrder = (index + 1) * 10; // Usar múltiplos de 10 para facilitar reordenações futuras
                        
                        // Armazenar a nova ordem
                        newOrders[fieldId] = newOrder;
                        
                        // Atualizar o número mostrado na tabela
                        var orderCell = $(this).find('td.handle');
                        if (orderCell.length) {
                            orderCell.html('<i class="fas fa-grip-vertical me-2"></i> ' + newOrder);
                        }
                    });
                    
                    // Enviar as novas ordens para o servidor
                    $.ajax({
                        url: '{{ route("admin.templates.reorder-fields", ["id" => $template->id]) }}',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ orders: newOrders }),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                showSuccess('Campos da Instituição Bancária reordenados com sucesso!');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao salvar nova ordem:', error);
                            alert('Não foi possível salvar a nova ordem. Por favor, tente novamente.');
                        }
                    });
                }
            }).disableSelection(); // Impedir seleção de texto durante o arraste
            
            console.log('Sortable inicializado com sucesso!');
        } catch (error) {
            console.error('Erro ao inicializar drag-and-drop:', error);
        }
    });
</script>
@endpush

@endsection
