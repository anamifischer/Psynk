// ----------------------------------------------------------------
// utils.js - funções compartilhadas entre as páginas
// ----------------------------------------------------------------


// ----------------------------------------------------------------
// Estado
// ----------------------------------------------------------------

let pacienteAtual = null;        // dados do paciente aberto no drawer
let pacienteAbertoId = null;
let consultaEmEdicao = null;     // preenchido quando o modal está editando
let pacientesModal = [];         // pacientes carregados no modal de consulta
let permissaoUsuario = '';        // perfil de quem está logado (vem da api)
let recarregarAoFechar = false;  // recarrega a página quando o drawer fechar


// ----------------------------------------------------------------
// Auxiliares
// ----------------------------------------------------------------

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML;
}

// POST simples para a api, devolve { ok, json }
async function enviarPost(url, dados) {
    const resp = await fetch(url, { method: 'POST', body: new URLSearchParams(dados) });
    const json = await resp.json().catch(() => ({}));
    return { ok: resp.ok, json };
}

// seleciona uma aba do drawer pelo nome
function irParaAba(nome) {
    document.querySelectorAll('.drawer-aba').forEach(a => a.classList.remove('active'));
    document.querySelectorAll('.drawer-painel').forEach(p => p.classList.remove('active'));
    document.querySelector(`.drawer-aba[data-aba="${nome}"]`)?.classList.add('active');
    document.getElementById('aba-' + nome)?.classList.add('active');
}

// recebe 'aaaa-mm-dd' e diz se é hoje (data local)
function dataEhHoje(dataIso) {
    const h = new Date();
    const hoje = h.getFullYear() + '-' + String(h.getMonth() + 1).padStart(2, '0') + '-' + String(h.getDate()).padStart(2, '0');
    return dataIso === hoje;
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

function badgeClassPagamento(status) {
    const map = {
        'Pago': 'verde',
        'Pendente': 'amarelo',
        'Atrasado': 'vermelho',
    };
    return map[status] || 'cinza';
}


// ----------------------------------------------------------------
// Calendário
// ----------------------------------------------------------------

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

    const primeiroDia = new Date(ano, mes, 1).getDay();
    const quantidadeDias = new Date(ano, mes + 1, 0).getDate();
    const hoje = new Date();

    // espaços vazios antes do dia 1
    for (let i = 0; i < primeiroDia; i++) {
        dias.appendChild(document.createElement("span"));
    }

    for (let dia = 1; dia <= quantidadeDias; dia++) {
        const botao = document.createElement("button");
        botao.textContent = dia;
        botao.classList.add("dia");
        if (dia === hoje.getDate() && mes === hoje.getMonth() && ano === hoje.getFullYear()) {
            botao.classList.add("hoje");
        }
        dias.appendChild(botao);
    }
}

function iniciarCalendario() {
    const anterior = document.getElementById("mes-anterior");
    const proximo = document.getElementById("mes-proximo");

    if (anterior) {
        anterior.addEventListener("click", () => {
            dataAtual.setMonth(dataAtual.getMonth() - 1);
            criarCalendario();
        });
    }
    if (proximo) {
        proximo.addEventListener("click", () => {
            dataAtual.setMonth(dataAtual.getMonth() + 1);
            criarCalendario();
        });
    }
    criarCalendario();
}


// ----------------------------------------------------------------
// Listas e filtros
// ----------------------------------------------------------------

// abre uma lista e fecha as outras
function toggleList(id) {
    const alvo = document.getElementById(id);
    if (!alvo) return;
    const jaEstaAberto = alvo.classList.contains('open');
    document.querySelectorAll('.collapse-list').forEach(el => el.classList.remove('open'));
    if (!jaEstaAberto) alvo.classList.add('open');
}

function toggleFiltroDropdown() {
    document.getElementById('filtro-dropdown')?.classList.toggle('open');
}

// clicou fora do filtro, fecha
document.addEventListener('click', (e) => {
    const wrapper = document.querySelector('.filtro-dropdown-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('filtro-dropdown')?.classList.remove('open');
    }
});


// ----------------------------------------------------------------
// Busca de pacientes (barra do topo)
// ----------------------------------------------------------------

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


// ----------------------------------------------------------------
// Modal de consulta (criar e editar)
// ----------------------------------------------------------------

