document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.barraLateral');
    const btn     = document.getElementById('btn-retrair');

    btn.addEventListener('click', () => {
        sidebar.classList.toggle('retraida');

        const main = document.querySelector('.main');
        if (main) main.classList.toggle('expandida');

        localStorage.setItem('menu-retraido', sidebar.classList.contains('retraida'));
    });

    // Restaura estado quando carrega página - local storage
    if (localStorage.getItem('menu-retraido') === 'true') {
        sidebar.classList.add('retraida');
        const main = document.querySelector('.main');
        if (main) main.classList.add('expandida');
    }
});