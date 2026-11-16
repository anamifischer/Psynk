// Navegação entre seções
document.querySelectorAll('.config-nav-item').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.config-nav-item').forEach(b => b.classList.remove('ativo'));
        document.querySelectorAll('.config-secao').forEach(s => s.classList.remove('ativa'));
        btn.classList.add('ativo');
        document.getElementById(btn.dataset.secao).classList.add('ativa');
    });
});

// Toggle dias da semana
function toggleDia(checkbox) {
    const linha = checkbox.closest('.horario-linha');
    const campos = linha.querySelector('.horario-campos');
    const inputs = campos.querySelectorAll('input');
    if (checkbox.checked) {
        linha.classList.add('ativo');
        campos.classList.remove('desabilitado');
        inputs.forEach(i => { i.disabled = false; i.value = i === inputs[0] ? '08:00' : '18:00'; });
    } else {
        linha.classList.remove('ativo');
        campos.classList.add('desabilitado');
        inputs.forEach(i => { i.disabled = true; i.value = ''; });
    }
}

// Formulário de usuário (criar e editar)
function atualizarCampoCrp() {
    const papel = document.getElementById('usuario-permissao').value;
    document.getElementById('usuario-campo-crp').style.display = papel === 'RECEPCIONISTA' ? 'none' : '';
}

function abrirFormUsuario() {
    document.getElementById('usuario-form-titulo').textContent = 'Novo usuário';
    document.getElementById('usuario-id').value = '0';
    ['usuario-nome', 'usuario-email', 'usuario-telefone', 'usuario-crp', 'usuario-senha']
        .forEach(id => { document.getElementById(id).value = ''; });
    const papel = document.getElementById('usuario-permissao');
    papel.value = '';
    papel.disabled = false;
    document.getElementById('usuario-rotulo-senha').textContent = 'Senha provisória';
    document.getElementById('usuario-dica-senha').textContent = '';
    document.getElementById('btn-salvar-usuario').textContent = 'Criar usuário';
    atualizarCampoCrp();
    document.getElementById('form-novo-usuario').style.display = 'block';
}

