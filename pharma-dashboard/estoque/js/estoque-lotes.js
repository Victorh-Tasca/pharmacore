document.addEventListener("DOMContentLoaded", () => {
    const tabelaCorpo = document.querySelector("table tbody");
    const API_URL_ESTOQUE = "/pharmacore/api/estoque/";
    const API_URL_ENTRADAS = "/pharmacore/api/entradas/";

    const modalHistorico = new bootstrap.Modal(document.getElementById('modalHistoricoEntradas'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarEntrada'));
    
    const formEditarEntrada = document.getElementById('formEditarEntrada');
    const tabelaHistoricoEntradas = document.getElementById('tabelaHistoricoEntradas');
    
    let loteInfoCache = {};

    async function fetchData(url, options = {}) {
        try {
            const response = await fetch(url, { ...options });
            
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

    function getStatusBadge(status) {
        if (status === 'OK') return `<span class="badge bg-success">OK</span>`;
        if (status === 'Próximo de vencer') return `<span class="badge bg-warning text-dark">Próximo de Vencer</span>`;
        if (status === 'Bloquear dispensação') return `<span class="badge bg-danger">Vencido</span>`;
        return `<span class="badge bg-secondary">${status}</span>`;
    }

    function getTarjaBadge(tarja) {
        if (!tarja) return '';
        if (tarja === 'sem_tarja') return '<span class="badge bg-secondary">Sem Tarja</span>';
        if (tarja === 'tarja_amarela') return '<span class="badge bg-warning text-dark">Tarja Amarela</span>';
        if (tarja === 'tarja_vermelha') return '<span class="badge bg-danger">Tarja Vermelha</span>';
        if (tarja === 'tarja_preta') return '<span class="badge bg-dark">Tarja Preta</span>';
        return tarja;
    }
    
    function formatarData(dataISO) {
        if (!dataISO) return '';
        const [ano, mes, dia] = dataISO.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    async function carregarTabela() {
        const data = await fetchData(API_URL_ESTOQUE + 'read_lotes.php');
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            loteInfoCache[item.lote_id] = { nome: item.medicamento, validade: formatarData(item.validade) };
            let podeArquivar = item.quantidade_disponivel <= 0 || item.status === 'Bloquear dispensação';

            tr.innerHTML = `
                <td data-label="Código">${item.codigo}</td>
                <td data-label="Produto">${item.medicamento}</td>
                <td data-label="Tarja">${getTarjaBadge(item.tarja)}</td>
                <td data-label="Qtd. Disponível">${item.quantidade_disponivel}</td>
                <td data-label="Validade">${formatarData(item.validade)}</td>
                <td data-label="Status">${getStatusBadge(item.status)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar-entradas" 
                            data-lote-id="${item.lote_id}" 
                            title="Editar Entradas deste Lote">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-arquivar" 
                            data-id="${item.lote_id}" 
                            title="Arquivar Lote (Se vencido ou sem saldo)" 
                            ${!podeArquivar ? 'disabled' : ''}>
                        <i class="bi bi-archive-fill"></i>
                    </button>
                </td>
            `;
            tabelaCorpo.appendChild(tr);
        });
    }

    async function carregarHistoricoEntradas(loteId) {
        const loteInfo = loteInfoCache[loteId];
        document.getElementById('hist_medicamento_nome').textContent = loteInfo.nome;
        document.getElementById('hist_validade').textContent = loteInfo.validade;

        const data = await fetchData(API_URL_ENTRADAS + `read.php?lote_id=${loteId}`, { method: 'GET' });
        if (!data) return;

        tabelaHistoricoEntradas.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            item.medicamento_nome = loteInfo.nome;
            tr.dataset.item = JSON.stringify(item);
            tr.innerHTML = `
                <td>${formatarData(item.data_entrada)}</td>
                <td>${item.fornecedor}</td>
                <td>${item.numero_lote_fornecedor}</td>
                <td>${item.quantidade_informada} ${item.unidade}</td>
                <td>${item.unidades_por_embalagem || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar-entrada-modal" title="Editar esta entrada">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-excluir-entrada" data-id="${item.id}" title="Excluir esta entrada">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            `;
            tabelaHistoricoEntradas.appendChild(tr);
        });
    }

    tabelaCorpo.addEventListener('click', async (e) => {
        const btnArquivar = e.target.closest('.btn-arquivar');
        if (btnArquivar) {
            const id = btnArquivar.dataset.id;
            if (confirm('Deseja arquivar este lote? Ele só será arquivado se estiver vencido ou com saldo zero.')) {
                const result = await fetchData(API_URL_ESTOQUE + 'archive_lote.php', {
                    method: 'POST',
                    body: JSON.stringify({ lote_id: id })
                });
                if (result) await carregarTabela();
            }
        }
        
        const btnEditarEntradas = e.target.closest('.btn-editar-entradas');
        if (btnEditarEntradas) {
            const loteId = btnEditarEntradas.dataset.loteId;
            await carregarHistoricoEntradas(loteId);
            modalHistorico.show();
        }
    });

    tabelaHistoricoEntradas.addEventListener('click', async (e) => {
        const btnExcluir = e.target.closest('.btn-excluir-entrada');
        if (btnExcluir) {
            const id = btnExcluir.dataset.id;
            if (confirm('Tem certeza que deseja EXCLUIR esta entrada?\nIsso afetará o saldo total do lote.')) {
                const result = await fetchData(API_URL_ENTRADAS + 'delete.php', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                });
                if (result) {
                    const loteId = document.querySelector('.btn-editar-entradas').dataset.loteId;
                    await carregarHistoricoEntradas(loteId);
                    await carregarTabela(); 
                }
            }
        }

        const btnEditar = e.target.closest('.btn-editar-entrada-modal');
        if (btnEditar) {
            const item = JSON.parse(btnEditar.closest('tr').dataset.item);
            
            document.getElementById('edit_entrada_id').value = item.id;
            document.getElementById('edit_medicamento_nome').textContent = item.medicamento_nome;
            document.getElementById('edit_lote_fornecedor').textContent = item.numero_lote_fornecedor;
            document.getElementById('edit_quantidade_informada').value = item.quantidade_informada;
            document.getElementById('edit_unidade').value = item.unidade;

            const campoConversao = document.getElementById('edit_campoUnidadesPorEmbalagem');
            const inputConversao = document.getElementById('edit_unidades_por_embalagem');
            
            if (item.unidade !== item.unidade_base) {
                campoConversao.style.display = 'block';
                inputConversao.value = item.unidades_por_embalagem;
                inputConversao.required = true;
            } else {
                campoConversao.style.display = 'none';
                inputConversao.value = '';
                inputConversao.required = false;
            }

            modalHistorico.hide();
            modalEditar.show();
        }
    });

    formEditarEntrada.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('edit_entrada_id').value;
        const data = {
            id: id,
            quantidade_informada: document.getElementById('edit_quantidade_informada').value,
            unidades_por_embalagem: document.getElementById('edit_unidades_por_embalagem').value
        };

        const result = await fetchData(API_URL_ENTRADAS + 'update.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result) {
            modalEditar.hide();
            await carregarTabela();
        }
    });

    carregarTabela();
});