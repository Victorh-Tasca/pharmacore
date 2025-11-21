document.addEventListener("DOMContentLoaded", () => {
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const modalTitle = document.getElementById('modalTitle');
    const tabelaCorpo = document.querySelector("table tbody");
    const containerPapeis = document.querySelector('[data-grupo-papeis]').parentNode;

    const API_URL = "/pharmacore/api/usuarios/";
    let listaDePapeis = [];

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

    async function carregarPapeis() {
        const data = await fetchData('/pharmacore/api/papeis/read.php');
        if (!data) return;
        
        listaDePapeis = data;
        containerPapeis.innerHTML = '';
        data.forEach(papel => {
            containerPapeis.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${papel.id}" id="papel_${papel.id}" name="papeis">
                    <label class="form-check-label" for="papel_${papel.id}">
                        ${papel.nome}
                    </label>
                </div>
            `;
        });
    }

    function getBadge(ativo) {
        return ativo ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>';
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
                <td data-label="Login">${item.login}</td>
                <td data-label="Email">${item.email}</td>
                <td data-label="Status">${getBadge(item.ativo)}</td>
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
        document.getElementById('senha').placeholder = 'Digite para definir a senha';
        document.getElementById('senhaHelp').textContent = 'Obrigatório ao criar. Deixe em branco na edição para não alterar.';

        if (acao === 'editar' && button) {
            modalTitle.textContent = 'Editar Usuário';
            const item = JSON.parse(button.closest('tr').dataset.item);
            
            document.getElementById('item_id').value = item.id;
            document.getElementById('nome').value = item.nome;
            document.getElementById('celular').value = item.celular;
            document.getElementById('email').value = item.email;
            document.getElementById('login').value = item.login;
            document.getElementById('ativo').checked = item.ativo;

            document.getElementById('senha').placeholder = 'Deixe em branco para não alterar';
            document.getElementById('senha').required = false;
            document.getElementById('senha_confirm').required = false;

            document.querySelectorAll('input[name="papeis"]').forEach(checkbox => {
                checkbox.checked = item.papeis.includes(parseInt(checkbox.value));
            });

        } else {
            modalTitle.textContent = 'Adicionar Usuário';
            document.getElementById('senha').required = true;
            document.getElementById('senha_confirm').required = true;
        }
    });

    crudForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const senha = document.getElementById('senha').value;
        const senhaConfirm = document.getElementById('senha_confirm').value;

        if (senha !== senhaConfirm) {
            alert('As senhas não conferem.');
            return;
        }
        
        const id = document.getElementById('item_id').value;
        const papeisSelecionados = Array.from(document.querySelectorAll('input[name="papeis"]:checked'))
                                       .map(cb => parseInt(cb.value));
        
        const data = {
            id: id || null,
            nome: document.getElementById('nome').value,
            celular: document.getElementById('celular').value,
            email: document.getElementById('email').value,
            login: document.getElementById('login').value,
            ativo: document.getElementById('ativo').checked,
            papeis: papeisSelecionados
        };

        if (senha) {
            data.senha = senha;
        }
        
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
            
            if (confirm('Tem certeza que deseja excluir este usuário?')) {
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

    carregarPapeis();
    carregarTabela();
});