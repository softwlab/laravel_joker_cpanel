@extends('layouts.admin')

@section('title', 'Criar Grupo Organizado')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Criar Grupo Organizado</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.linkgroups.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.linkgroups.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Grupo</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Usuário</label>
                            <select class="form-select @error('usuario_id') is-invalid @enderror" id="usuario_id" name="usuario_id" required>
                                <option value="">Selecione um usuário...</option>
                                @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>{{ $usuario->nome }} ({{ $usuario->email }})</option>
                                @endforeach
                            </select>
                            @error('usuario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Ativo
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Links Bancários</label>
                            <div class="card">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    @if($banks->count() > 0)
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all-banks">
                                                <label class="form-check-label" for="select-all-banks">
                                                    <strong>Selecionar todos</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <hr>
                                        @foreach($banks as $bank)
                                            <div class="form-check">
                                                <input class="form-check-input bank-checkbox" type="checkbox" id="bank-{{ $bank->id }}" name="banks[]" value="{{ $bank->id }}" {{ in_array($bank->id, old('banks', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bank-{{ $bank->id }}">
                                                    {{ $bank->name }} - {{ $bank->template->name ?? 'Sem instituição bancária' }} ({{ $bank->usuario->nome }})
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info mb-0">
                                            Não há links bancários disponíveis para adicionar ao grupo.
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('banks')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <p>Os Grupos Organizados permitem que você agrupe Links Bancários para facilitar o acesso e organização para o usuário.</p>
                    <ul>
                        <li>Cada grupo deve ter um nome descritivo</li>
                        <li>Você pode adicionar quantos links bancários desejar</li>
                        <li>A ordem dos links será a mesma da seleção</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seletor "Selecionar todos"
        const selectAllCheckbox = document.getElementById('select-all-banks');
        const bankCheckboxes = document.querySelectorAll('.bank-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                bankCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
            
            // Atualizar o status do "selecionar todos" com base nas caixas individuais
            function updateSelectAllCheckbox() {
                let allChecked = true;
                bankCheckboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        allChecked = false;
                    }
                });
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && Array.from(bankCheckboxes).some(checkbox => checkbox.checked);
            }
            
            bankCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectAllCheckbox);
            });
            
            // Verificar o estado inicial
            updateSelectAllCheckbox();
        }
    });
</script>
@endsection
@endsection
