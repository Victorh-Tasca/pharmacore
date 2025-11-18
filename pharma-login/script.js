document.addEventListener("DOMContentLoaded", function() {

    // Slider
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');
    
    if (signUpButton) {
        signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });
    }
    
    if (signInButton) {
        signInButton.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });
    }

    // Dark Mode
    const toggle = document.getElementById('darkModeToggle');
    const body = document.body;

    function applySavedTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            toggle.checked = true;
        } else {
            body.classList.remove('dark-mode');
            toggle.checked = false;
        }
    }

    toggle.addEventListener('change', function() {
        if (toggle.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });
    
    applySavedTheme();

    // Login
    const loginForm = document.querySelector('.login-panel form');
    const registerForm = document.querySelector('.register-panel form');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault(); 

            const email = document.getElementById('floatingInput').value;
            const password = document.getElementById('floatingPassword').value;

            const data = {
                email: email,
                senha: password
            };

            fetch('../api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(err => Promise.reject(err));
                }
            })
            .then(result => {
                alert(result.message); 
                window.location.href = result.redirect; 
            })
            .catch(error => {
                console.error('Erro no login:', error);
                alert(error.message || 'Erro ao tentar conectar.');
            });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const name = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;

            const data = {
                nome: name,
                email: email,
                senha: password
            };

            fetch('../api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(err => Promise.reject(err));
                }
            })
            .then(result => {
                alert(result.message); 
                registerForm.reset();
                
                if(signInButton) {
                    signInButton.click();
                }
            })
            .catch(error => {
                console.error('Erro no cadastro:', error);
                alert(error.message || 'Erro ao tentar cadastrar.');
            });
        });
    }

});