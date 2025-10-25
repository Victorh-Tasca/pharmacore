document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        const hiddenIdInput = document.getElementById('item_id');

        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                modalTitle.textContent = 'Editar Medicamento';
                
                // NOTA: Em um aplicativo real, aqui você pegaria o ID
                // const id = button.closest('tr').getAttribute('data-id');
                // hiddenIdInput.value = id;
                
                // E faria uma chamada (fetch) para a API (back-end)
                // para buscar os dados completos do medicamento e preencher
                // todos os campos do formulário (dosagem, tarja, selects, etc.)
                // Ex: preencherFormularioComDadosDaAPI(id);

            } else {
                modalTitle.textContent = 'Adicionar Medicamento';
                crudForm.reset(); 
                hiddenIdInput.value = ''; 
            }
        });
    }

});