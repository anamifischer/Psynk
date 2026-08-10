const mesAtual = document.getElementById("mes-atual");
const dias = document.getElementById("dias");

let dataAtual = new Date();

const meses = [
    "Janeiro", "Fevereiro", "Março", "Abril",
    "Maio", "Junho", "Julho", "Agosto",
    "Setembro", "Outubro", "Novembro", "Dezembro"
];

// Dados mockados
const eventos = [
    { data: "2026-08-10", hora: "08:00", paciente: "Ana Lima", psicologo: "Dr. Carlos Souza", status: "confirmado" },
    { data: "2026-08-10", hora: "09:30", paciente: "Bruno Martins", psicologo: "Dra. Fernanda Rocha", status: "pendente" },
    { data: "2026-08-11", hora: "11:00", paciente: "Carla Dias", psicologo: "Dr. Carlos Souza", status: "confirmado" },
    { data: "2026-08-13", hora: "14:00", paciente: "Diego Fernandes", psicologo: "Dra. Fernanda Rocha", status: "confirmado" },
    { data: "2026-08-15", hora: "15:30", paciente: "Elisa Costa", psicologo: "Dr. Carlos Souza", status: "pendente" },
    { data: "2026-08-20", hora: "09:00", paciente: "Felipe Nunes", psicologo: "Dra. Fernanda Rocha", status: "confirmado" },
    { data: "2026-08-22", hora: "10:00", paciente: "Gabriela Melo", psicologo: "Dr. Carlos Souza", status: "cancelado" },
];

function criarCalendario() {

    dias.innerHTML = "";

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth();

    mesAtual.textContent = `${meses[mes]} ${ano}`;

    const primeiroDia = new Date(ano, mes, 1).getDay();
    const quantidadeDias = new Date(ano, mes + 1, 0).getDate();

    // Espaços vazios antes do primeiro dia
    for (let i = 0; i < primeiroDia; i++) {
        const vazio = document.createElement("div");
        vazio.classList.add("agenda-dia", "vazio");
        dias.appendChild(vazio);
    }

    // Dias do mês
    for (let dia = 1; dia <= quantidadeDias; dia++) {

        const celula = document.createElement("div");
        celula.classList.add("agenda-dia");

        const hoje = new Date();
        if (
            dia === hoje.getDate() &&
            mes === hoje.getMonth() &&
            ano === hoje.getFullYear()
        ) {
            celula.classList.add("hoje");
        }

        const numero = document.createElement("span");
        numero.classList.add("dia-numero");
        numero.textContent = dia;
        celula.appendChild(numero);

        // Filtra eventos do dia
        const dataStr = `${ano}-${String(mes + 1).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
        const eventosHoje = eventos.filter(e => e.data === dataStr);

        eventosHoje.forEach(evento => {
            const tag = document.createElement("div");
            tag.classList.add("agenda-evento");

            if (evento.status === "confirmado") tag.classList.add("evento-confirmado");
            else if (evento.status === "pendente") tag.classList.add("evento-pendente");
            else if (evento.status === "cancelado") tag.classList.add("evento-cancelado");

            tag.textContent = `${evento.hora} · ${evento.paciente}`;
            tag.addEventListener("click", (e) => {
                e.stopPropagation();
                abrirPainel(evento);
            });

            celula.appendChild(tag);
        });

        dias.appendChild(celula);
    }
}

function abrirPainel(evento) {
    const painel = document.getElementById("painel-detalhe");
    const conteudo = document.getElementById("painel-conteudo");

    conteudo.innerHTML = `
        <div class="painel-secao">
            <p class="painel-label">Paciente</p>
            <p class="painel-valor">${evento.paciente}</p>
        </div>
        <div class="painel-secao">
            <p class="painel-label">Psicólogo(a)</p>
            <p class="painel-valor">${evento.psicologo}</p>
        </div>
        <div class="painel-secao">
            <p class="painel-label">Horário</p>
            <p class="painel-valor">${evento.hora}</p>
        </div>
        <div class="painel-secao">
            <p class="painel-label">Status</p>
            <p class="painel-valor">${evento.status}</p>
        </div>
        <div class="painel-secao">
            <p class="painel-label">Histórico</p>
            <p class="painel-valor painel-vazio">Nenhuma consulta anterior registrada.</p>
        </div>
    `;

    painel.classList.add("aberto");
}

document.getElementById("fechar-painel")
    .addEventListener("click", () => {
        document.getElementById("painel-detalhe").classList.remove("aberto");
    });

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