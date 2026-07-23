(function () {
    const key = 'gpi-theme';
    const btn = document.getElementById('theme-toggle');
    function apply(dark) {
        document.documentElement.classList.toggle('dark-theme', dark);
        if (btn) btn.querySelector('i').className = dark ? 'fas fa-sun' : 'fas fa-moon';
    }
    apply(localStorage.getItem(key) === 'dark');
    btn?.addEventListener('click', () => {
        const next = !document.documentElement.classList.contains('dark-theme');
        localStorage.setItem(key, next ? 'dark' : 'light');
        apply(next);
    });
})();
