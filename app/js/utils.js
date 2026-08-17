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

//Search
const pacientesMockados = [
    { 
        nome: 'Ana Lima', 
        status: 'Ativo', 
        psicologo: 'Dr. Carlos Souza', 
        nascimento: '12/03/1990', 
        idade: 36, 
        mostrarAnotacoes: true,
        sessoes: [
            {
                data: '15/08/2026',
                hora: '14:00',
                realizada: false,
                anotacao: null,
                futuro: true
            },
            {
                data: '05/08/2026',
                hora: '14:00',
                realizada: true,
                anotacao: null,
                futuro: false
            },
            {
                data: '29/07/2026',
                hora: '14:00',
                realizada: true,
                anotacao: 'Paciente relatou melhora significativa na qualidade do sono. Continuamos trabalhando técnicas de respiração.',
                futuro: false
            },
        ]
    },
    { nome: 'Maria Laura Silva', status: 'Em espera', psicologo: 'Dr. Carlos Souza', nascimento: '05/07/1995', idade: 31, mostrarAnotacoes: true },
    { nome: 'José Gonçalves', status: 'Encerrado', psicologo: 'Dra. Fernanda Rocha', nascimento: '22/11/1985', idade: 40, mostrarAnotacoes: false },
    { nome: 'Gabriela Moreira', status: 'Pausado', psicologo: 'Dra. Fernanda Rocha', nascimento: '18/04/1992', idade: 34, mostrarAnotacoes: true },
    { nome: 'Gabriel Mussoi', status: 'Inativo', psicologo: 'Dr. Pedro Alves', nascimento: '30/09/1998', idade: 27, mostrarAnotacoes: true },
];

function iniciarSearch() {
    const input = document.getElementById('search-paciente');
    const resultados = document.getElementById('search-resultados');
    if (!input || !resultados) return;

    input.addEventListener('input', () => {
        const termo = input.value.trim().toLowerCase();
        if (!termo) { resultados.classList.remove('open'); return; }

        const filtrados = pacientesMockados.filter(p => p.nome.toLowerCase().includes(termo));

        if (!filtrados.length) { resultados.classList.remove('open'); return; }

        resultados.innerHTML = filtrados.map(p => `
            <div class="search-resultado-item" onclick="abrirDrawer('${p.nome}')">
                <div>
                    <div class="search-resultado-nome">${p.nome}</div>
                    <div class="search-resultado-psicologo">${p.psicologo}</div>
                </div>
                <span class="badge badge-${badgeClassStatus(p.status)}">${p.status}</span>
            </div>
        `).join('');

        resultados.classList.add('open');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-wrapper')) {
            resultados.classList.remove('open');
        }
    });
}

function badgeClassStatus(status) {
    const map = {
        'Ativo': 'verde',
        'Em espera': 'azul',
        'Encerrado': 'cinza',
        'Pausado': 'amarelo',
        'Inativo': 'cinza',
    };
    return map[status] || 'cinza';
}

