document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        // Campos específicos deste formulário
        const hiddenIdInput = document.getElementById('item_id');
        const codigoInput = document.getElementById('codigo_classe');
        const nomeInput = document.getElementById('nome');

        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                const tableRow = button.closest('tr');
                
                // Pega os dados dos atributos data-* da linha
                const id = tableRow.getAttribute('data-id');
                const codigo = tableRow.getAttribute('data-codigo_classe');
                const nome = tableRow.getAttribute('data-nome'); 
                
                modalTitle.textContent = 'Editar Classe';
                hiddenIdInput.value = id;
                codigoInput.value = codigo;
                nomeInput.value = nome;
                
            } else {
                modalTitle.textContent = 'Adicionar Classe';
                crudForm.reset(); 
                hiddenIdInput.value = ''; 
            }
        });
    }

});