async function abrirModalConsulta(idPaciente = null) {
    consultaEmEdicao = null;
    prepararModalConsulta('criar');
    const modal = document.getElementById('modal-consulta');
    const overlay = document.getElementById('modal-consulta-overlay');
    if (!modal || !overlay) return;

    let dados;
    try {
        const resp = await fetch('/app/api/modal_consulta_dados.php');
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        dados = await resp.json();
    } catch (erro) {
        console.error('Erro ao carregar dados do modal:', erro);
        return;
    }
    pacientesModal = dados.pacientes;
    permissaoUsuario = dados.usuario.permissao;

    // recepção não lança anotação prévia
    document.getElementById('modal-consulta-campo-anotacao').style.display =
        permissaoUsuario === 'RECEPCIONISTA' ? 'none' : '';

    const selectPaciente = document.getElementById('modal-consulta-paciente');
    selectPaciente.innerHTML = '<option value="">Selecione o paciente...</option>';
    dados.pacientes.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.nome;
        selectPaciente.appendChild(opt);
    });

    const selectPsicologo = document.getElementById('modal-consulta-psicologo');
    selectPsicologo.innerHTML = '<option value="">Selecione o psicólogo...</option>';
    dados.psicologos.forEach(ps => {
        const opt = document.createElement('option');
        opt.value = ps.id;
        opt.textContent = ps.nome;
        selectPsicologo.appendChild(opt);
    });

    // data padrão é hoje (data local, não UTC)
    const hoje = new Date();
    document.getElementById('modal-consulta-data').value = hoje.getFullYear() + '-'
        + String(hoje.getMonth() + 1).padStart(2, '0') + '-'
        + String(hoje.getDate()).padStart(2, '0');
    document.getElementById('modal-consulta-hora').value = '09:00';
    document.getElementById('modal-consulta-anotacao').value = '';

    // se veio de um paciente, já seleciona ele e o responsável
    const subtitulo = document.getElementById('modal-consulta-subtitulo');
    const paciente = idPaciente ? dados.pacientes.find(p => p.id === Number(idPaciente)) : null;
    if (paciente) {
        selectPaciente.value = paciente.id;
        selectPsicologo.value = paciente.idPsicologo;
        subtitulo.textContent = 'Agendando para ' + paciente.nome;
    } else {
        selectPsicologo.value = '';
        subtitulo.textContent = '';
    }

    atualizarHorarioFim();
    modal.classList.add('open');
    overlay.classList.add('open');
}

function fecharModalConsulta() {
    document.getElementById('modal-consulta')?.classList.remove('open');
    document.getElementById('modal-consulta-overlay')?.classList.remove('open');
}

// escolheu o paciente, puxa o psicólogo responsável
function preencherPsicologo() {
    const id = Number(document.getElementById('modal-consulta-paciente').value);
    if (!id) return;
    const paciente = pacientesModal.find(p => p.id === id);
    if (paciente) {
        document.getElementById('modal-consulta-psicologo').value = paciente.idPsicologo;
    }
}

function atualizarHorarioFim() {
    const hora = document.getElementById('modal-consulta-hora').value;
    const duracao = parseInt(document.getElementById('modal-consulta-duracao').value);
    const fimEl = document.getElementById('modal-consulta-horario-fim');
    if (!hora || !duracao || !fimEl) return;

    const [h, m] = hora.split(':').map(Number);
    const total = h * 60 + m + duracao;
    const hFim = String(Math.floor(total / 60) % 24).padStart(2, '0');
    const mFim = String(total % 60).padStart(2, '0');
    fimEl.textContent = `Término previsto às ${hFim}:${mFim}`;
}

// o mesmo modal serve para criar e editar
function prepararModalConsulta(modo) {
    const editando = modo === 'editar';
    document.getElementById('modal-consulta-titulo').textContent = editando ? 'Editar consulta' : 'Nova consulta';
    document.getElementById('btn-salvar-consulta').textContent   = editando ? 'Salvar alterações' : 'Agendar consulta';
    document.getElementById('btn-cancelar-consulta').style.display = 'none';
    document.getElementById('modal-consulta-paciente').disabled  = editando;
    document.getElementById('modal-consulta-psicologo').disabled = editando;
    ['modal-consulta-data', 'modal-consulta-hora', 'modal-consulta-duracao', 'modal-consulta-anotacao']
        .forEach(id => { document.getElementById(id).disabled = false; });
    document.getElementById('modal-consulta-campo-anotacao').style.display = '';
    document.getElementById('modal-consulta-rotulo-anotacao').innerHTML =
        editando ? 'Anotação' : 'Anotação prévia <span class="modal-consulta-opcional">(opcional)</span>';
}

