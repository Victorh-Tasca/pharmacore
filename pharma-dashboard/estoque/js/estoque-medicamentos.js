document.addEventListener("DOMContentLoaded", () => {
    const tabelaCorpo = document.querySelector("table tbody");
    const API_URL = "/pharmacore/api/estoque/read_medicamentos.php";

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

    function getAlertaBadge(item) {
        if (item.alerta_menos_que_10_unidades) {
            return `<span class="badge bg-warning text-dark">< 10 Unidades</span>`;
        }
        if (item.alerta_menos_que_20_porcento) {
            return `<span class="badge bg-warning text-dark"><= 20% do Total</span>`;
        }
        if (item.alerta_minimo) {
             return `<span class="badge bg-info">Estoque Baixo</span>`;
        }
        return `<span class="badge bg-success">OK</span>`;
    }

    async function carregarTabela() {
        const data = await fetchData(API_URL);
        if (!data) return;

        tabelaCorpo.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Código">${item.codigo}</td>
                <td data-label="Medicamento">${item.nome}</td>
                <td data-label="Entradas">${item.quantidade_entrada}</td>
                <td data-label="Saídas">${item.quantidade_saida}</td>
                <td data-label="Saldo">${item.quantidade_disponivel} ${item.unidade_base}</td>
                <td data-label="Mínimo">${item.limite_minimo}</td>
                <td data-label="Alerta">${getAlertaBadge(item)}</td>
                <td>
                    <a href="estoque.html?medicamento_id=${item.medicamento_id}" class="btn btn-sm btn-outline-primary" title="Ver Lotes">
                        <i class="bi bi-box-seam-fill"></i> Ver Lotes
                    </a>
                </td>
            `;
            tabelaCorpo.appendChild(tr);
        });
    }

    carregarTabela();
});