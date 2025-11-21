document.addEventListener("DOMContentLoaded", () => {
    const statCards = {
        total_produtos: document.querySelector('.border-primary .display-4'),
        prox_vencimento: document.querySelector('.border-warning .display-4'),
        vencidos: document.querySelector('.border-danger .display-4'),
        estoque_baixo: document.querySelector('.border-info .display-4')
    };

    const tabelaCorpo = document.querySelector(".card:last-child table tbody");

    const API_URL_DASHBOARD = "/pharmacore/api/dashboard/";

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
            return null;
        }
    }

    function getBadgeClass(tipo) {
        if (tipo === 'Entrada') return 'bg-success';
        if (tipo === 'Saída') return 'bg-danger';
        return 'bg-info';
    }

    function formatarDataHora(dataHoraISO) {
        if (!dataHoraISO) return '';
        const data = new Date(dataHoraISO);
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const ano = data.getFullYear();
        const hora = String(data.getHours()).padStart(2, '0');
        const min = String(data.getMinutes()).padStart(2, '0');
        return `${dia}/${mes}/${ano} ${hora}:${min}`;
    }

    async function carregarEstatisticas() {
        const stats = await fetchData(API_URL_DASHBOARD + 'read_stats.php', { method: 'GET' });
        if (!stats) return;

        statCards.total_produtos.textContent = stats.total_produtos.toLocaleString('pt-BR');
        statCards.prox_vencimento.textContent = stats.prox_vencimento.toLocaleString('pt-BR');
        statCards.vencidos.textContent = stats.vencidos.toLocaleString('pt-BR');
        statCards.estoque_baixo.textContent = stats.estoque_baixo.toLocaleString('pt-BR');
    }

    async function carregarAtividade() {
        const activity = await fetchData(API_URL_DASHBOARD + 'read_activity.php', { method: 'GET' });
        if (!activity) return;

        tabelaCorpo.innerHTML = '';
        activity.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="badge ${getBadgeClass(item.tipo)}">${item.tipo}</span></td>
                <td>${item.produto}</td>
                <td>${formatarDataHora(item.data)}</td>
                <td>${item.responsavel}</td>
            `;
            tabelaCorpo.appendChild(tr);
        });
    }

    carregarEstatisticas();
    carregarAtividade();
});