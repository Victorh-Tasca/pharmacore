document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        
        // --- Campos específicos deste formulário ---
        const hiddenIdInput = document.getElementById('item_id');
        const nomeInput = document.getElementById('nome');
        const descricaoInput = document.getElementById('descricao');
        
        // Pega TODOS os checkboxes de permissões
        const checkboxesPermissoes = document.querySelectorAll('input[name="permissoes"]');

        
        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                modalTitle.textContent = 'Editar Papel';
                const tableRow = button.closest('tr');
                
                // Pega os dados dos atributos data-*
                const id = tableRow.getAttribute('data-id');
                const nome = tableRow.getAttribute('data-nome'); 
                const descricao = tableRow.getAttribute('data-descricao'); 
                
                // Pega o JSON string de permissões e converte para um Array
                // Ex: "[1, 2, 3]" -> [1, 2, 3]
                const permissoes = JSON.parse(tableRow.getAttribute('data-permissoes') || '[]'); 

                // Preenche os campos
                hiddenIdInput.value = id;
                nomeInput.value = nome;
                descricaoInput.value = descricao;

                // --- Lógica dos Checkboxes de Permissões ---
                checkboxesPermissoes.forEach(function(checkbox) {
                    // Converte o value="1" para número 1
                    const checkboxId = parseInt(checkbox.value, 10);
                    
                    // Verifica se o ID do checkbox está no array de permissões
                    if (permissoes.includes(checkboxId)) {
                        checkbox.checked = true;
                    } else {
                        checkbox.checked = false;
                    }
                });
                
            } else {
                // --- MODO ADICIONAR ---
                modalTitle.textContent = 'Adicionar Papel';
                crudForm.reset(); // Limpa todos os inputs e checkboxes
                hiddenIdInput.value = ''; 
            }
        });
    }

});