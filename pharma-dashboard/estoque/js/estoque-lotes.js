document.addEventListener("DOMContentLoaded", () => {
    const tabelaCorpo = document.querySelector("table tbody");
    const API_URL = "/api/estoque/";

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
        if (status === 'OK') {
            return `<span class="badge bg-success">OK</span>`;
        }
        if (status === 'Próximo de vencer') {
            return `<span class="badge bg-warning text-dark">Próximo de Vencer</span>`;
        }
        if (status === 'Bloquear dispensação') {
            return `<span class="badge bg-danger">Vencido</span>`;
        }
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
        const data = await fetchData(API_URL + 'read_lotes.php');
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            
            let podeArquivar = item.quantidade_disponivel <= 0 || item.status === 'Bloquear dispensação';

            tr.innerHTML = `
                <td data-label="Código">${item.codigo}</td>
                <td data-label="Produto">${item.medicamento}</td>
                <td data-label="Tarja">${getTarjaBadge(item.tarja)}</td>
                <td data-label="Qtd. Disponível">${item.quantidade_disponivel}</td>
                <td data-label="Validade">${formatarData(item.validade)}</td>
                <td data-label="Status">${getStatusBadge(item.status)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" disabled title="Em breve">
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

    tabelaCorpo.addEventListener('click', async (e) => {
        const btnArquivar = e.target.closest('.btn-arquivar');
        if (btnArquivar) {
            const id = btnArquivar.dataset.id;
            
            if (confirm('Deseja arquivar este lote? Ele só será arquivado se estiver vencido ou com saldo zero.')) {
                const result = await fetchData(API_URL + 'archive_lote.php', {
                    method: 'POST',
                    body: JSON.stringify({ lote_id: id })
                });

                if (result) {
                    await carregarTabela();
                }
            }
        }
    });

    carregarTabela();
});