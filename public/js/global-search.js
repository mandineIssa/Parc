(function () {
    const modal = document.getElementById('global-search-modal');
    const input = document.getElementById('global-search-input');
    const results = document.getElementById('global-search-results');
    const openBtn = document.getElementById('global-search-open');
    if (!modal || !input || !results) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const searchUrl = document.querySelector('meta[name="global-search-url"]')?.content;

    function open() {
        modal.classList.remove('hidden');
        input.value = '';
        results.innerHTML = '';
        input.focus();
    }

    function close() {
        modal.classList.add('hidden');
    }

    openBtn?.addEventListener('click', open);
    modal.querySelector('[data-close]')?.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            open();
        }
        if (e.key === 'Escape') close();
    });

    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
            results.innerHTML = '<p class="text-sm text-gray-500 p-4">Saisissez au moins 2 caractères…</p>';
            return;
        }
        timer = setTimeout(async () => {
            const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            if (!data.results?.length) {
                results.innerHTML = '<p class="text-sm text-gray-500 p-4">Aucun résultat.</p>';
                return;
            }
            results.innerHTML = data.results.map((r) =>
                `<a href="${r.url}" class="block px-4 py-3 hover:bg-gray-50 border-b last:border-0">
                    <span class="text-xs font-semibold text-[#C8102E]">${r.type}</span>
                    <span class="block text-sm text-gray-800">${r.label}</span>
                </a>`
            ).join('');
        }, 250);
    });
})();
