document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.barraLateral');
    const btn = document.getElementById('btn-retrair');

    btn.addEventListener('click', () => {
        sidebar.classList.toggle('retraida');
        localStorage.setItem('menu-retraido', sidebar.classList.contains('retraida'));
    });

    if (localStorage.getItem('menu-retraido') === 'true') {
        sidebar.classList.add('retraida');
    }
});