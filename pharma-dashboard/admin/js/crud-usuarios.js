document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    if (crudModal) {
        
        const modalTitle = document.getElementById('modalTitle');
        const crudForm = document.getElementById('crudForm');
        
        // --- Campos específicos deste formulário ---
        const hiddenIdInput = document.getElementById('item_id');
        const nomeInput = document.getElementById('nome');
        const celularInput = document.getElementById('celular');
        const emailInput = document.getElementById('email');
        const loginInput = document.getElementById('login');
        const senhaInput = document.getElementById('senha');
        const senhaConfirmInput = document.getElementById('senha_confirm');
        const ativoSwitch = document.getElementById('ativo');
        
        // Pega TODOS os checkboxes de papéis
        const checkboxesPapeis = document.querySelectorAll('input[name="papeis"]');

        
        crudModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                modalTitle.textContent = 'Editar Usuário';
                const tableRow = button.closest('tr');
                
                // Pega os dados dos atributos data-*
                const id = tableRow.getAttribute('data-id');
                const nome = tableRow.getAttribute('data-nome'); 
                const celular = tableRow.getAttribute('data-celular'); 
                const email = tableRow.getAttribute('data-email'); 
                const login = tableRow.getAttribute('data-login'); 
                const ativo = tableRow.getAttribute('data-ativo') === 'true'; // Converte string 'true' para booleano
                
                // Pega o JSON string de papéis e converte para um Array
                // Ex: "[1, 3]" -> [1, 3]
                const papeis = JSON.parse(tableRow.getAttribute('data-papeis') || '[]'); 

                // Preenche os campos
                hiddenIdInput.value = id;
                nomeInput.value = nome;
                celularInput.value = celular;
                emailInput.value = email;
                loginInput.value = login;
                ativoSwitch.checked = ativo;
                
                // Limpa senhas
                senhaInput.value = '';
                senhaConfirmInput.value = '';

                // --- Lógica dos Checkboxes de Papéis ---
                checkboxesPapeis.forEach(function(checkbox) {
                    // Converte o value="1" para número 1
                    const checkboxId = parseInt(checkbox.value, 10);
                    
                    // Verifica se o ID do checkbox está no array de papéis
                    if (papeis.includes(checkboxId)) {
                        checkbox.checked = true;
                    } else {
                        checkbox.checked = false;
                    }
                });
                
            } else {
                // --- MODO ADICIONAR ---
                modalTitle.textContent = 'Adicionar Usuário';
                crudForm.reset(); // Limpa todos os inputs
                hiddenIdInput.value = ''; 
                
                // Garante que o usuário é 'Ativo' por padrão ao criar
                ativoSwitch.checked = true; 
            }
        });
    }

});