function editarUsuario(u) {
    abrirFormUsuario();
    document.getElementById('usuario-form-titulo').textContent = 'Editar usuário';
    document.getElementById('usuario-id').value = u.id;
    document.getElementById('usuario-nome').value = u.nome;
    document.getElementById('usuario-email').value = u.email;
    document.getElementById('usuario-telefone').value = u.telefone;
    document.getElementById('usuario-crp').value = u.crp;
    const papel = document.getElementById('usuario-permissao');
    papel.value = u.permissao;
    papel.disabled = u.voce;   // ninguém muda o próprio papel
    document.getElementById('usuario-rotulo-senha').textContent = 'Redefinir senha';
    document.getElementById('usuario-dica-senha').textContent =
        'Deixe em branco para manter a senha atual. Se preencher, a pessoa passa a entrar com a nova senha.';
    document.getElementById('btn-salvar-usuario').textContent = 'Salvar alterações';
    atualizarCampoCrp();
    document.getElementById('form-novo-usuario').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function fecharFormUsuario() {
    document.getElementById('form-novo-usuario').style.display = 'none';
}

async function salvarUsuario() {
    const botao = document.getElementById('btn-salvar-usuario');
    const corpo = new URLSearchParams({
        id_usuario: document.getElementById('usuario-id').value,
        nome:       document.getElementById('usuario-nome').value,
        email:      document.getElementById('usuario-email').value,
        telefone:   document.getElementById('usuario-telefone').value,
        crp:        document.getElementById('usuario-crp').value,
        permissao:  document.getElementById('usuario-permissao').value,
        senha:      document.getElementById('usuario-senha').value,
    });
    botao.disabled = true;
    try {
        const resp = await fetch('/app/api/usuario_salvar.php', { method: 'POST', body: corpo });
        const json = await resp.json().catch(() => ({}));
        if (!resp.ok) { alert(json.erro || 'Não foi possível salvar.'); return; }
        location.reload();
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão ao salvar.');
    } finally {
        botao.disabled = false;
    }
}

async function alterarStatusUsuario(id, acao) {
    if (acao === 'desativar' && !confirm('Desativar este usuário? Ele perde o acesso ao sistema na hora, mas o histórico dele é mantido.')) return;
    try {
        const resp = await fetch('/app/api/usuario_status.php', {
            method: 'POST',
            body: new URLSearchParams({ id_usuario: id, acao })
        });
        const json = await resp.json().catch(() => ({}));
        if (!resp.ok) { alert(json.erro || 'Não foi possível alterar o usuário.'); return; }
        location.reload();
    } catch (erro) {
        console.error(erro);
        alert('Erro de conexão.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-salvar-valor');
    const campo = document.getElementById('valor-consulta-input');
    if (!botao || !campo) return;

    botao.addEventListener('click', async () => {
        const dados = new FormData();
        dados.append('valor', campo.value);

        try {
            const resp = await fetch('/app/api/config_valor_consulta.php', {
                method: 'POST',
                body: dados
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);

            // percentual de repasse (só existe para o admin)
            const percentual = document.getElementById('percentual-repasse-input');
            if (percentual) {
                const r = await fetch('/app/api/config_clinica.php', {
                    method: 'POST',
                    body: new URLSearchParams({ secao: 'repasse', percentual: percentual.value })
                });
                const json = await r.json().catch(() => ({}));
                if (!r.ok) { alert(json.erro || 'Não foi possível salvar o percentual.'); return; }
            }
            alert('Salvo!');
        } catch (erro) {
            console.error(erro);
            alert('Não foi possível salvar.');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-salvar-clinica');
    if (!botao) return;

    botao.addEventListener('click', async () => {
        const corpo = new URLSearchParams({
            secao:    'dados',
            nome:     document.getElementById('clinica-nome').value,
            cnpj:     document.getElementById('clinica-cnpj').value,
            endereco: document.getElementById('clinica-endereco').value,
            telefone: document.getElementById('clinica-telefone').value,
        });
        try {
            const resp = await fetch('/app/api/config_clinica.php', { method: 'POST', body: corpo });
            const json = await resp.json().catch(() => ({}));
            if (!resp.ok) { alert(json.erro || 'Não foi possível salvar.'); return; }
            alert('Dados da clínica salvos!');
        } catch (erro) {
            console.error(erro);
            alert('Erro de conexão ao salvar.');
        }
    });
});

// Dados pessoais
document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-salvar-perfil');
    if (!botao) return;

    botao.addEventListener('click', async () => {
        const crp = document.getElementById('perfil-crp');
        const corpo = new URLSearchParams({
            nome:     document.getElementById('perfil-nome').value,
            email:    document.getElementById('perfil-email').value,
            telefone: document.getElementById('perfil-telefone').value,
            crp:      crp ? crp.value : '',
        });
        botao.disabled = true;
        try {
            const resp = await fetch('/app/api/perfil_atualizar.php', { method: 'POST', body: corpo });
            const json = await resp.json().catch(() => ({}));
            if (!resp.ok) { alert(json.erro || 'Não foi possível salvar.'); return; }
            alert('Dados pessoais salvos!');
            location.reload();
        } catch (erro) {
            console.error(erro);
            alert('Erro de conexão ao salvar.');
        } finally {
            botao.disabled = false;
        }
    });
});


// Alterar a própria senha
document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-alterar-senha');
    if (!botao) return;

    botao.addEventListener('click', async () => {
        const atual = document.getElementById('senha-atual');
        const nova = document.getElementById('senha-nova');
        const confirmar = document.getElementById('senha-confirmar');

        botao.disabled = true;
        try {
            const resp = await fetch('/app/api/senha_alterar.php', {
                method: 'POST',
                body: new URLSearchParams({
                    senha_atual: atual.value,
                    nova_senha: nova.value,
                    confirmar_senha: confirmar.value,
                })
            });
            const json = await resp.json().catch(() => ({}));
            if (!resp.ok) { alert(json.erro || 'Não foi possível alterar a senha.'); return; }
            atual.value = nova.value = confirmar.value = '';
            alert('Senha alterada com sucesso!');
        } catch (erro) {
            console.error(erro);
            alert('Erro de conexão ao alterar a senha.');
        } finally {
            botao.disabled = false;
        }
    });
});


// Horários de atendimento
document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-salvar-horarios');
    if (!botao) return;

    botao.addEventListener('click', async () => {
        const nomes = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
        const corpo = new URLSearchParams({
            duracao:   document.getElementById('horario-duracao').value,
            intervalo: document.getElementById('horario-intervalo').value,
        });

        const linhas = document.querySelectorAll('#horarios-grid .horario-linha');
        for (let i = 0; i < linhas.length; i++) {
            const marcado = linhas[i].querySelector('input[type="checkbox"]').checked;
            if (!marcado) continue;
            const [inicio, fim] = linhas[i].querySelectorAll('input[type="time"]');
            if (!inicio.value || !fim.value) { alert(nomes[i] + ': informe o início e o fim.'); return; }
            if (inicio.value >= fim.value)   { alert(nomes[i] + ': o início precisa ser antes do fim.'); return; }
            corpo.append(`dias[${i + 1}][inicio]`, inicio.value);
            corpo.append(`dias[${i + 1}][fim]`, fim.value);
        }

        botao.disabled = true;
        try {
            const resp = await fetch('/app/api/horarios_salvar.php', { method: 'POST', body: corpo });
            const json = await resp.json().catch(() => ({}));
            if (!resp.ok) { alert(json.erro || 'Não foi possível salvar.'); return; }
            alert('Horários salvos!');
        } catch (erro) {
            console.error(erro);
            alert('Erro de conexão ao salvar.');
        } finally {
            botao.disabled = false;
        }
    });
});
