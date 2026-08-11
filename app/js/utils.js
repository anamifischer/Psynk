//Calendário
const mesAtual = document.getElementById("mes-atual");
const dias = document.getElementById("dias");

let dataAtual = new Date();

const meses = [
    "Janeiro", "Fevereiro", "Março", "Abril",
    "Maio", "Junho", "Julho", "Agosto",
    "Setembro", "Outubro", "Novembro", "Dezembro"
];

function criarCalendario() {
    if (!dias || !mesAtual) return;

    dias.innerHTML = "";

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth();

    mesAtual.textContent = `${meses[mes]} ${ano}`;

    const primeiroDia    = new Date(ano, mes, 1).getDay();
    const quantidadeDias = new Date(ano, mes + 1, 0).getDate();

    for (let i = 0; i < primeiroDia; i++) {
        dias.appendChild(document.createElement("span"));
    }

    for (let dia = 1; dia <= quantidadeDias; dia++) {
        const elementoDia = document.createElement("button");
        elementoDia.textContent = dia;
        elementoDia.classList.add("dia");
        elementoDia.addEventListener("click", function () {
            selecionarDia(dia, mes, ano);
        });
        dias.appendChild(elementoDia);
    }
}

function selecionarDia(dia, mes, ano) {
    console.log(`Dia selecionado: ${dia}/${mes + 1}/${ano}`);
}

const btnAnterior = document.getElementById("mes-anterior");
const btnProximo  = document.getElementById("mes-proximo");

if (btnAnterior) {
    btnAnterior.addEventListener("click", function () {
        dataAtual.setMonth(dataAtual.getMonth() - 1);
        criarCalendario();
    });
}

if (btnProximo) {
    btnProximo.addEventListener("click", function () {
        dataAtual.setMonth(dataAtual.getMonth() + 1);
        criarCalendario();
    });
}

criarCalendario();

//Toggle Lists
function toggleList(id) {
    const alvo = document.getElementById(id);
    if (!alvo) return;

    const jaEstaAberto = alvo.classList.contains('open');

    document.querySelectorAll('.collapse-list').forEach(el => {
        el.classList.remove('open');
    });

    if (!jaEstaAberto) {
        alvo.classList.add('open');
    }
}

//Filtros
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

function iniciarFiltros() {
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

function toggleFiltroDropdown() {
    const dropdown = document.getElementById('filtro-dropdown');
    if (!dropdown) return;
    dropdown.classList.toggle('open');
}

document.addEventListener('click', (e) => {
    const wrapper = document.querySelector('.filtro-dropdown-wrapper');
    if (!wrapper) return;
    if (!wrapper.contains(e.target)) {
        document.getElementById('filtro-dropdown')?.classList.remove('open');
    }
});

//Init
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('filtro-psicologo')) {
        iniciarFiltros();
    }

    //Abrir listaCadastrados por padrão
    const listaCadastrados = document.getElementById('listaCadastrados');
    if (listaCadastrados) {
        listaCadastrados.classList.add('open');
    }
});