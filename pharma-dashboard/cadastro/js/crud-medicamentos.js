document.addEventListener("DOMContentLoaded", function() {

    const crudModal = document.getElementById('crudModal');
    const modalTitle = document.getElementById('modalTitle');
    const crudForm = document.getElementById('crudForm');
    const hiddenIdInput = document.getElementById('item_id');
    const tableBody = document.getElementById('medicamentos-table-body');

    const API_BASE_URL = '../../api/medicamentos/';

    /**
     * Carrega a lista de medicamentos da API e popula a tabela.
     */
    async function loadMedicamentos() {
        try {
            const response = await fetch(API_BASE_URL + 'read.php');
            if (!response.ok) {
                throw new Error('Falha ao buscar medicamentos.');
            }
            const medicamentos = await response.json();

            tableBody.innerHTML = ''; // Limpa a tabela antes de popular

            if (medicamentos.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Nenhum medicamento encontrado.</td></tr>';
                return;
            }

            medicamentos.forEach(medicamento => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', medicamento.id);
                tr.innerHTML = `
                    <td>${medicamento.codigo}</td>
                    <td>${medicamento.nome}</td>
                    <td>${medicamento.laboratorio_nome || 'N/A'}</td>
                    <td>${medicamento.classe_terapeutica_nome || 'N/A'}</td>
                    <td><span class="badge bg-secondary">${medicamento.tarja.replace('_', ' ').toUpperCase()}</span></td>
                    <td><span class="badge ${medicamento.ativo ? 'bg-success' : 'bg-danger'}">${medicamento.ativo ? 'Ativo' : 'Inativo'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#crudModal" data-acao="editar" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-acao="deletar" title="Desativar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

        } catch (error) {
            console.error('Erro ao carregar medicamentos:', error);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados.</td></tr>`;
        }
    }

    /**
     * Preenche o formulário de edição com dados de um medicamento específico.
     * @param {string} id - O ID do medicamento.
     */
    async function preencherFormularioParaEdicao(id) {
        try {
            const response = await fetch(`${API_BASE_URL}read_one.php?id=${id}`);
            if (!response.ok) throw new Error('Medicamento não encontrado.');
            
            const data = await response.json();
            
            // Preenche todos os campos do formulário com os dados recebidos
            for (const key in data) {
                const field = crudForm.elements[key];
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = !!data[key];
                    } else {
                        field.value = data[key];
                    }
                }
            }
            hiddenIdInput.value = data.id; // Garante que o ID oculto está setado

        } catch (error) {
            console.error("Erro ao buscar dados do medicamento:", error);
            alert("Não foi possível carregar os dados para edição.");
            bootstrap.Modal.getInstance(crudModal).hide();
        }
    }

    // Event listener para a abertura do modal
    if (crudModal) {
        crudModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const acao = button.getAttribute('data-acao');

            if (acao === 'editar') {
                modalTitle.textContent = 'Editar Medicamento';
                const id = button.closest('tr').getAttribute('data-id');
                preencherFormularioParaEdicao(id);
            } else {
                modalTitle.textContent = 'Adicionar Medicamento';
                crudForm.reset();
                hiddenIdInput.value = '';
            }
        });
    }

    // Event listener para o envio do formulário (Criar/Atualizar)
    if (crudForm) {
        crudForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const id = hiddenIdInput.value;
            const url = id ? `${API_BASE_URL}update.php` : `${API_BASE_URL}create.php`;
            
            const formData = new FormData(crudForm);
            const data = Object.fromEntries(formData.entries());

            // Converte valores de checkbox para booleano
            data.generico = crudForm.elements['generico'].checked;
            data.ativo = crudForm.elements['ativo'] ? crudForm.elements['ativo'].checked : true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    bootstrap.Modal.getInstance(crudModal).hide();
                    loadMedicamentos(); // Recarrega a tabela
                } else {
                    throw new Error(result.message || 'Ocorreu um erro.');
                }
            } catch (error) {
                console.error('Erro ao salvar medicamento:', error);
                alert(`Erro: ${error.message}`);
            }
        });
    }

    // Event listener para deleção (usando delegação de evento na tabela)
    if (tableBody) {
        tableBody.addEventListener('click', async function(event) {
            const target = event.target.closest('button[data-acao="deletar"]');
            if (!target) return;

            const id = target.closest('tr').getAttribute('data-id');
            if (!confirm(`Tem certeza que deseja desativar o medicamento com ID ${id}?`)) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}delete.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    loadMedicamentos(); // Recarrega a tabela
                } else {
                    throw new Error(result.message || 'Ocorreu um erro ao desativar.');
                }
            } catch (error) {
                console.error('Erro ao desativar medicamento:', error);
                alert(`Erro: ${error.message}`);
            }
        });
    }

    // Carrega os dados iniciais ao carregar a página
    loadMedicamentos();
});