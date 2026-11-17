document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.barraLateral');
    const btn     = document.getElementById('btn-retrair');
    const main    = document.querySelector('.main');
    const estreita = window.matchMedia('(max-width: 1000px)');

    function recolher(valor) {
        sidebar.classList.toggle('retraida', valor);
        if (main) main.classList.toggle('retraido', valor);
    }

    btn.addEventListener('click', () => {
        recolher(!sidebar.classList.contains('retraida'));
        // a escolha só é lembrada em janela larga
        if (!estreita.matches) {
            localStorage.setItem('menu-retraido', sidebar.classList.contains('retraida'));
        }
    });

    // estado ao carregar: janela estreita recolhe; senão usa o que ficou salvo
    function aplicarEstado() {
        recolher(estreita.matches || localStorage.getItem('menu-retraido') === 'true');
    }

    aplicarEstado();
    estreita.addEventListener('change', aplicarEstado);
});
