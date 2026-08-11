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
    document.querySelectorAll('.collapse-list').forEach(el => el.classList.remove('open'));
    if (!jaEstaAberto) alvo.classList.add('open');
}

//Dropdown genérico
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

//Init global
document.addEventListener('DOMContentLoaded', () => {
    // Pacientes
    if (document.getElementById('listaCadastrados')) {
        document.getElementById('listaCadastrados').classList.add('open');
        iniciarFiltrosPacientes();
    }

    // Financeiro
    if (document.getElementById('listaVencendo')) {
        document.getElementById('listaVencendo').classList.add('open');
    }

    if (document.getElementById('graficoReceita')) {
        iniciarFiltroAnos();
    }
});