@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Visualizar Template de Banco</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ $template->name }}</h6>
            <div>
                <a href="{{ route('admin.bank-templates.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('admin.bank-templates.edit', $template->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informações do Template</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Nome</th>
                            <td>{{ $template->name }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>{{ $template->slug }}</td>
                        </tr>
                        <tr>
                            <th>Descrição</th>
                            <td>{{ $template->description ?? 'Não definido' }}</td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td>{{ $template->template_url ?? 'Não definido' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($template->active)
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-danger">Inativo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Criado em</th>
                            <td>{{ $template->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizado em</th>
                            <td>{{ $template->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h5>Campos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Label</th>
                                    <th>Tipo</th>
                                    <th>Obrigatório</th>
                                    <th>Ordem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($template->fields->sortBy('order') as $field)
                                    <tr>
                                        <td>{{ $field->field_name }}</td>
                                        <td>{{ $field->field_label }}</td>
                                        <td>{{ $field->field_type }}</td>
                                        <td>
                                            @if($field->required)
                                                <span class="badge badge-success">Sim</span>
                                            @else
                                                <span class="badge badge-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>{{ $field->order }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum campo definido</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
