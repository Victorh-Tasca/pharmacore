
document.addEventListener("DOMContentLoaded", function() {

    // --- LÓGICA DO SLIDE ---
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


    // --- LÓGICA DO MODO NOTURNO ---
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

});