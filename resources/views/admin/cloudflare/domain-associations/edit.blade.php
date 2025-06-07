@extends('layouts.admin')

@section('title', 'Editar Associação de Domínio Cloudflare')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Associação de Domínio Cloudflare</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.cloudflare.domain-associations.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <form action="{{ route('admin.cloudflare.domain-associations.update', [$domain->id, $usuario->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Domínio Cloudflare</label>
                            <input type="text" class="form-control" value="{{ $domain->name }}" disabled>
                            <small class="text-muted">O domínio não pode ser alterado. Para mudar o domínio, exclua esta associação e crie uma nova.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" class="form-control" value="{{ $usuario->nome }} ({{ $usuario->email }})" disabled>
                            <small class="text-muted">O usuário não pode ser alterado. Para mudar o usuário, exclua esta associação e crie uma nova.</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ $association->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="paused" {{ $association->status == 'paused' ? 'selected' : '' }}>Pausado</option>
                                <option value="pending" {{ $association->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $association->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="config" class="form-label">Configurações (JSON)</label>
                            <textarea class="form-control @error('config') is-invalid @enderror" id="config" name="config" rows="5">{{ old('config', json_encode($association->config, JSON_PRETTY_PRINT)) }}</textarea>
                            <small class="text-muted">Formato JSON válido. Deixe como "{}" se não tiver configurações específicas.</small>
                            @error('config')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <p>Altere as configurações da associação entre o domínio Cloudflare e o usuário.</p>
                    <ul class="mb-0">
                        <li>O status <strong>Ativo</strong> permite que o usuário visualize e edite registros</li>
                        <li>O status <strong>Pausado</strong> permite apenas visualização</li>
                        <li>O status <strong>Pendente</strong> não permite nenhuma ação</li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$domain->id, $usuario->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta associação?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Excluir Associação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const configTextarea = document.getElementById('config');
        
        // Verificar e formatar JSON ao enviar o formulário
        document.querySelector('form').addEventListener('submit', function(event) {
            try {
                const config = configTextarea.value.trim();
                if (config !== '' && config !== '{}') {
                    // Tenta fazer o parse e depois formata bonito com indentação
                    const parsedConfig = JSON.parse(config);
                    configTextarea.value = JSON.stringify(parsedConfig, null, 2);
                }
            } catch (e) {
                event.preventDefault();
                alert('Configurações JSON inválidas: ' + e.message);
                configTextarea.focus();
            }
        });
    });
</script>
@endsection
