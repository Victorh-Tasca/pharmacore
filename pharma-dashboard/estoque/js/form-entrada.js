// Espera o DOM carregar
document.addEventListener("DOMContentLoaded", function() {

    // Seleciona os elementos do formulário
    const selectMedicamento = document.getElementById('medicamento_id');
    const selectUnidade = document.getElementById('unidade');
    const campoCondicional = document.getElementById('campoUnidadesPorEmbalagem');
    const inputCondicional = document.getElementById('unidades_por_embalagem');

    // Variável para guardar a unidade base do medicamento selecionado
    let unidadeBaseDoMedicamento = "";

    // Adiciona 'listener' ao <select> de Medicamento
    if (selectMedicamento) {
        selectMedicamento.addEventListener('change', function() {
            // Pega a unidade base do atributo 'data-unidade-base' da <option> selecionada
            const selectedOption = selectMedicamento.options[selectMedicamento.selectedIndex];
            unidadeBaseDoMedicamento = selectedOption.getAttribute('data-unidade-base');
            
            // Re-valida a lógica da unidade
            validarUnidade();
        });
    }

    // Adiciona 'listener' ao <select> de Unidade
    if (selectUnidade) {
        selectUnidade.addEventListener('change', function() {
            // Valida a lógica da unidade
            validarUnidade();
        });
    }

    /**
     * Função que implementa a regra de negócio do dicionário de dados:
     * "exige unidades_por_embalagem quando unidade difere [da unidade_base]"
     * "usada na conversão quando unidade != unidade_base" 
     */
    function validarUnidade() {
        const unidadeInformada = selectUnidade.value;

        // Verifica se um medicamento foi selecionado E se uma unidade foi selecionada
        if (unidadeBaseDoMedicamento && unidadeInformada) {
            
            // Se a unidade informada (ex: 'caixa') for DIFERENTE da unidade base (ex: 'comprimido')
            if (unidadeInformada !== unidadeBaseDoMedicamento) {
                // Mostra o campo e o torna obrigatório
                campoCondicional.style.display = 'block';
                inputCondicional.setAttribute('required', 'required');
            } else {
                // Oculta o campo, remove o 'required' e limpa o valor
                campoCondicional.style.display = 'none';
                inputCondicional.removeAttribute('required');
                inputCondicional.value = '';
            }
        } else {
            // Se o usuário não selecionou medicamento ou unidade, oculta o campo
            campoCondicional.style.display = 'none';
            inputCondicional.removeAttribute('required');
            inputCondicional.value = '';
        }
    }
});