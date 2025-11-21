document.addEventListener("DOMContentLoaded", () => {
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const modalTitle = document.getElementById('modalTitle');
    const tabelaCorpo = document.querySelector("table tbody");

    const API_URL = "/pharmacore/api/medicamentos/";
    
    const selectClasses = document.getElementById('classe_terapeutica_id');
    const selectLaboratorios = document.getElementById('laboratorio_id');

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

    async function carregarDropdowns() {
        const [classes, laboratorios] = await Promise.all([
            fetchData('/pharmacore/api/classes_terapeuticas/read.php'),
            fetchData('/pharmacore/api/laboratorios/read.php')
        ]);

        if (classes) {
            selectClasses.innerHTML = '<option value="">Selecione...</option>';
            classes.forEach(item => {
                selectClasses.innerHTML += `<option value="${item.id}">${item.nome}</option>`;
            });
        }
        
        if (laboratorios) {
            selectLaboratorios.innerHTML = '<option value="">Selecione...</option>';
            laboratorios.forEach(item => {
                selectLaboratorios.innerHTML += `<option value="${item.id}">${item.nome}</option>`;
            });
        }
    }

    function getBadge(text) {
        if (!text) return '';
        if (text === 'Sim' || text === true) return '<span class="badge bg-success">Sim</span>';
        if (text === 'Não' || text === false) return '<span class="badge bg-secondary">Não</span>';
        if (text === 'sem_tarja') return '<span class="badge bg-secondary">Sem Tarja</span>';
        if (text === 'tarja_amarela') return '<span class="badge bg-warning text-dark">Tarja Amarela</span>';
        if (text === 'tarja_vermelha') return '<span class="badge bg-danger">Tarja Vermelha</span>';
        if (text === 'tarja_preta') return '<span class="badge bg-dark">Tarja Preta</span>';
        return text;
    }

    async function carregarTabela() {
        const data = await fetchData(API_URL + 'read.php');
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.dataset.item = JSON.stringify(item); 
            tr.innerHTML = `
                <td data-label="Código">${item.codigo}</td>
                <td data-label="Nome">${item.nome}</td>
                <td data-label="Classe">${item.classe_terapeutica || ''}</td>
                <td data-label="Laboratório">${item.laboratorio || ''}</td>
                <td data-label="Tarja">${getBadge(item.tarja)}</td>
                <td data-label="Ativo">${getBadge(item.ativo)}</td>
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

        if (acao === 'editar' && button) {
            modalTitle.textContent = 'Editar Medicamento';
            const item = JSON.parse(button.closest('tr').dataset.item);
            
            document.getElementById('item_id').value = item.id;
            document.getElementById('codigo').value = item.codigo;
            document.getElementById('nome').value = item.nome;
            document.getElementById('classe_terapeutica_id').value = item.classe_terapeutica_id;
            document.getElementById('laboratorio_id').value = item.laboratorio_id;
            document.getElementById('tarja').value = item.tarja;
            document.getElementById('forma_retirada').value = item.forma_retirada;
            document.getElementById('forma_fisica').value = item.forma_fisica;
            document.getElementById('apresentacao').value = item.apresentacao;
            document.getElementById('unidade_base').value = item.unidade_base;
            document.getElementById('dosagem_valor').value = item.dosagem_valor;
            document.getElementById('dosagem_unidade').value = item.dosagem_unidade;
            document.getElementById('limite_minimo').value = item.limite_minimo;
            document.getElementById('generico').checked = item.generico;

        } else {
            modalTitle.textContent = 'Adicionar Medicamento';
        }
    });

    crudForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('item_id').value;
        const data = {
            id: id || null,
            codigo: document.getElementById('codigo').value,
            nome: document.getElementById('nome').value,
            classe_terapeutica_id: document.getElementById('classe_terapeutica_id').value,
            laboratorio_id: document.getElementById('laboratorio_id').value,
            tarja: document.getElementById('tarja').value,
            forma_retirada: document.getElementById('forma_retirada').value,
            forma_fisica: document.getElementById('forma_fisica').value,
            apresentacao: document.getElementById('apresentacao').value,
            unidade_base: document.getElementById('unidade_base').value,
            dosagem_valor: document.getElementById('dosagem_valor').value,
            dosagem_unidade: document.getElementById('dosagem_unidade').value,
            limite_minimo: document.getElementById('limite_minimo').value,
            generico: document.getElementById('generico').checked
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
            
            if (confirm('Tem certeza que deseja excluir este medicamento?')) {
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

    carregarDropdowns();
    carregarTabela();
});