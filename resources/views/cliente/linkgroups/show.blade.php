@extends('layouts.cliente')

@section('title', 'Detalhes do Grupo Organizado')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $linkGroup->title }}</h2>
        <div>
            <a href="{{ route('cliente.banks') }}" class="btn btn-outline-secondary">
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
        <!-- Informações do Grupo -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Grupo Organizado</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nome do Grupo:</strong> {{ $linkGroup->title }}</p>
                    <p><strong>Finalidade:</strong> {{ $linkGroup->description }}</p>
                    <p><strong>Categoria:</strong> {{ $linkGroup->category }}</p>
                    <p><strong>Links Bancários Associados:</strong> {{ $linkGroup->banks->count() }}</p>
                    <p><strong>Itens Organizados:</strong> {{ $linkGroup->items->count() }}</p>
                    <p><strong>Criado em:</strong> {{ $linkGroup->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Atualizado em:</strong> {{ $linkGroup->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Links Bancários Associados -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Links Bancários Associados</h5>
                </div>
                <div class="card-body">
                    @if($linkGroup->banks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome do Link</th>
                                        <th>Instituição Bancária</th>
                                        <th>Estado</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($linkGroup->banks as $bank)
                                    <tr>
                                        <td>{{ $bank->name }}</td>
                                        <td>{{ $bank->template->name ?? 'Não definido' }}</td>
                                        <td>
                                            @if($bank->active)
                                                <span class="badge bg-success text-white">Ativo</span>
                                            @else
                                                <span class="badge bg-danger text-white">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('cliente.banks.show', $bank->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Este grupo organizado ainda não possui links bancários associados. Você pode adicionar links existentes ao grupo ou criar novos links bancários.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Itens do Grupo -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Itens do Grupo</h5>
                </div>
                <div class="card-body">
                    @if($linkGroup->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Descrição</th>
                                        <th>Ordem</th>
                                        <th>Tipo</th>
                                        <th>Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($linkGroup->items as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($item->description, 50) }}</td>
                                        <td>{{ $item->order }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>
                                            @if($item->url)
                                                <a href="{{ $item->url }}" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> Abrir
                                                </a>
                                            @else
                                                <span class="text-muted">Não disponível</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Este grupo não possui itens de link.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
