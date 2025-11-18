document.addEventListener("DOMContentLoaded", () => {
    
    const API_URL = "/api/usuarios/";
    
    const formMeusDados = document.getElementById('formMeusDados');
    const formAlterarSenha = document.getElementById('formAlterarSenha');

    async function fetchData(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: { 'Content-Type': 'application/json', ...options.headers },
            });
            
            if (response.status === 401 && url.endsWith('me.php')) {
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

    async function carregarMeusDados() {
        const data = await fetchData(API_URL + 'me.php', { method: 'GET' });
        if (!data) return;

        document.getElementById('nome').value = data.nome;
        document.getElementById('celular').value = data.celular || '';
        document.getElementById('login').value = data.login;
        document.getElementById('email').value = data.email;
    }

    formMeusDados.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            nome: document.getElementById('nome').value,
            celular: document.getElementById('celular').value
        };

        const result = await fetchData(API_URL + 'update_me.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result) {
            alert(result.message);
            localStorage.setItem('pharma_user_name', result.user_name);
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                userDropdown.textContent = `Olá, ${result.user_name}`;
            }
        }
    });

    formAlterarSenha.addEventListener('submit', async (e) => {
        e.preventDefault();

        const senhaAtual = document.getElementById('senha_atual').value;
        const novaSenha = document.getElementById('nova_senha').value;
        const novaSenhaConfirm = document.getElementById('nova_senha_confirm').value;

        if (novaSenha !== novaSenhaConfirm) {
            alert('A nova senha e a confirmação não conferem.');
            return;
        }

        const data = {
            senha_atual: senhaAtual,
            nova_senha: novaSenha
        };

        const result = await fetchData(API_URL + 'change_password.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result) {
            alert(result.message);
            formAlterarSenha.reset();
        }
    });

    carregarMeusDados();
});