document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        // Campos específicos deste formulário
        const hiddenIdInput = document.getElementById('item_id');
        const nomeInput = document.getElementById('nome');

        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                const tableRow = button.closest('tr');
                
                // Pega os dados dos atributos data-* da linha
                const id = tableRow.getAttribute('data-id');
                const nome = tableRow.getAttribute('data-nome'); 
                
                modalTitle.textContent = 'Editar Laboratório';
                hiddenIdInput.value = id;
                nomeInput.value = nome;
                
            } else {
                modalTitle.textContent = 'Adicionar Laboratório';
                crudForm.reset(); 
                hiddenIdInput.value = ''; 
            }
        });
    }

});