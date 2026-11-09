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
        const hoje = new Date();
        if (
            dia === hoje.getDate() &&
            mes === hoje.getMonth() &&
            ano === hoje.getFullYear()
        ) {
            elementoDia.classList.add("hoje");
        }
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


function iniciarSearch() {
    const input = document.getElementById('search-paciente');
    const resultados = document.getElementById('search-resultados');
    if (!input || !resultados) return;

    input.addEventListener('input', async () => {
        const termo = input.value.trim();
        if (!termo) { resultados.classList.remove('open'); return; }

        const resp = await fetch('/app/api/buscar_pacientes.php?q=' + encodeURIComponent(termo));
        const pacientes = await resp.json();

        if (!pacientes.length) { resultados.classList.remove('open'); return; }

        resultados.innerHTML = '';
        pacientes.forEach(p => {
            const item = document.createElement('div');
            item.className = 'search-resultado-item';
            item.innerHTML = `
                <div>
                    <div class="search-resultado-nome"></div>
                    <div class="search-resultado-psicologo"></div>
                </div>
            `;
            item.querySelector('.search-resultado-nome').textContent = p.nome;
            item.querySelector('.search-resultado-psicologo').textContent = p.nome_psicologo;
            item.onclick = () => abrirDrawer(p.idpaciente);
            resultados.appendChild(item);
        });

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

// ── MODAL NOVA CONSULTA ──

function abrirModalConsulta(nomePaciente = null) {
    const modal = document.getElementById('modal-consulta');
    const overlay = document.getElementById('modal-consulta-overlay');
    if (!modal || !overlay) return;

    // popula select de pacientes
    const selectPaciente = document.getElementById('modal-consulta-paciente');
    selectPaciente.innerHTML = '<option value="">Selecione o paciente...</option>';
    pacientesMockados.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.nome;
        opt.textContent = p.nome;
        selectPaciente.appendChild(opt);
    });

    // define data padrão como hoje
    const hoje = new Date();
    const dataFormatada = hoje.toISOString().split('T')[0];
    document.getElementById('modal-consulta-data').value = dataFormatada;
    document.getElementById('modal-consulta-hora').value = '09:00';
    document.getElementById('modal-consulta-anotacao').value = '';
    document.getElementById('modal-consulta-status').value = 'pendente';

    // se veio de um paciente, preenche e já seleciona o psicólogo
    if (nomePaciente) {
        const paciente = pacientesMockados.find(p => p.nome === nomePaciente);
        if (paciente) {
            selectPaciente.value = paciente.nome;
            document.getElementById('modal-consulta-psicologo').value = paciente.psicologo;
            document.getElementById('modal-consulta-subtitulo').textContent = `Agendando para ${paciente.nome}`;
        }
    } else {
        document.getElementById('modal-consulta-psicologo').value = '';
        document.getElementById('modal-consulta-subtitulo').textContent = '';
    }

    atualizarHorarioFim();

    modal.classList.add('open');
    overlay.classList.add('open');
}

function fecharModalConsulta() {
    const modal = document.getElementById('modal-consulta');
    const overlay = document.getElementById('modal-consulta-overlay');
    if (modal) modal.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
}

function preencherPsicologo() {
    const selectPaciente = document.getElementById('modal-consulta-paciente');
    const nomeSelecionado = selectPaciente.value;
    if (!nomeSelecionado) return;

    const paciente = pacientesMockados.find(p => p.nome === nomeSelecionado);
    if (paciente) {
        document.getElementById('modal-consulta-psicologo').value = paciente.psicologo;
    }
}

function atualizarHorarioFim() {
    const hora = document.getElementById('modal-consulta-hora').value;
    const duracao = parseInt(document.getElementById('modal-consulta-duracao').value);
    const fimEl = document.getElementById('modal-consulta-horario-fim');

    if (!hora || !duracao || !fimEl) return;

    const [h, m] = hora.split(':').map(Number);
    const totalMinutos = h * 60 + m + duracao;
    const hFim = String(Math.floor(totalMinutos / 60) % 24).padStart(2, '0');
    const mFim = String(totalMinutos % 60).padStart(2, '0');

    fimEl.textContent = `Término previsto às ${hFim}:${mFim}`;
}

function salvarConsulta() {
    const paciente  = document.getElementById('modal-consulta-paciente').value;
    const psicologo = document.getElementById('modal-consulta-psicologo').value;
    const data      = document.getElementById('modal-consulta-data').value;
    const hora      = document.getElementById('modal-consulta-hora').value;
    const status    = document.getElementById('modal-consulta-status').value;
    const anotacao  = document.getElementById('modal-consulta-anotacao').value.trim();

    if (!paciente || !psicologo || !data || !hora) {
        alert('Preencha todos os campos obrigatórios.');
        return;
    }

    // feedback visual
    const btn = document.querySelector('#modal-consulta .btn-primary');
    btn.textContent = 'Agendado!';
    btn.disabled = true;

    setTimeout(() => {
        fecharModalConsulta();
        btn.textContent = 'Agendar consulta';
        btn.disabled = false;
    }, 1500);
}

// recalcula horário de fim ao mudar hora ou duração
document.addEventListener('DOMContentLoaded', () => {
    const horaInput    = document.getElementById('modal-consulta-hora');
    const duracaoInput = document.getElementById('modal-consulta-duracao');
    if (horaInput)    horaInput.addEventListener('change', atualizarHorarioFim);
    if (duracaoInput) duracaoInput.addEventListener('change', atualizarHorarioFim);
});

//Drawer
let pacienteAbertoId = null;

async function abrirDrawer(idPaciente) {
    let paciente;
    try {
        const resp = await fetch('/app/api/paciente_detalhes.php?id=' + encodeURIComponent(idPaciente));
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        paciente = await resp.json();
    } catch (erro) {
        console.error('Erro ao carregar paciente:', erro);
        return;
    }
    pacienteAbertoId = paciente.id;

    // topo do drawer
    document.getElementById('drawer-nome').textContent = paciente.nome;
    document.getElementById('drawer-nome-input').value = paciente.nome;

    const statusEl = document.getElementById('drawer-status');
    statusEl.textContent = paciente.status;
    statusEl.className = `badge badge-${badgeClassStatus(paciente.status)}`;

    // aba dados — campos editáveis
    const statusSelect = document.getElementById('drawer-status-select');
    if (statusSelect) statusSelect.value = paciente.statusValor;

    const nascimentoInput = document.getElementById('drawer-nascimento');
    if (nascimentoInput && paciente.nascimento) {
        const partes = paciente.nascimento.split('/');
        nascimentoInput.value = `${partes[2]}-${partes[1]}-${partes[0]}`;
    }

    const telefoneInput = document.getElementById('drawer-telefone');
    if (telefoneInput) telefoneInput.value = paciente.telefone || '';

    const enderecoInput = document.getElementById('drawer-endereco');
    if (enderecoInput) enderecoInput.value = paciente.endereco || '';

    const psicologoSelect = document.getElementById('drawer-psicologo-select');
    if (psicologoSelect) {
        psicologoSelect.innerHTML = paciente.psicologos
            .map(ps => `<option value="${ps.id}">${ps.nome}</option>`)
            .join('');
        psicologoSelect.value = paciente.idPsicologo;
    }

    const observacoesInput = document.getElementById('drawer-observacoes');
    if (observacoesInput) observacoesInput.value = paciente.observacoes || '';

    // aba anotações — visibilidade
    const abaAnotacoes = document.querySelector('.drawer-aba[data-aba="anotacoes"]');
    if (abaAnotacoes) {
        abaAnotacoes.style.display = paciente.mostrarAnotacoes ? '' : 'none';
    }

    // aba anotações — renderiza sessões
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

    // aba pagamentos — renderiza
    const abaPagamentosPanel = document.getElementById('aba-pagamentos');
    if (abaPagamentosPanel) {
        if (!paciente.pagamentos || !paciente.pagamentos.length) {
            abaPagamentosPanel.innerHTML = '<p class="drawer-vazio">Nenhum pagamento registrado.</p>';
        } else {
            abaPagamentosPanel.innerHTML = paciente.pagamentos.map(pg => `
                <div class="pagamento-item">
                    <div class="pagamento-info">
                        <span class="pagamento-data">${pg.data}</span>
                        <span class="badge badge-${badgeClassPagamento(pg.status)}">${pg.status}</span>
                    </div>
                    <span class="pagamento-valor">R$ ${pg.valor.toFixed(2).replace('.', ',')}</span>
                </div>
            `).join('');
        }
    }

    // abre o drawer
    document.getElementById('drawer-paciente').classList.add('open');
    document.getElementById('drawer-overlay').classList.add('open');

    // reseta abas para "dados"
    document.querySelectorAll('.drawer-aba').forEach(a => a.classList.remove('active'));
    document.querySelectorAll('.drawer-painel').forEach(p => p.classList.remove('active'));
    document.querySelector('.drawer-aba[data-aba="dados"]').classList.add('active');
    document.getElementById('aba-dados').classList.add('active');

    // limpa search
    const searchResultados = document.getElementById('search-resultados');
    const searchInput = document.getElementById('search-paciente');
    if (searchResultados) searchResultados.classList.remove('open');
    if (searchInput) searchInput.value = '';
}

function salvarDadosPaciente() {
    const nome = document.getElementById('drawer-nome').textContent;
    const paciente = pacientesMockados.find(p => p.nome === nome);
    if (!paciente) return;

    paciente.status      = document.getElementById('drawer-status-select').value;
    paciente.telefone    = document.getElementById('drawer-telefone').value;
    paciente.endereco    = document.getElementById('drawer-endereco').value;
    paciente.psicologo   = document.getElementById('drawer-psicologo-select').value;
    paciente.observacoes = document.getElementById('drawer-observacoes').value;

    // atualiza badge de status no topo do drawer
    const statusEl = document.getElementById('drawer-status');
    statusEl.textContent = paciente.status;
    statusEl.className = `badge badge-${badgeClassStatus(paciente.status)}`;

    // feedback visual
    const btn = document.querySelector('#aba-dados .btn-primary');
    btn.textContent = 'Salvo!';
    setTimeout(() => btn.textContent = 'Atualizar dados', 2000);


    // renderiza pagamentos
    const abaPagamentosPanel = document.getElementById('aba-pagamentos');
    if (abaPagamentosPanel) {
        if (!paciente.pagamentos || !paciente.pagamentos.length) {
            abaPagamentosPanel.innerHTML = '<p class="drawer-vazio">Nenhum pagamento registrado.</p>';
        } else {
            abaPagamentosPanel.innerHTML = paciente.pagamentos.map(pg => `
                <div class="pagamento-item">
                    <div class="pagamento-info">
                        <span class="pagamento-data">${pg.data}</span>
                        <span class="badge badge-${badgeClassPagamento(pg.status)}">${pg.status}</span>
                    </div>
                    <span class="pagamento-valor">R$ ${pg.valor.toFixed(2).replace('.', ',')}</span>
                </div>
            `).join('');
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

function badgeClassPagamento(status) {
    const map = {
        'Pago': 'verde',
        'Pendente': 'amarelo',
        'Atrasado': 'vermelho',
    };
    return map[status] || 'cinza';
}

function abrirDrawerPagamentos(nomePaciente) {
    abrirDrawer(nomePaciente);

    document.querySelectorAll('.drawer-aba').forEach(a => a.classList.remove('active'));
    document.querySelectorAll('.drawer-painel').forEach(p => p.classList.remove('active'));
    document.querySelector('.drawer-aba[data-aba="pagamentos"]').classList.add('active');
    document.getElementById('aba-pagamentos').classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    // Global
    iniciarSearch();
    iniciarAbasDrawer();

    if (document.getElementById('listaConsultas')) {
        document.getElementById('listaConsultas').classList.add('open');
    }

    // Pacientes
    if (document.getElementById('listaCadastrados')) {
        document.getElementById('listaCadastrados').classList.add('open');
        iniciarFiltrosPacientes();
    }

    // Financeiro
    if (document.getElementById('listaVencendo')) {
        document.getElementById('listaVencendo').classList.add('open');
    }

    // lista faturamento
    if (document.getElementById('faturamento-lista-body')) {
        document.getElementById('faturamentoClinica').classList.add('open');
        renderizarFaturamentoLista();
    }

    if (document.getElementById('graficoReceita')) {
        iniciarFiltroAnos();
    }

    // Modal consulta
    const horaInput    = document.getElementById('modal-consulta-hora');
    const duracaoInput = document.getElementById('modal-consulta-duracao');
    if (horaInput)    horaInput.addEventListener('change', atualizarHorarioFim);
    if (duracaoInput) duracaoInput.addEventListener('change', atualizarHorarioFim);

    // Botão agendar consulta na drawer
    const btnConsulta = document.getElementById('btn-consulta');
    if (btnConsulta) {
        btnConsulta.addEventListener('click', () => {
            const nome = document.getElementById('drawer-nome').textContent;
            abrirModalConsulta(nome);
        });
    }
});