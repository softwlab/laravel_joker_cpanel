@extends('layouts.app')

@section('title', $linkGroup->title)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cliente.linkgroups.index') }}">Meus Grupos Organizados</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $linkGroup->title }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $linkGroup->title }}</h1>
        <div>
            <a href="{{ route('cliente.linkgroups.edit', $linkGroup->id) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-edit"></i> Configurar Grupo
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus"></i> Adicionar Item ao Grupo
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informações do Grupo</h5>
                <span class="badge {{ $linkGroup->active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $linkGroup->active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <p>{{ $linkGroup->description }}</p>
            <div class="small text-muted mt-2">
                <div>Criado em: {{ $linkGroup->created_at->format('d/m/Y H:i') }}</div>
                <div>Última atualização: {{ $linkGroup->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <h2 class="mb-3">Links</h2>

    @if($linkGroup->items->isEmpty())
    <div class="alert alert-info">
        Este grupo ainda não possui links. Adicione seu primeiro link clicando no botão "Adicionar Link".
    </div>
    @else
    <div class="card">
        <div class="list-group list-group-flush sortable-items" id="linkItems">
            @foreach($linkGroup->items as $item)
            <div class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $item->id }}">
                <div class="d-flex align-items-center">
                    <div class="me-3 handle" style="cursor: grab;">
                        <i class="fas fa-grip-vertical text-muted"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            @if($item->icon)
                            <i class="fas fa-{{ $item->icon }} me-2"></i>
                            @endif
                            <h5 class="mb-0 {{ !$item->active ? 'text-muted' : '' }}">{{ $item->title }}</h5>
                            @if(!$item->active)
                            <span class="badge bg-secondary ms-2">Inativo</span>
                            @endif
                        </div>
                        <a href="{{ $item->url }}" target="_blank" class="small text-truncate d-block" style="max-width: 500px;">
                            {{ $item->url }}
                        </a>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary edit-item" 
                            data-id="{{ $item->id }}"
                            data-title="{{ $item->title }}"
                            data-url="{{ $item->url }}"
                            data-icon="{{ $item->icon }}"
                            data-order="{{ $item->order }}"
                            data-active="{{ $item->active }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('cliente.linkgroups.items.destroy', [$linkGroup->id, $item->id]) }}" method="POST" class="d-inline delete-item-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Modal para adicionar novo item -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('cliente.linkgroups.items.store', $linkGroup->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Adicionar Novo Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                            @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Ícone (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="ex: link, bank, home">
                            </div>
                            <small class="text-muted">Insira o nome do ícone FontAwesome sem o prefixo "fa-"</small>
                            @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                            <label class="form-check-label" for="active">Ativo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar item -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemModalLabel">Editar Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_url" class="form-label">URL</label>
                            <input type="text" class="form-control" id="edit_url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Ícone (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="ex: link, bank, home">
                            </div>
                            <small class="text-muted">Insira o nome do ícone FontAwesome sem o prefixo "fa-"</small>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="edit_active" name="active">
                            <label class="form-check-label" for="edit_active">Ativo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Drag-and-drop reordering
        const sortableList = document.getElementById('linkItems');
        if (sortableList) {
            new Sortable(sortableList, {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    updateOrder();
                }
            });
        }

        // Update order when items are reordered
        function updateOrder() {
            const items = document.querySelectorAll('#linkItems .list-group-item');
            const itemIds = Array.from(items).map(item => item.dataset.id);
            
            fetch('{{ route("cliente.linkgroups.items.reorder", $linkGroup->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: itemIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Order updated successfully');
                } else {
                    console.error('Error updating order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Edit item
        const editButtons = document.querySelectorAll('.edit-item');
        const editItemForm = document.getElementById('editItemForm');
        const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const url = this.getAttribute('data-url');
                const icon = this.getAttribute('data-icon');
                const active = this.getAttribute('data-active') === '1';

                document.getElementById('edit_title').value = title;
                document.getElementById('edit_url').value = url;
                document.getElementById('edit_icon').value = icon;
                document.getElementById('edit_active').checked = active;

                // Como o itemId vem do JavaScript, precisamos construir a URL manualmente
                const baseUrl = '{{ url("/cliente/linkgroups/{$linkGroup->id}/items") }}';
                editItemForm.action = `${baseUrl}/${itemId}`;
                editItemModal.show();
            });
        });

        // Delete confirmation
        const deleteForms = document.querySelectorAll('.delete-item-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!confirm('Tem certeza que deseja excluir este link?')) {
                    event.preventDefault();
                }
            });
        });
    });
</script>
@endsection
