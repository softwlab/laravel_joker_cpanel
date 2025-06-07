/**
 * Bank Template Editor - Scripts para gerenciamento dos campos de templates bancários
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando script do template bancário...');
    
    // Verificar se o Sortable foi carregado corretamente
    if (typeof Sortable === 'undefined') {
        console.error('Erro: Biblioteca Sortable.js não foi carregada!');
        return;
    }
    console.log('✅ Sortable.js carregado com sucesso');
    
    // --------------- FUNÇÕES AUXILIARES ---------------
    function handleFieldTypeChange(selectEl, optionsContainer) {
        if (selectEl && optionsContainer) {
            if (selectEl.value === 'select') {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }
    }
    
    function showSuccessMessage(message) {
        var alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = '<strong><i class="fas fa-check-circle me-2"></i>Sucesso!</strong> ' + 
                          message + 
                          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        
        var container = document.querySelector('.container-fluid');
        if (container) {
            container.insertBefore(alert, container.firstChild);
            
            setTimeout(function() {
                alert.classList.remove('show');
                setTimeout(function() { 
                    alert.remove(); 
                }, 150);
            }, 3000);
        }
    }
    
    // --------------- GESTÃO DOS TIPOS DE CAMPO ---------------
    // Para o modal de adicionar campo
    var fieldTypeSelect = document.getElementById('field_type');
    var optionsContainer = document.querySelector('.options-container');
    
    if (fieldTypeSelect) {
        fieldTypeSelect.addEventListener('change', function() {
            handleFieldTypeChange(this, optionsContainer);
        });
        // Inicializar o estado do campo de opções
        handleFieldTypeChange(fieldTypeSelect, optionsContainer);
    }
    
    // Para o modal de editar campo
    var editFieldTypeSelect = document.getElementById('edit_field_type');
    var editOptionsContainer = document.querySelector('.edit-options-container');
    
    if (editFieldTypeSelect) {
        editFieldTypeSelect.addEventListener('change', function() {
            handleFieldTypeChange(this, editOptionsContainer);
        });
    }
    
    // --------------- MODAL DE EDIÇÃO DE CAMPOS ---------------
    var editFieldButtons = document.querySelectorAll('.edit-field-btn');
    
    editFieldButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var fieldId = this.getAttribute('data-field-id');
            var fieldName = this.getAttribute('data-field-name');
            var fieldKey = this.getAttribute('data-field-key');
            var fieldType = this.getAttribute('data-field-type');
            var fieldOptions = this.getAttribute('data-field-options');
            var fieldRequired = this.getAttribute('data-field-required') === '1';
            var fieldOrder = this.getAttribute('data-field-order');
            
            document.getElementById('edit_name').value = fieldName;
            document.getElementById('edit_field_key').value = fieldKey;
            document.getElementById('edit_field_type').value = fieldType;
            document.getElementById('edit_options').value = fieldOptions || '';
            document.getElementById('edit_is_required').checked = fieldRequired;
            document.getElementById('edit_order').value = fieldOrder;
            
            var editFormEl = document.getElementById('edit-field-form');
            if (editFormEl && editFormEl.getAttribute('data-base-url')) {
                var baseUrl = editFormEl.getAttribute('data-base-url');
                editFormEl.action = baseUrl.replace('__id__', fieldId);
            }
            
            // Mostrar/ocultar campo de opções com base no tipo
            handleFieldTypeChange(editFieldTypeSelect, editOptionsContainer);
        });
    });
    
    // --------------- DRAG AND DROP COM SORTABLE ---------------
    function initSortable() {
        console.log('Inicializando reordenação de campos...');
        
        // Selecionar o tbody que contém as linhas arrastáveis
        var tbody = document.getElementById('sortable-fields');
        if (!tbody) {
            console.error('Erro: Elemento tbody#sortable-fields não foi encontrado!');
            return;
        }
        
        // Obter as informações necessárias para a requisição AJAX
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('Erro: Meta tag CSRF-token não encontrada!');
            return;
        }
        csrfToken = csrfToken.getAttribute('content');
        
        var reorderUrl = tbody.getAttribute('data-reorder-url');
        if (!reorderUrl) {
            console.error('Erro: URL de reordenação não encontrada!');
            return;
        }
        
        // Adicionar estilo de cursor aos manipuladores
        var handleCells = document.querySelectorAll('td.handle');
        handleCells.forEach(function(cell) {
            cell.style.cursor = 'grab';
        });
        
        // Log de depuração
        var rows = tbody.querySelectorAll('tr.sortable-row');
        console.log('Encontradas ' + rows.length + ' linhas para ordenar');
        
        try {
            // Criar nova instância do Sortable
            var sortable = Sortable.create(tbody, {
                animation: 150, // Duração da animação em ms
                handle: '.handle', // Seletor do manipulador de arrastar
                ghostClass: 'sortable-ghost', // Classe aplicada ao clone/fantasma durante o arraste
                chosenClass: 'sortable-chosen', // Classe aplicada ao elemento escolhido
                dragClass: 'sortable-drag', // Classe aplicada ao elemento sendo arrastado
                
                // Evento quando começa a arrastar
                onStart: function(evt) {
                    console.log('Iniciando arraste: linha #' + evt.oldIndex);
                    document.body.style.cursor = 'grabbing';
                },
                
                // Evento quando termina de arrastar
                onEnd: function(evt) {
                    console.log('Arraste finalizado: de ' + evt.oldIndex + ' para ' + evt.newIndex);
                    document.body.style.cursor = 'default';
                    
                    // Se a posição não mudou, não faz nada
                    if (evt.oldIndex === evt.newIndex) {
                        console.log('Posição não mudou, pulando atualização...');
                        return;
                    }
                    
                    // Calcular novas ordens para todos os campos
                    var newOrders = {};
                    var rows = tbody.querySelectorAll('tr.sortable-row');
                    
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        var fieldId = row.getAttribute('data-id');
                        var newOrder = (i + 1) * 10; // Usar múltiplos de 10 para facilitar reordenações futuras
                        
                        newOrders[fieldId] = newOrder;
                        
                        // Atualizar visualmente o número de ordem na tabela
                        var orderCell = row.querySelector('td.handle');
                        if (orderCell) {
                            orderCell.innerHTML = '<i class="fas fa-grip-vertical me-2"></i> ' + newOrder;
                        }
                    }
                    
                    console.log('Enviando novas ordens:', newOrders);
                    
                    // Enviar a nova ordem para o servidor via AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', reorderUrl, true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        showSuccessMessage('Campos reordenados com sucesso!');
                                    } else {
                                        console.error('Resposta do servidor sem sucesso');
                                    }
                                } catch (e) {
                                    console.error('Erro ao processar resposta JSON:', e);
                                }
                            } else {
                                console.error('Erro na requisição AJAX: ' + xhr.status);
                            }
                        }
                    };
                    
                    xhr.send(JSON.stringify({orders: newOrders}));
                }
            });
            
            console.log('✅ Sortable inicializado com sucesso!');
            
            // Retornar instância para caso seja necessário manipular depois
            return sortable;
        } catch (error) {
            console.error('❌ Erro ao inicializar Sortable:', error);
            return null;
        }
    }
    
    // Inicializa o componente de drag and drop
    try {
        initSortable();
    } catch (error) {
        console.error('Erro ao inicializar Sortable:', error);
    }
});
