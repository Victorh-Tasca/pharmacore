document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        // Campos específicos deste formulário
        const hiddenIdInput = document.getElementById('item_id');
        const nomeInput = document.getElementById('nome');
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('telefone');
        const cidadeInput = document.getElementById('cidade');

        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                const tableRow = button.closest('tr');
                
                // Pega os dados dos atributos data-* da linha
                const id = tableRow.getAttribute('data-id');
                const nome = tableRow.getAttribute('data-nome'); 
                const cpf = tableRow.getAttribute('data-cpf'); 
                const telefone = tableRow.getAttribute('data-telefone'); 
                const cidade = tableRow.getAttribute('data-cidade'); 
                
                modalTitle.textContent = 'Editar Paciente';
                hiddenIdInput.value = id;
                nomeInput.value = nome;
                cpfInput.value = cpf;
                telefoneInput.value = telefone;
                cidadeInput.value = cidade;
                
            } else {
                modalTitle.textContent = 'Adicionar Paciente';
                crudForm.reset(); 
                hiddenIdInput.value = ''; 
            }
        });
    }

});