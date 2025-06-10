@extends('layouts.cliente')

@section('title', 'Templates Bancários')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Templates Bancários</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('cliente.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
    
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
    

    <!-- Banner informativo com estilo moderno -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary p-3 me-4 text-white">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Templates Bancários</h5>
                            <p class="mb-0 text-muted">Escolha entre os modelos disponíveis para criar seus registros DNS personalizados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de templates com design moderno -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
    @if($templates->count() > 0)
        @foreach($templates as $template)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-header bg-white border-bottom-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-primary mb-0">{{ $template->name }}</h5>
                        <span class="badge rounded-pill bg-primary px-3 py-2">{{ $template->banks_count }} uso(s)</span>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <p class="card-text text-muted mb-3">{{ $template->description ?? 'Sem descrição disponível' }}</p>
                    
                    @if($template->fields->count() > 0)
                    <h6 class="fw-bold mb-3">Campos do Template</h6>
                    <div class="mb-4">
                        @foreach($template->fields->take(4) as $field)
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-2">
                                <i class="fas fa-circle-check text-success"></i>
                            </div>
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <span>{{ $field->label }}</span>
                                @if($field->required) 
                                    <span class="badge bg-danger rounded-pill ms-2">Obrigatório</span> 
                                @endif
                            </div>
                        </div>
                        @endforeach
                        
                        @if($template->fields->count() > 4)
                        <div class="d-flex align-items-center text-muted">
                            <div class="me-2">
                                <i class="fas fa-ellipsis-h"></i>
                            </div>
                            <span>mais {{ $template->fields->count() - 4 }} campo(s)...</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top-0 text-center p-4">
                    <a href="{{ route('cliente.dashboard') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus-circle me-2"></i> Usar Template
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="alert alert-info p-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <p class="mb-0">Não há templates bancários disponíveis no momento.</p>
                </div>
            </div>
        </div>
    @endif
    </div>
    
    <!-- Estilos adicionais para melhorar a aparência -->
    <style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .3s ease;
    }
    .card {
        border-radius: 10px;
        transition: all .3s ease;
    }
    .badge {
        font-weight: 500;
    }
    </style>


@endsection