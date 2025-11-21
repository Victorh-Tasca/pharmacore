document.addEventListener("DOMContentLoaded", () => {
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const modalTitle = document.getElementById('modalTitle');
    const tabelaCorpo = document.querySelector("table tbody");

    const API_URL = "/pharmacore/api/papeis/";
    let listaDePermissoes = [];

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

    async function carregarPermissoes() {
        const data = [
            { id: 1, codigo: 'acesso_sistema', nome: 'Acesso básico ao sistema' },
            { id: 2, codigo: 'relatorios', nome: 'Visualizar relatórios' },
            { id: 3, codigo: 'entradas', nome: 'Registrar entradas no estoque' },
            { id: 4, codigo: 'saidas', nome: 'Registrar dispensações/saídas' },
            { id: 5, codigo: 'estoque', nome: 'Gerenciar medicamentos, lotes, etc' }
        ];
        listaDePermissoes = data;
        
        const container = document.querySelector('.modal-body .col-12');
        container.innerHTML = '<label class="form-label">Permissões para este Papel</label>';
        data.forEach(perm => {
            container.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${perm.id}" id="perm_${perm.id}" name="permissoes">
                    <label class="form-check-label" for="perm_${perm.id}">${perm.codigo} (${perm.nome})</label>
                </div>
            `;
        });
    }

    async function carregarTabela() {
        const data = await fetchData(API_URL + 'read.php');
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.dataset.item = JSON.stringify(item);
            tr.innerHTML = `
                <td data-label="Nome">${item.nome}</td>
                <td data-label="Descrição">${item.descricao || ''}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar" 
                            data-bs-toggle="modal" 
                            data-bs-target="#crudModal" 
                            data-acao="editar">
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
        const acao = button ? button.getAttribute('data-acao') : 'adicionar';
        
        crudForm.reset();
        document.getElementById('item_id').value = '';
        document.querySelectorAll('input[name="permissoes"]').forEach(cb => cb.checked = false);

        if (acao === 'editar' && button) {
            modalTitle.textContent = 'Editar Papel';
            const item = JSON.parse(button.closest('tr').dataset.item);
            
            document.getElementById('item_id').value = item.id;
            document.getElementById('nome').value = item.nome;
            document.getElementById('descricao').value = item.descricao;
            
            document.querySelectorAll('input[name="permissoes"]').forEach(checkbox => {
                checkbox.checked = item.permissoes.includes(parseInt(checkbox.value));
            });

        } else {
            modalTitle.textContent = 'Adicionar Papel';
        }
    });

    crudForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('item_id').value;
        const permissoesSelecionadas = Array.from(document.querySelectorAll('input[name="permissoes"]:checked'))
                                             .map(cb => parseInt(cb.value));
        
        const data = {
            id: id || null,
            nome: document.getElementById('nome').value,
            descricao: document.getElementById('descricao').value,
            permissoes: permissoesSelecionadas
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
            
            if (confirm('Tem certeza que deseja excluir este papel?')) {
                const result = await fetchData(API_URL + 'delete.php', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                });

                if (result) {
                    await carregarTabela();
                }
            }
        }
    });

    carregarPermissoes();
    carregarTabela();
});