async function abrirModalEdicao(idConsulta) {
    if (!pacienteAtual) return;
    const sessao = pacienteAtual.sessoes.find(s => s.id === Number(idConsulta));
    if (!sessao) return;

    await abrirModalConsulta(pacienteAtual.id);
    consultaEmEdicao = sessao;
    prepararModalConsulta('editar');

    document.getElementById('modal-consulta-paciente').value  = pacienteAtual.id;
    document.getElementById('modal-consulta-psicologo').value = sessao.psicologoId;
    document.getElementById('modal-consulta-data').value      = sessao.dataIso;
    document.getElementById('modal-consulta-hora').value      = sessao.hora;
    document.getElementById('modal-consulta-duracao').value   = String(sessao.duracao);
    document.getElementById('modal-consulta-anotacao').value  = sessao.anotacao || '';
    document.getElementById('modal-consulta-subtitulo').textContent = 'Consulta de ' + pacienteAtual.nome;

    // só libera o que as permissões deixam
    ['modal-consulta-data', 'modal-consulta-hora', 'modal-consulta-duracao']
        .forEach(id => { document.getElementById(id).disabled = !sessao.podeRemarcar; });
    document.getElementById('modal-consulta-anotacao').disabled = !sessao.podeAnotar;
    document.getElementById('modal-consulta-campo-anotacao').style.display =
        pacienteAtual.mostrarAnotacoes ? '' : 'none';
    document.getElementById('btn-cancelar-consulta').style.display = sessao.podeCancelar ? '' : 'none';

    atualizarHorarioFim();
}

async function salvarConsulta() {
    const paciente  = document.getElementById('modal-consulta-paciente').value;
    const psicologo = document.getElementById('modal-consulta-psicologo').value;
    const data      = document.getElementById('modal-consulta-data').value;
    const hora      = document.getElementById('modal-consulta-hora').value;
    const duracao   = document.getElementById('modal-consulta-duracao').value;
    const anotacao  = document.getElementById('modal-consulta-anotacao').value.trim();

    // ao editar, paciente e psicólogo ficam travados e nem vão no envio
    const faltaPessoa = !consultaEmEdicao && (!paciente || !psicologo);
    if (faltaPessoa || !data || !hora) {
        alert('Preencha todos os campos obrigatórios.');
        return;
    }

    const dados = { data, hora_inicio: hora, duracao, anotacao };
    let url;
    if (consultaEmEdicao) {
        url = '/app/api/consulta_editar.php';
        dados.id_consulta = consultaEmEdicao.id;
    } else {
        url = '/app/api/consulta_criar.php';
        dados.id_paciente = paciente;
        dados.id_psicologo = psicologo;
    }

    const btn = document.getElementById('btn-salvar-consulta');
    btn.disabled = true;
    try {
        const { ok, json } = await enviarPost(url, dados);
        if (!ok) { alert(json.erro || 'Não foi possível salvar.'); return; }

        const drawerAberto = document.getElementById('drawer-paciente').classList.contains('open');
        fecharModalConsulta();
        if (drawerAberto && pacienteAbertoId) {
            await abrirDrawer(pacienteAbertoId);
            irParaAba('anotacoes');
        } else {
            location.reload();
        }
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão ao salvar a consulta.');
    } finally {
        btn.disabled = false;
    }
}

async function cancelarConsultaAtual() {
    if (!consultaEmEdicao) return;
    let msg = 'Cancelar esta consulta? Ela continuará no histórico como cancelada.';
    if (consultaEmEdicao.pagamentoPago) {
        msg += '\n\nATENÇÃO: o pagamento desta consulta já foi pago e será mantido. O estorno deve ser tratado à parte.';
    }
    if (!confirm(msg)) return;
    const id = consultaEmEdicao.id;
    fecharModalConsulta();
    await alterarStatusConsulta(id, 'cancelar');
}


// ----------------------------------------------------------------
// Ações de consulta e pagamento
// ----------------------------------------------------------------

// concluir, desconcluir, confirmar, desconfirmar, cancelar (de dentro do drawer)
async function alterarStatusConsulta(idConsulta, acao) {
    try {
        const { ok, json } = await enviarPost('/app/api/consulta_status.php', { id_consulta: idConsulta, acao });
        if (!ok) { alert(json.erro || 'Não foi possível alterar a consulta.'); return; }
        await abrirDrawer(pacienteAbertoId);
        irParaAba('anotacoes');
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão.');
    }
}

