
document.addEventListener("DOMContentLoaded", function() {

    // --- LÓGICA DO TOGGLE DA SIDEBAR ---
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // --- LÓGICA DO MODO NOTURNO ---
    const toggle = document.getElementById('darkModeToggle');
    
    
    const rootElement = document.documentElement; 

    
    function applySavedTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            rootElement.classList.add('dark-mode');
            if (toggle) toggle.checked = true;
        } else {
            rootElement.classList.remove('dark-mode');
            if (toggle) toggle.checked = false;
        }
    }

    
    if (toggle) {
        toggle.addEventListener('change', function() {
            if (toggle.checked) {
                rootElement.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
            } else {
                rootElement.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
            }
        });
    }

    
    applySavedTheme();

});