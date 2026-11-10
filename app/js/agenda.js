const nomesMeses = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho",
                    "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
const nomesDias  = ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"];

const coresPsicologos = ["#24474D", "#91a6a4", "#d97c56", "#7a6c9d", "#c2a24b"];
let psicologos = [];
let eventos = [];
let dataCalendarioAgenda = new Date();
let viewAtual= "mes";
let psicologosFiltro = new Set();

function formatarData(ano, mes, dia) {
    return `${ano}-${String(mes + 1).padStart(2,"0")}-${String(dia).padStart(2,"0")}`;
}

function eventosDoDia(ano, mes, dia) {
    const str = formatarData(ano, mes, dia);
    return eventos.filter(e => e.data === str && psicologosFiltro.has(e.psicologoId));
}

function ehHoje(ano, mes, dia) {
    const h = new Date();
    return dia === h.getDate() && mes === h.getMonth() && ano === h.getFullYear();
}

function psicologoPorId(id) {
    return psicologos.find(p => p.id === id);
}

function renderizarFiltros() {
    const lista = document.getElementById("lista-psicologos");
    lista.innerHTML = "";

    psicologos.forEach(p => {
        const item = document.createElement("div");
        item.className = "item-psicologo" + (psicologosFiltro.has(p.id) ? " selecionado" : "");
        item.innerHTML = `
            <div class="avatar-psicologo" style="background:${p.cor}">${p.iniciais}</div>
            <span class="nome-psicologo">${p.nome}</span>
        `;
        item.addEventListener("click", () => {
            if (psicologosFiltro.has(p.id)) {
                if (psicologosFiltro.size > 1) psicologosFiltro.delete(p.id);
            } else {
                psicologosFiltro.add(p.id);
            }
            renderizarFiltros();
            renderizar();
        });
        lista.appendChild(item);
    });
}

function renderizar() {
    const grade         = document.getElementById("grade-calendario");
    const cabecalho     = document.getElementById("cabecalho-semana");
    const periodoEl     = document.getElementById("periodo-atual");

    grade.innerHTML = "";

    if (viewAtual === "mes") {
        cabecalho.style.display = "grid";
        renderizarMes(grade, periodoEl);
    } else if (viewAtual === "semana") {
        cabecalho.style.display = "none";
        renderizarSemana(grade, periodoEl);
    } else {
        cabecalho.style.display = "none";
        renderizarAno(grade, periodoEl);
    }
}


function renderizarMes(grade, periodoEl) {
    const ano = dataCalendarioAgenda.getFullYear();
    const mes = dataCalendarioAgenda.getMonth();
    periodoEl.textContent = `${nomesMeses[mes]} ${ano}`;

    const gradeEl       = document.createElement("div");
    gradeEl.className   = "grade-mes";

    const primeiroDia   = new Date(ano, mes, 1).getDay();
    const totalDias     = new Date(ano, mes + 1, 0).getDate();
    const diasAnteriores = new Date(ano, mes, 0).getDate();

    for (let i = primeiroDia - 1; i >= 0; i--) {
        gradeEl.appendChild(criarCelulaMes(diasAnteriores - i, ano, mes - 1, true));
    }
    for (let d = 1; d <= totalDias; d++) {
        gradeEl.appendChild(criarCelulaMes(d, ano, mes, false));
    }
    const resto = (primeiroDia + totalDias) % 7;
    if (resto > 0) {
        for (let d = 1; d <= 7 - resto; d++) {
            gradeEl.appendChild(criarCelulaMes(d, ano, mes + 1, true));
        }
    }

    grade.appendChild(gradeEl);
}

