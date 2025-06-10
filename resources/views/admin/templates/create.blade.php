@extends('layouts.admin')

@section('title', 'Nova Instituição Bancária')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nova Instituição Bancária</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.templates.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Instituição Bancária <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            <small class="text-muted">Ex: Banco do Brasil, Nubank, Caixa Econômica Federal</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Identificador (Slug)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                            <small class="text-muted">Identificador único para facilitar a localização. Se não preenchido, será gerado automaticamente.</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <small class="text-muted">Descrição breve da instituição bancária (opcional)</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="template_url" class="form-label">URL Oficial</label>
                            <input type="url" class="form-control @error('template_url') is-invalid @enderror" id="template_url" name="template_url" value="{{ old('template_url') }}">
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
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_multipage" name="is_multipage" {{ old('is_multipage', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_multipage">Multipágina</label>
                            <div class="text-muted small">Quando ativado, este template poderá ter múltiplas páginas associadas em um único registro DNS.</div>
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Instituição Ativa</label>
                            <div class="text-muted small">Instituições inativas não aparecerão para seleção ao criar novos links bancários.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar Instituição Bancária
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informações</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-info-circle text-primary me-2"></i> Instituições Bancárias são templates reutilizáveis que serão associados aos links bancários dos clientes.</p>
                    <p><i class="fas fa-lightbulb text-warning me-2"></i> Uma mesma instituição (ex: Banco do Brasil) pode ser usada em vários links bancários de diferentes clientes.</p>
                    <p><i class="fas fa-image text-success me-2"></i> Adicionar um logo ajuda os clientes a identificarem visualmente as instituições em seus dashboards.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script para gerar slug automaticamente a partir do nome
    document.getElementById('name').addEventListener('blur', function() {
        const slugField = document.getElementById('slug');
        if (slugField.value === '') {
            // Só gera o slug se o campo estiver vazio
            const name = this.value;
            const slug = name.toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            
            slugField.value = slug;
        }
    });
</script>
@endpush