//Drawer
function abrirDrawer(nomePaciente) {
    const paciente = pacientesMockados.find(p => p.nome === nomePaciente);
    if (!paciente) return;

    document.getElementById('drawer-nome').textContent = paciente.nome;
    document.getElementById('drawer-nascimento').textContent = paciente.nascimento;
    document.getElementById('drawer-idade').textContent = paciente.idade + ' anos';
    document.getElementById('drawer-psicologo').textContent = paciente.psicologo;

    const statusEl = document.getElementById('drawer-status');
    statusEl.textContent = paciente.status;
    statusEl.className = `badge badge-${badgeClassStatus(paciente.status)}`;

    // aba anotações
    const abaAnotacoes = document.querySelector('.drawer-aba[data-aba="anotacoes"]');
    if (abaAnotacoes) {
        abaAnotacoes.style.display = paciente.mostrarAnotacoes ? '' : 'none';
    }

    // renderiza sessoes
    const abaAnotacoesPanel = document.getElementById('aba-anotacoes');
    if (abaAnotacoesPanel) {
        if (!paciente.sessoes || !paciente.sessoes.length) {
            abaAnotacoesPanel.innerHTML = '<p class="drawer-vazio">Nenhuma sessão registrada.</p>';
        } else {
            abaAnotacoesPanel.innerHTML = paciente.sessoes.map((s, index) => {
                const corData = s.futuro ? 'sessao-data-futura' : (!s.anotacao && s.realizada ? 'sessao-data-pendente' : '');
                const badge = s.futuro
                    ? '<span class="badge badge-azul">Agendada</span>'
                    : (!s.anotacao && s.realizada
                        ? '<span class="badge badge-vermelho">Anotação pendente</span>'
                        : '<span class="badge badge-verde">Concluída</span>');

                const podeLancar = !s.anotacao && papelUsuario !== 'recepcionista';

                const textoAnotacao = (s.anotacao && papelUsuario !== 'recepcionista')
                    ? `<p class="sessao-anotacao">${s.anotacao}</p>`
                    : '';

                return `
                    <div class="sessao-item">
                        <div class="sessao-header">
                            <span class="sessao-data ${corData}">${s.data}</span>
                            <span class="sessao-hora">${s.hora}</span>
                            ${badge}
                            ${podeLancar ? `<button class="btn btn-sm btn-secondary" onclick="abrirModal('${paciente.nome}', ${index})">Lançar</button>` : ''}
                        </div>
                        ${textoAnotacao}
                    </div>
                `;
            }).join('');
        }
    }


    document.getElementById('drawer-paciente').classList.add('open');
    document.getElementById('drawer-overlay').classList.add('open');

    document.querySelectorAll('.drawer-aba').forEach(a => a.classList.remove('active'));
    document.querySelectorAll('.drawer-painel').forEach(p => p.classList.remove('active'));
    document.querySelector('.drawer-aba[data-aba="dados"]').classList.add('active');
    document.getElementById('aba-dados').classList.add('active');

    document.getElementById('search-resultados').classList.remove('open');
    document.getElementById('search-paciente').value = '';
}

function fecharDrawer() {
    document.getElementById('drawer-paciente').classList.remove('open');
    document.getElementById('drawer-overlay').classList.remove('open');
}

function iniciarAbasDrawer() {
    document.querySelectorAll('.drawer-aba').forEach(aba => {
        aba.addEventListener('click', () => {
            document.querySelectorAll('.drawer-aba').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.drawer-painel').forEach(p => p.classList.remove('active'));
            aba.classList.add('active');
            document.getElementById('aba-' + aba.dataset.aba).classList.add('active');
        });
    });
}

//Modal Anotação
let sessaoAtiva = null;
let pacienteAtivo = null;

function abrirModal(nomePaciente, indexSessao) {
    const paciente = pacientesMockados.find(p => p.nome === nomePaciente);
    if (!paciente) return;

    const sessao = paciente.sessoes[indexSessao];
    if (!sessao) return;

    pacienteAtivo = paciente;
    sessaoAtiva = { paciente: nomePaciente, index: indexSessao };

    document.getElementById('modal-paciente').textContent = paciente.nome;
    document.getElementById('modal-psicologo').textContent = paciente.psicologo;
    document.getElementById('modal-data').textContent = `${sessao.data} às ${sessao.hora}`;
    document.getElementById('modal-subtitulo').textContent = sessao.futuro ? 'Sessão agendada' : 'Anotação pendente';
    document.getElementById('modal-textarea').value = sessao.anotacao || '';

    document.getElementById('modal-anotacao').classList.add('open');
    document.getElementById('modal-overlay').classList.add('open');
}

function fecharModal() {
    document.getElementById('modal-anotacao').classList.remove('open');
    document.getElementById('modal-overlay').classList.remove('open');
    sessaoAtiva = null;
    pacienteAtivo = null;
}

function salvarAnotacao() {
    if (!sessaoAtiva) return;

    const texto = document.getElementById('modal-textarea').value.trim();
    if (!texto) return;

    const paciente = pacientesMockados.find(p => p.nome === sessaoAtiva.paciente);
    if (!paciente) return;

    paciente.sessoes[sessaoAtiva.index].anotacao = texto;
    paciente.sessoes[sessaoAtiva.index].realizada = true;

    fecharModal();
    abrirDrawer(sessaoAtiva.paciente);
}

//Init global
document.addEventListener('DOMContentLoaded', () => {
    // Global
    iniciarSearch();
    iniciarAbasDrawer();

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