// confirmar direto da tabela do dashboard
async function confirmarConsulta(idConsulta) {
    try {
        const { ok, json } = await enviarPost('/app/api/consulta_status.php', { id_consulta: idConsulta, acao: 'confirmar' });
        if (!ok) { alert(json.erro || 'Não foi possível confirmar.'); return; }
        location.reload();
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão.');
    }
}

// botão "Lançar" do dashboard
async function abrirModalAnotacao(idPaciente, idConsulta) {
    await abrirDrawer(idPaciente);
    abrirModalEdicao(idConsulta);
}

async function alterarPagamento(idPagamento, acao) {
    if (acao === 'desfazer' && !confirm('Desfazer o recebimento deste pagamento?')) return;
    try {
        const { ok, json } = await enviarPost('/app/api/pagamento_receber.php', { id_pagamento: idPagamento, acao });
        if (!ok) { alert(json.erro || 'Não foi possível alterar o pagamento.'); return; }
        recarregarAoFechar = true;
        await abrirDrawer(pacienteAbertoId);
        irParaAba('pagamentos');
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão.');
    }
}


// ----------------------------------------------------------------
// Drawer do paciente
// ----------------------------------------------------------------

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
    pacienteAtual = paciente;
    prepararDrawer('existente');

    preencherDadosDrawer(paciente);
    renderizarAnotacoes(paciente);
    renderizarPagamentos(paciente);

    document.getElementById('drawer-paciente').classList.add('open');
    document.getElementById('drawer-overlay').classList.add('open');
    irParaAba('dados');

    // limpa a busca do topo
    document.getElementById('search-resultados')?.classList.remove('open');
    const searchInput = document.getElementById('search-paciente');
    if (searchInput) searchInput.value = '';
}

// aba Dados
function preencherDadosDrawer(paciente) {
    document.getElementById('drawer-nome').textContent = paciente.nome;
    document.getElementById('drawer-nome-input').value = paciente.nome;

    const cpf = document.getElementById('drawer-cpf');
    if (cpf) cpf.value = paciente.cpf || '';

    const statusEl = document.getElementById('drawer-status');
    statusEl.textContent = paciente.status;
    statusEl.className = `badge badge-${badgeClassStatus(paciente.status)}`;

    const statusSelect = document.getElementById('drawer-status-select');
    if (statusSelect) statusSelect.value = paciente.statusValor;

    // a api manda dd/mm/aaaa, o input quer aaaa-mm-dd
    const nascimento = document.getElementById('drawer-nascimento');
    if (nascimento && paciente.nascimento) {
        const [d, m, a] = paciente.nascimento.split('/');
        nascimento.value = `${a}-${m}-${d}`;
    }

    const telefone = document.getElementById('drawer-telefone');
    if (telefone) telefone.value = paciente.telefone || '';

    const endereco = document.getElementById('drawer-endereco');
    if (endereco) endereco.value = paciente.endereco || '';

    const psicologoSelect = document.getElementById('drawer-psicologo-select');
    if (psicologoSelect) {
        psicologoSelect.innerHTML = paciente.psicologos
            .map(ps => `<option value="${ps.id}">${escaparHtml(ps.nome)}</option>`)
            .join('');
        psicologoSelect.value = paciente.idPsicologo;
        psicologoSelect.disabled = !paciente.podeAlterarResponsavel;
    }

    const observacoes = document.getElementById('drawer-observacoes');
    if (observacoes) observacoes.value = paciente.observacoes || '';

    const btnImprimir = document.getElementById('btn-imprimir');
    if (btnImprimir) {
        btnImprimir.style.display = paciente.mostrarAnotacoes ? '' : 'none';
        btnImprimir.onclick = () => window.open('/app/api/prontuario_pdf.php?id=' + paciente.id, '_blank');
    }
}

