document.addEventListener("DOMContentLoaded", () => {
    const tabelaValidade = document.querySelector("#cardValidade table tbody");
    const tabelaEstoque = document.querySelector("#cardEstoqueBaixo table tbody");
    const API_URL = "/pharmacore/api/alertas/read.php";

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
        if (status === 'Próximo de vencer') {
            return `<span class="badge bg-warning text-dark">Próximo de Vencer</span>`;
        }
        if (status === 'Bloquear dispensação') {
            return `<span class="badge bg-danger">Vencido</span>`;
        }
        return `<span class="badge bg-secondary">${status}</span>`;
    }

    function formatarData(dataISO) {
        if (!dataISO) return '';
        const [ano, mes, dia] = dataISO.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    async function carregarTabelas() {
        const data = await fetchData(API_URL, { method: 'GET' });
        if (!data) return;

        tabelaValidade.innerHTML = '';
        if (data.validade.length > 0) {
            data.validade.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td data-label="Medicamento">${item.medicamento}</td>
                    <td data-label="Validade">${formatarData(item.validade)}</td>
                    <td data-label="Status">${getStatusBadge(item.status)}</td>
                `;
                tabelaValidade.appendChild(tr);
            });
        } else {
            tabelaValidade.innerHTML = '<tr><td colspan="3" class="text-center">Nenhum alerta de validade.</td></tr>';
        }

        tabelaEstoque.innerHTML = '';
        if (data.estoque_baixo.length > 0) {
            data.estoque_baixo.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td data-label="Medicamento">${item.nome}</td>
                    <td data-label="Saldo">${item.quantidade_disponivel}</td>
                    <td data-label="Mínimo">${item.limite_minimo}</td>
                `;
                tabelaEstoque.appendChild(tr);
            });
        } else {
            tabelaEstoque.innerHTML = '<tr><td colspan="3" class="text-center">Nenhum alerta de estoque baixo.</td></tr>';
        }
    }

    carregarTabelas();
});