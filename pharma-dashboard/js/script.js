document.addEventListener("DOMContentLoaded", function() {

    function loadUserData() {
        const userName = localStorage.getItem('pharma_user_name');
        if (userName) {
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                userDropdown.textContent = `Olá, ${userName}`;
            }
        }
    }

    function setupLogout() {
        const logoutLinks = document.querySelectorAll('a.dropdown-item');
        logoutLinks.forEach(link => {
            if (link.textContent.trim() === 'Sair') {
                link.href = '/pharmacore/api/auth/logout.php';
            }
        });
    }

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const wrapper = document.getElementById('wrapper'); 

    if (sidebarToggle && (sidebar || wrapper)) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar) sidebar.classList.toggle('active');
            if (wrapper) wrapper.classList.toggle('sidebar-toggled'); 
        });
    }

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
    setupLogout();
    loadUserData();
});