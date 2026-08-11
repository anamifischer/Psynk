//Filtros Pacientes
let filtroAtivo = { psicologo: 'todos', status: 'todos' };

function aplicarFiltro() {
    document.querySelectorAll('.data-table tbody tr').forEach(tr => {
        const psicologo = tr.cells[1]?.textContent.trim();
        const status = tr.cells[2]?.textContent.trim();
        const passaPsicologo = filtroAtivo.psicologo === 'todos' || psicologo === filtroAtivo.psicologo;
        const passaStatus = filtroAtivo.status === 'todos' || status === filtroAtivo.status;
        tr.style.display = passaPsicologo && passaStatus ? '' : 'none';
    });
}

function iniciarFiltrosPacientes() {
    ['filtro-psicologo', 'filtro-status'].forEach(grupoId => {
        const grupo = document.getElementById(grupoId);
        if (!grupo) return;
        grupo.querySelectorAll('.chip').forEach(chip => {
            chip.addEventListener('click', () => {
                grupo.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                const chave = grupoId === 'filtro-psicologo' ? 'psicologo' : 'status';
                filtroAtivo[chave] = chip.dataset.valor;
                aplicarFiltro();
            });
        });
    });
}