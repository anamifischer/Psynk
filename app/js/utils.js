const mesAtual = document.getElementById("mes-atual");
const dias = document.getElementById("dias");

let dataAtual = new Date();

const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro"
];

function criarCalendario() {

    dias.innerHTML = "";

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth();

    mesAtual.textContent = `${meses[mes]} ${ano}`;

    // Primeiro dia do mês
    const primeiroDia = new Date(ano, mes, 1).getDay();

    // Quantidade de dias do mês
    const quantidadeDias = new Date(ano, mes + 1, 0).getDate();

    // Espaços antes do primeiro dia
    for (let i = 0; i < primeiroDia; i++) {

        const vazio = document.createElement("span");

        dias.appendChild(vazio);
    }

    // Dias do mês
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


document.getElementById("mes-anterior")
    .addEventListener("click", function () {

        dataAtual.setMonth(dataAtual.getMonth() - 1);

        criarCalendario();

    });


document.getElementById("mes-proximo")
    .addEventListener("click", function () {

        dataAtual.setMonth(dataAtual.getMonth() + 1);

        criarCalendario();

    });


criarCalendario();

function toggleList(id) {
    const el = document.getElementById(id);
    el.classList.toggle('open');
}