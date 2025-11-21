document.addEventListener("DOMContentLoaded", () => {
    
    const formEntrada = document.getElementById('formEntrada');
    const selectMedicamento = document.getElementById('medicamento_id');
    const selectFornecedor = document.getElementById('fornecedor_id');
    const selectUnidade = document.getElementById('unidade');
    const campoUnidadesPorEmbalagem = document.getElementById('campoUnidadesPorEmbalagem');
    const inputUnidadesPorEmbalagem = document.getElementById('unidades_por_embalagem');
    
    let unidadeBaseMedicamento = '';

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

    async function carregarDropdowns() {
        const [medicamentos, fornecedores] = await Promise.all([
            fetchData('/pharmacore/api/medicamentos/read.php'),
            fetchData('/pharmacore/api/fornecedores/read.php')
        ]);

        if (medicamentos) {
            selectMedicamento.innerHTML = '<option value="" data-unidade-base="">Selecione o medicamento</option>';
            medicamentos.forEach(item => {
                selectMedicamento.innerHTML += `<option value="${item.id}" data-unidade-base="${item.unidade_base}">${item.nome} (Base: ${item.unidade_base})</option>`;
            });
        }
        
        if (fornecedores) {
            selectFornecedor.innerHTML = '<option value="">Selecione o fornecedor</option>';
            fornecedores.forEach(item => {
                selectFornecedor.innerHTML += `<option value="${item.id}">${item.nome}</option>`;
            });
        }
    }

    function checarConversaoUnidade() {
        const unidadeSelecionada = selectUnidade.value;
        
        if (unidadeSelecionada && unidadeBaseMedicamento && unidadeSelecionada !== unidadeBaseMedicamento) {
            campoUnidadesPorEmbalagem.style.display = 'block';
            inputUnidadesPorEmbalagem.required = true;
            inputUnidadesPorEmbalagem.title = `Quantas unidades base (${unidadeBaseMedicamento}) vêm na unidade recebida (${unidadeSelecionada})?`;
        } else {
            campoUnidadesPorEmbalagem.style.display = 'none';
            inputUnidadesPorEmbalagem.required = false;
            inputUnidadesPorEmbalagem.value = '';
        }
    }

    selectMedicamento.addEventListener('change', () => {
        const selectedOption = selectMedicamento.options[selectMedicamento.selectedIndex];
        unidadeBaseMedicamento = selectedOption.dataset.unidadeBase || '';
        checarConversaoUnidade();
    });

    selectUnidade.addEventListener('change', checarConversaoUnidade);

    formEntrada.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            medicamento_id: document.getElementById('medicamento_id').value,
            data_fabricacao: document.getElementById('data_fabricacao').value,
            validade: document.getElementById('validade').value,
            fornecedor_id: document.getElementById('fornecedor_id').value,
            numero_lote_fornecedor: document.getElementById('numero_lote_fornecedor').value,
            quantidade_informada: document.getElementById('quantidade_informada').value,
            unidade: document.getElementById('unidade').value,
            unidades_por_embalagem: document.getElementById('unidades_por_embalagem').value,
            estado: document.getElementById('estado').value,
            observacao: document.getElementById('observacao').value
        };

        const result = await fetchData('/pharmacore/api/estoque/create_entrada.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result) {
            alert(result.message);
            formEntrada.reset();
            window.location.href = 'estoque.html';
        }
    });

    carregarDropdowns();
});