function criarCelulaMes(dia, ano, mes, outroMes) {
    const cel       = document.createElement("div");
    const classes   = ["celula-dia"];
    if (outroMes) classes.push("outro-mes");
    if (!outroMes && ehHoje(ano, mes, dia)) classes.push("hoje");
    cel.className = classes.join(" ");

    const num = document.createElement("span");
    num.className   = "numero-dia";
    num.textContent = dia;
    cel.appendChild(num);

    if (!outroMes) {
        const evs = eventosDoDia(ano, mes, dia);
        evs.slice(0, 2).forEach(ev => {
            const psi     = psicologoPorId(ev.psicologoId);
            const tag     = document.createElement("div");
            tag.className = `evento-dia ${ev.status}`;

            const linhaPaciente = document.createElement("div");
            linhaPaciente.textContent = `${ev.hora} · ${ev.paciente}`;

            const linhaPsicologo = document.createElement("div");
            linhaPsicologo.className = "evento-psicologo";
            linhaPsicologo.textContent = psi ? psi.nome : "";

            tag.append(linhaPaciente, linhaPsicologo);
            tag.addEventListener("click", e => { e.stopPropagation(); abrirDrawer(ev.pacienteId); });
            cel.appendChild(tag);
        });
        if (evs.length > 2) {
            const mais       = document.createElement("div");
            mais.className   = "mais-eventos";
            mais.textContent = `+${evs.length - 2} mais`;
            cel.appendChild(mais);
        }
    }
    return cel;
}


function renderizarSemana(grade, periodoEl) {
    const inicio = new Date(dataCalendarioAgenda);
    inicio.setDate(dataCalendarioAgenda.getDate() - dataCalendarioAgenda.getDay());
    const fim = new Date(inicio);
    fim.setDate(inicio.getDate() + 6);

    const fmt = d => `${d.getDate()} ${nomesMeses[d.getMonth()].slice(0,3)}`;
    periodoEl.textContent = `${fmt(inicio)} – ${fmt(fim)} ${fim.getFullYear()}`;

    const gradeEl     = document.createElement("div");
    gradeEl.className = "grade-semana";

    for (let i = 0; i < 7; i++) {
        const dia = new Date(inicio);
        dia.setDate(inicio.getDate() + i);

        const col     = document.createElement("div");
        col.className = "coluna-semana";

        const hoje    = ehHoje(dia.getFullYear(), dia.getMonth(), dia.getDate());
        const cab     = document.createElement("div");
        cab.className = "coluna-semana-cabecalho" + (hoje ? " hoje" : "");
        cab.innerHTML = `
            <div class="nome-dia-semana">${nomesDias[i]}</div>
            <div class="numero-dia-semana">${dia.getDate()}</div>
        `;
        col.appendChild(cab);

        const corpo     = document.createElement("div");
        corpo.className = "coluna-semana-corpo";

        const evs = eventosDoDia(dia.getFullYear(), dia.getMonth(), dia.getDate());
        evs.forEach(ev => {
            const psi       = psicologoPorId(ev.psicologoId);
            const tag       = document.createElement("div");
            tag.className   = `evento-semana ${ev.status}`;
            const linhaHora = document.createElement("div");
            linhaHora.className = "evento-hora";
            linhaHora.textContent = ev.hora;

            const linhaNome = document.createElement("div");
            linhaNome.className = "evento-nome";
            linhaNome.textContent = ev.paciente;

            const linhaPsicologo = document.createElement("div");
            linhaPsicologo.className = "evento-psicologo";
            linhaPsicologo.textContent = psi ? psi.nome : "";

            tag.append(linhaHora, linhaNome, linhaPsicologo);
            if (ev.anotacao) {
                const linhaAnotacao = document.createElement("div");
                linhaAnotacao.className = "evento-anotacao";
                linhaAnotacao.textContent = ev.anotacao;
                tag.appendChild(linhaAnotacao);
            }
            tag.addEventListener("click", e => { e.stopPropagation(); abrirDrawer(ev.pacienteId); });
            corpo.appendChild(tag);
        });

        col.appendChild(corpo);
        gradeEl.appendChild(col);
    }

    grade.appendChild(gradeEl);
}