// badge e cor da data de cada sessão
function badgeDaSessao(s, paciente) {
    const anotacaoPendente = ['aguarda data', 'atrasada'].includes(s.status)
        && s.jaComecou && !s.anotacao && paciente.mostrarAnotacoes;

    let badge;
    if (s.status === 'concluida')          badge = '<span class="badge badge-verde">Concluída</span>';
    else if (s.status === 'cancelada')     badge = '<span class="badge badge-cinza">Cancelada</span>';
    else if (anotacaoPendente)             badge = '<span class="badge badge-vermelho">Anotação pendente</span>';
    else if (!s.jaComecou && s.confirmada) badge = '<span class="badge badge-verde">Confirmada</span>';
    else if (!s.jaComecou && dataEhHoje(s.dataIso)) badge = '<span class="badge badge-laranja">Aguardando confirmação</span>';
    else if (!s.jaComecou)                 badge = '<span class="badge badge-amarelo">Aguardando confirmação</span>';
    else                                   badge = '<span class="badge badge-amarelo">Aguardando conclusão</span>';

    let corData = '';
    if (!s.jaComecou && s.status !== 'cancelada') corData = 'sessao-data-futura';
    else if (anotacaoPendente) corData = 'sessao-data-pendente';

    return { badge, corData };
}

function botoesDaSessao(s) {
    const botoes = [];
    const btn = (acao, texto) =>
        `<button class="btn btn-sm btn-secondary" onclick="alterarStatusConsulta(${s.id}, '${acao}')">${texto}</button>`;

    if (s.podeConfirmar) botoes.push(s.confirmada ? btn('desconfirmar', 'Desfazer confirmação') : btn('confirmar', 'Confirmar'));
    if (s.podeConcluir)    botoes.push(btn('concluir', 'Concluir'));
    if (s.podeDesconcluir) botoes.push(btn('desconcluir', 'Desfazer conclusão'));
    if (s.podeRemarcar || s.podeAnotar) {
        botoes.push(`<button class="btn btn-sm btn-secondary btn-icone" title="Editar" onclick="abrirModalEdicao(${s.id})"><img src="/imgs/icons/editar.png" alt="Editar" width="14" height="14"></button>`);
    }
    return botoes.join('');
}

// aba Anotações: sessões e trocas de responsável, da mais recente para a mais antiga
function renderizarAnotacoes(paciente) {
    const painel = document.getElementById('aba-anotacoes');
    if (!painel) return;

    const historico = paciente.historicoResponsavel || [];
    if (!(paciente.sessoes || []).length && !historico.length) {
        painel.innerHTML = '<p class="drawer-vazio">Nenhuma consulta registrada.</p>';
        return;
    }

    const itens = (paciente.sessoes || []).map(s => {
        const { badge, corData } = badgeDaSessao(s, paciente);
        const texto = s.anotacao ? `<p class="sessao-anotacao">${escaparHtml(s.anotacao)}</p>` : '';
        return {
            chave: s.dataIso + ' ' + s.hora,
            html: `
                <div class="sessao-item">
                    <div class="sessao-header">
                        <span class="sessao-data ${corData}">${s.data}</span>
                        <span class="sessao-hora">${s.hora}</span>
                        ${badge}
                        ${botoesDaSessao(s)}
                    </div>
                    ${texto}
                </div>`
        };
    });

    historico.forEach(h => {
        itens.push({
            chave: h.quando,
            html: `<div class="historico-linha">${h.data} · ${escaparHtml(h.texto)}</div>`
        });
    });

    itens.sort((a, b) => b.chave.localeCompare(a.chave));
    painel.innerHTML = itens.map(i => i.html).join('');
}

// aba Pagamentos
function renderizarPagamentos(paciente) {
    const painel = document.getElementById('aba-pagamentos');
    if (!painel) return;

    if (!paciente.pagamentos || !paciente.pagamentos.length) {
        painel.innerHTML = '<p class="drawer-vazio">Nenhum pagamento registrado.</p>';
        return;
    }

    painel.innerHTML = paciente.pagamentos.map(pg => {
        let botao = '';
        if (pg.podeReceber && pg.status === 'Pago') {
            botao = `<button class="btn btn-sm btn-secondary" onclick="alterarPagamento(${pg.id}, 'desfazer')">Desfazer</button>`;
        } else if (pg.podeReceber) {
            botao = `<button class="btn btn-sm btn-secondary" onclick="alterarPagamento(${pg.id}, 'receber')">Marcar como recebido</button>`;
        }
        const recebido = pg.dataPagamento ? `<span class="sessao-hora">recebido em ${pg.dataPagamento}</span>` : '';
        return `
            <div class="pagamento-item">
                <div class="pagamento-info">
                    <span class="pagamento-data">${pg.data}</span>
                    <span class="badge badge-${badgeClassPagamento(pg.status)}">${pg.status}</span>
                    ${recebido}
                </div>
                ${botao}
                <span class="pagamento-valor">R$ ${pg.valor.toFixed(2).replace('.', ',')}</span>
            </div>`;
    }).join('');
}

