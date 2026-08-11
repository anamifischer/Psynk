//Dados mockados
const dadosProfissionais = {
    'todos': {
        receita: 12150.76,
        pendente: 630,
        atrasados: 4,
        vencendoHoje: 3
    },
    'Dr. Carlos Souza': {
        receita: 2800,
        pendente: 180,
        atrasados: 2,
        vencendoHoje: 1
    },
    'Dra. Fernanda Rocha': {
        receita: 3500,
        pendente: 250,
        atrasados: 1,
        vencendoHoje: 1
    },
    'Dr. Pedro Alves': {
        receita: 4700,
        pendente: 200,
        atrasados: 1,
        vencendoHoje: 1
    },
};

//Atualizar Cards
function atualizarCards(profissional) {
    const dados = dadosProfissionais[profissional];
    if (!dados) return;

    const cardReceita = document.getElementById('valor-receita');
    const cardPendente = document.getElementById('valor-pendente');
    const cardAtrasados = document.getElementById('valor-atrasados');
    const cardVencendo = document.getElementById('valor-vencendo');

    if (cardReceita) cardReceita.textContent = dados.receita.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    if (cardPendente) cardPendente.textContent = dados.pendente.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    if (cardAtrasados) cardAtrasados.textContent = dados.atrasados;
    if (cardVencendo) cardVencendo.textContent = dados.vencendoHoje;
}

//Filtro Tabelas
function aplicarFiltroProfissional(profissional) {
    document.querySelectorAll('.data-table tbody tr').forEach(tr => {
        const psicologo = tr.cells[1]?.textContent.trim();
        tr.style.display = profissional === 'todos' || psicologo === profissional ? '' : 'none';
    });
}

//Filtro Profissional
function iniciarFiltrosProfissional() {
    const grupo = document.getElementById('filtro-profissional');
    if (!grupo) return;

    grupo.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', (e) => {
            e.stopPropagation();
            grupo.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            atualizarCards(chip.dataset.valor);
            aplicarFiltroProfissional(chip.dataset.valor);
        });
    });

    const chipAtivo = grupo.querySelector('.chip.active');
    if (chipAtivo) {
        atualizarCards(chipAtivo.dataset.valor);
        aplicarFiltroProfissional(chipAtivo.dataset.valor);
    }
}

//Init
document.addEventListener('DOMContentLoaded', () => {
    iniciarFiltrosProfissional();
});