function renderizarAno(grade, periodoEl) {
    const ano = dataCalendarioAgenda.getFullYear();
    periodoEl.textContent = ano;

    const gradeEl     = document.createElement("div");
    gradeEl.className = "grade-ano";

    for (let m = 0; m < 12; m++) {
        const miniMes     = document.createElement("div");
        miniMes.className = "mini-mes";

        const titulo       = document.createElement("div");
        titulo.className   = "mini-mes-titulo";
        titulo.textContent = nomesMeses[m];
        miniMes.appendChild(titulo);

        const cabSemana     = document.createElement("div");
        cabSemana.className = "mini-cabecalho-semana";
        ["D","S","T","Q","Q","S","S"].forEach(d => {
            const s = document.createElement("span");
            s.textContent = d;
            cabSemana.appendChild(s);
        });
        miniMes.appendChild(cabSemana);

        const diasGrid     = document.createElement("div");
        diasGrid.className = "mini-grade-dias";

        const primeiroDia = new Date(ano, m, 1).getDay();
        const totalDias   = new Date(ano, m + 1, 0).getDate();

        for (let i = 0; i < primeiroDia; i++) {
            diasGrid.appendChild(document.createElement("div"));
        }

        for (let d = 1; d <= totalDias; d++) {
            const cel       = document.createElement("div");
            cel.className   = "mini-celula-dia";
            if (ehHoje(ano, m, d)) cel.classList.add("hoje");
            if (eventosDoDia(ano, m, d).length > 0) cel.classList.add("tem-evento");
            cel.textContent = d;
            cel.addEventListener("click", () => {
                dataCalendarioAgenda = new Date(ano, m, d);
                viewAtual = "semana";
                atualizarBotoes();
                renderizar();
            });
            diasGrid.appendChild(cel);
        }

        miniMes.appendChild(diasGrid);
        gradeEl.appendChild(miniMes);
    }

    grade.appendChild(gradeEl);
}


document.getElementById("btn-anterior").addEventListener("click", () => {
    if (viewAtual === "mes")    dataCalendarioAgenda.setMonth(dataCalendarioAgenda.getMonth() - 1);
    else if (viewAtual === "semana") dataCalendarioAgenda.setDate(dataCalendarioAgenda.getDate() - 7);
    else dataCalendarioAgenda.setFullYear(dataCalendarioAgenda.getFullYear() - 1);
    renderizar();
});

document.getElementById("btn-proximo").addEventListener("click", () => {
    if (viewAtual === "mes")    dataCalendarioAgenda.setMonth(dataCalendarioAgenda.getMonth() + 1);
    else if (viewAtual === "semana") dataCalendarioAgenda.setDate(dataCalendarioAgenda.getDate() + 7);
    else dataCalendarioAgenda.setFullYear(dataCalendarioAgenda.getFullYear() + 1);
    renderizar();
});

function atualizarBotoes() {
    document.querySelectorAll(".botao-view").forEach(btn => {
        btn.classList.toggle("ativo", btn.dataset.view === viewAtual);
    });
}

document.querySelectorAll(".botao-view").forEach(btn => {
    btn.addEventListener("click", () => {
        viewAtual = btn.dataset.view;
        atualizarBotoes();
        renderizar();
    });
});



document.addEventListener("DOMContentLoaded", async function() {
    const resp = await fetch("/app/api/agenda_psicologos.php");
    const lista = await resp.json();

    psicologos = lista.map((p, i) => ({
        id: p.id,
        nome: p.nome,
        iniciais: p.nome.split(" ").filter(Boolean).slice(0, 2).map(n => n[0]).join("").toUpperCase(),
        cor: coresPsicologos[i % coresPsicologos.length]
    }));
    psicologosFiltro = new Set(psicologos.map(p => p.id));
    const respEventos = await fetch("/app/api/agenda_consultas.php");
    eventos = await respEventos.json();

    renderizarFiltros();
    renderizar();
});