// o mesmo drawer serve para paciente existente e cadastro novo
function prepararDrawer(modo) {
    const novo = modo === 'novo';
    document.querySelector('.drawer-abas').style.display = novo ? 'none' : '';
    document.getElementById('drawer-status').style.display = novo ? 'none' : '';
    document.getElementById('drawer-cpf').disabled = !novo;
    document.querySelector('#aba-dados .btn-primary').textContent = novo ? 'Cadastrar paciente' : 'Atualizar dados';
    if (novo) irParaAba('dados');
}

async function abrirDrawerNovo() {
    let dados;
    try {
        const resp = await fetch('/app/api/modal_consulta_dados.php');
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        dados = await resp.json();
    } catch (erro) {
        console.error('Erro ao carregar psicólogos:', erro);
        return;
    }

    pacienteAtual = null;
    pacienteAbertoId = null;
    prepararDrawer('novo');

    document.getElementById('drawer-nome').textContent = 'Novo paciente';
    ['drawer-nome-input', 'drawer-cpf', 'drawer-nascimento', 'drawer-telefone', 'drawer-endereco', 'drawer-observacoes']
        .forEach(id => { document.getElementById(id).value = ''; });
    document.getElementById('drawer-status-select').value = 'ativo';

    // psicólogo já vem fixo no próprio nome; recepção e admin escolhem
    const select = document.getElementById('drawer-psicologo-select');
    select.innerHTML = '<option value="">Selecione o responsável...</option>'
        + dados.psicologos.map(ps => `<option value="${ps.id}">${escaparHtml(ps.nome)}</option>`).join('');
    if (dados.usuario.permissao === 'PSICOLOGO') {
        select.value = dados.usuario.id;
        select.disabled = true;
    } else {
        select.value = '';
        select.disabled = false;
    }

    document.getElementById('drawer-paciente').classList.add('open');
    document.getElementById('drawer-overlay').classList.add('open');
}

async function salvarDadosPaciente() {
    const novo = !pacienteAtual;
    const dados = {
        nome:         document.getElementById('drawer-nome-input').value.trim(),
        status:       document.getElementById('drawer-status-select').value,
        nascimento:   document.getElementById('drawer-nascimento').value,
        telefone:     document.getElementById('drawer-telefone').value.trim(),
        endereco:     document.getElementById('drawer-endereco').value.trim(),
        id_psicologo: document.getElementById('drawer-psicologo-select').value,
        observacoes:  document.getElementById('drawer-observacoes').value.trim(),
    };
    if (novo) dados.cpf = document.getElementById('drawer-cpf').value.trim();
    else      dados.id_paciente = pacienteAtual.id;

    const btn = document.querySelector('#aba-dados .btn-primary');
    btn.disabled = true;
    try {
        const { ok, json } = await enviarPost(
            novo ? '/app/api/paciente_criar.php' : '/app/api/paciente_atualizar.php', dados);
        if (!ok) { alert(json.erro || 'Não foi possível salvar.'); return; }

        recarregarAoFechar = !!document.getElementById('listaCadastrados');
        await abrirDrawer(novo ? json.id : pacienteAtual.id);

        const btnNovo = document.querySelector('#aba-dados .btn-primary');
        btnNovo.textContent = novo ? 'Cadastrado!' : 'Salvo!';
        setTimeout(() => btnNovo.textContent = 'Atualizar dados', 2000);
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão ao salvar.');
    } finally {
        btn.disabled = false;
    }
}

function fecharDrawer() {
    document.getElementById('drawer-paciente').classList.remove('open');
    document.getElementById('drawer-overlay').classList.remove('open');
    if (recarregarAoFechar) location.reload();
}

function iniciarAbasDrawer() {
    document.querySelectorAll('.drawer-aba').forEach(aba => {
        aba.addEventListener('click', () => irParaAba(aba.dataset.aba));
    });
}


// ----------------------------------------------------------------
// Inicialização
// ----------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    iniciarCalendario();
    iniciarSearch();
    iniciarAbasDrawer();

    // listas que começam abertas em cada página
    ['listaConsultas', 'listaCadastrados', 'listaVencendo'].forEach(id => {
        document.getElementById(id)?.classList.add('open');
    });

    // recalcula o horário de término do modal
    document.getElementById('modal-consulta-hora')?.addEventListener('change', atualizarHorarioFim);
    document.getElementById('modal-consulta-duracao')?.addEventListener('change', atualizarHorarioFim);
});
