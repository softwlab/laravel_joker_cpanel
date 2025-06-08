@extends('layouts.app')

@section('title', 'Configurar Template')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho com breadcrumb -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Configurar Template: {{ $template->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('cliente.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Configuração de Campos
                </div>
                <div class="card-body">
                    <form action="{{ route('cliente.templates.config.update', $template->id) }}" method="POST" id="fieldConfigForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="record_id" value="{{ $record->id }}">

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Configure os campos que deseja exibir no seu template e a ordem de exibição. 
                            Campos obrigatórios não podem ser desativados.
                        </div>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="25%">Nome do Campo</th>
                                    <th width="40%">Descrição</th>
                                    <th width="15%" class="text-center">Ordem</th>
                                    <th width="15%" class="text-center">Ativo</th>
                                </tr>
                            </thead>

                            <tbody class="sortable">
                                @foreach($template->fields as $field)
                                    @php
                                        $fieldConfig = isset($userConfig->config[$field->field_key]) 
                                            ? $userConfig->config[$field->field_key] 
                                            : ['active' => true, 'order' => $field->order];
                                    @endphp
                                    <tr data-field-name="{{ $field->field_key }}">
                                        <td class="text-center align-middle">
                                            <i class="fas fa-grip-vertical handle" style="cursor: move; font-size: 1.2em; color: #666;"></i>
                                        </td>
                                        <td class="align-middle"><strong>{{ $field->name }}</strong></td>
                                        <td class="align-middle">
                                            <span>
                                                {{ $field->options ?? 'Sem descrição' }}
                                                @if($field->is_required)
                                                    <span class="badge bg-danger ms-1">Obrigatório</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Opcional</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" 
                                                name="field_order[{{ $field->field_key }}]" 
                                                class="form-control form-control-sm mx-auto" 
                                                style="width: 70px;" 
                                                value="{{ $fieldConfig['order'] }}" 
                                                min="1">
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" 
                                                    type="checkbox" 
                                                    id="field_active_{{ $field->field_key }}"
                                                    name="field_active[{{ $field->field_key }}]"
                                                    value="1"
                                                    style="width: 3em; height: 1.5em;" 
                                                    @if($fieldConfig['active'] || $field->is_required) checked @endif
                                                    @if($field->is_required) disabled @endif>
                                                <label class="form-check-label" for="field_active_{{ $field->field_key }}"></label>
                                            </div>
                                            @if($field->is_required)
                                                <input type="hidden" name="field_active[{{ $field->field_key }}]" value="1">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('cliente.dashboard') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configuração
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informações do Template
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $template->name }}</dd>
                        
                        <dt class="col-sm-4">Descrição:</dt>
                        <dd class="col-sm-8">{{ $template->description ?? 'Sem descrição' }}</dd>
                        
                        <dt class="col-sm-4">Total de campos:</dt>
                        <dd class="col-sm-8">{{ $template->fields->count() }}</dd>
                        
                        <dt class="col-sm-4">Campos obrigatórios:</dt>
                        <dd class="col-sm-8">{{ $template->fields->where('required', true)->count() }}</dd>
                        
                        <dt class="col-sm-4">Campos opcionais:</dt>
                        <dd class="col-sm-8">{{ $template->fields->where('required', false)->count() }}</dd>
                    </dl>
                    
                    <hr>
                    
                    <h6 class="card-subtitle mb-2 text-muted">Página/Domínio Associado</h6>
                    <dl class="row mt-3">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $record->name }}</dd>
                        
                        <dt class="col-sm-4">Tipo:</dt>
                        <dd class="col-sm-8">{{ $record->type }}</dd>
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($record->status === 'active')
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar o Sortable para permitir arrastar e soltar
    const sortableList = document.querySelector('.sortable');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.handle',
            animation: 150,
            onEnd: function(evt) {
                // Atualizar os números de ordem após arrastar e soltar
                function updateOrders() {
                    const rows = document.querySelectorAll('.sortable tr');
                    rows.forEach(function(row, index) {
                        const fieldKey = row.getAttribute('data-field-name');
                        const orderInput = row.querySelector(`input[name="field_order[${fieldKey}]"]`);
                        if (orderInput) {
                            orderInput.value = index + 1;
                        }
                    });
                }
                updateOrders();
            }
        });
    }
});
</script>
@endsection
