document.addEventListener("DOMContentLoaded", () => {
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const modalTitle = document.getElementById('modalTitle');
    const tabelaCorpo = document.querySelector("table tbody");

    const API_URL = "/pharmacore/api/laboratorios/";

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
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${item.id}" data-nome="${item.nome}">
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
            modalTitle.textContent = 'Editar Laboratório';
            const item = button.closest('tr').querySelector('.btn-editar');
            
            document.getElementById('item_id').value = item.dataset.id;
            document.getElementById('nome').value = item.dataset.nome;
        } else {
            modalTitle.textContent = 'Adicionar Laboratório';
        }
    });

    crudForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('item_id').value;
        const nome = document.getElementById('nome').value;
        
        const data = { nome };
        let url = API_URL + 'create.php';
        
        if (id) {
            data.id = id;
            url = API_URL + 'update.php';
        }

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
        if (e.target.classList.contains('btn-excluir') || e.target.closest('.btn-excluir')) {
            const button = e.target.closest('.btn-excluir');
            const id = button.dataset.id;
            
            if (confirm('Tem certeza que deseja excluir este item?')) {
                const result = await fetchData(API_URL + 'delete.php', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                });

                if (result) {
                    await carregarTabela();
                }
            }
        } else if (e.target.classList.contains('btn-editar') || e.target.closest('.btn-editar')) {
            const button = e.target.closest('.btn-editar');
            
            crudForm.reset();
            document.getElementById('item_id').value = '';
            
            modalTitle.textContent = 'Editar Laboratório';
            document.getElementById('item_id').value = button.dataset.id;
            document.getElementById('nome').value = button.dataset.nome;
            
            crudModal.show();
        }
    });

    carregarTabela();
});