document.addEventListener("DOMContentLoaded", () => {
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const modalTitle = document.getElementById('modalTitle');
    const tabelaCorpo = document.querySelector("table tbody");

    const API_URL = "/pharmacore/api/pacientes/";

    async function fetchData(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: { 'Content-Type': 'application/json', ...options.headers },
            });
            
            if (response.status === 401) {
                window.location.href = '/pharma-login/index.html';
                return Promise.reject('Sessão inválida');
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro na requisição');
            }
            return data;
        } catch (error) {
            console.error(error.message);
            alert(error.message);
            return null;
        }
    }

    async function carregarTabela() {
        const data = await fetchData(API_URL + 'read.php');
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Nome">${item.nome}</td>
                <td data-label="CPF">${item.cpf}</td>
                <td data-label="Telefone">${item.telefone || ''}</td>
                <td data-label="Cidade">${item.cidade || ''}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar" 
                        data-id="${item.id}" 
                        data-nome="${item.nome}"
                        data-cpf="${item.cpf}"
                        data-telefone="${item.telefone || ''}"
                        data-cidade="${item.cidade || ''}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="${item.id}">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            `;
            tabelaCorpo.appendChild(tr);
        });
    }

    document.getElementById('crudModal').addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const acao = button.getAttribute('data-acao');
        
        crudForm.reset();
        document.getElementById('item_id').value = '';

        if (acao === 'editar') {
            modalTitle.textContent = 'Editar Paciente';
            const item = button;
            
            document.getElementById('item_id').value = item.dataset.id;
            document.getElementById('nome').value = item.dataset.nome;
            document.getElementById('cpf').value = item.dataset.cpf;
            document.getElementById('telefone').value = item.dataset.telefone;
            document.getElementById('cidade').value = item.dataset.cidade;
        } else {
            modalTitle.textContent = 'Adicionar Paciente';
        }
    });

    crudForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('item_id').value;
        const data = {
            id: id || null,
            nome: document.getElementById('nome').value,
            cpf: document.getElementById('cpf').value,
            telefone: document.getElementById('telefone').value,
            cidade: document.getElementById('cidade').value
        };
        
        let url = id ? API_URL + 'update.php' : API_URL + 'create.php';
        
        const result = await fetchData(url, {
            method: 'POST',
            body: JSON.stringify(data),
        });

        if (result) {
            crudModal.hide();
            await carregarTabela();
        }
    });

    tabelaCorpo.addEventListener('click', async (e) => {
        const btnExcluir = e.target.closest('.btn-excluir');
        if (btnExcluir) {
            const id = btnExcluir.dataset.id;
            
            if (confirm('Tem certeza que deseja excluir este paciente?')) {
                const result = await fetchData(API_URL + 'delete.php', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                });

                if (result) {
                    await carregarTabela();
                }
            }
        }
        
        const btnEditar = e.target.closest('.btn-editar');
        if (btnEditar) {
             crudForm.reset();
            document.getElementById('item_id').value = '';
            
            modalTitle.textContent = 'Editar Paciente';
            document.getElementById('item_id').value = btnEditar.dataset.id;
            document.getElementById('nome').value = btnEditar.dataset.nome;
            document.getElementById('cpf').value = btnEditar.dataset.cpf;
            document.getElementById('telefone').value = btnEditar.dataset.telefone;
            document.getElementById('cidade').value = btnEditar.dataset.cidade;
            
            crudModal.show();
        }
    });

    carregarTabela();
});