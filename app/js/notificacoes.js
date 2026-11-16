// Sino de notificações do cabeçalho
(function () {
    const botao = document.getElementById('btn-notificacoes');
    const painel = document.getElementById('painel-notificacoes');
    if (!botao || !painel) return;

    const selo = document.getElementById('selo-notificacoes');
    const lista = document.getElementById('lista-notificacoes');

    function escapar(texto) {
        const div = document.createElement('div');
        div.textContent = texto == null ? '' : String(texto);
        return div.innerHTML;
    }

    async function carregar() {
        try {
            const resp = await fetch('/app/api/notificacoes_listar.php');
            if (!resp.ok) return;
            const dados = await resp.json();

            selo.textContent = dados.naoLidas > 99 ? '99+' : dados.naoLidas;
            selo.style.display = dados.naoLidas > 0 ? '' : 'none';

            if (!dados.itens.length) {
                lista.innerHTML = '<p class="notificacoes-vazio">Nenhuma notificação.</p>';
                return;
            }
            lista.innerHTML = dados.itens.map(n => `
                <div class="notificacao-item ${n.lida ? '' : 'nao-lida'} ${n.idPaciente ? 'clicavel' : ''}"
                     data-id="${n.id}" data-paciente="${n.idPaciente || ''}">
                    <span class="notificacao-msg">${escapar(n.mensagem)}</span>
                    <span class="notificacao-data">${n.quando}</span>
                </div>
            `).join('');
        } catch (erro) {
            console.error('Erro ao carregar notificações:', erro);
        }
    }

    async function marcar(id) {
        try {
            await fetch('/app/api/notificacoes_marcar.php', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
        } catch (erro) {
            console.error(erro);
        }
    }

    botao.addEventListener('click', (e) => {
        e.stopPropagation();
        painel.classList.toggle('aberto');
        if (painel.classList.contains('aberto')) carregar();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.notificacoes-wrapper')) painel.classList.remove('aberto');
    });

    lista.addEventListener('click', async (e) => {
        const item = e.target.closest('.notificacao-item');
        if (!item) return;
        await marcar(item.dataset.id);
        const idPaciente = item.dataset.paciente;
        painel.classList.remove('aberto');
        if (idPaciente && typeof abrirDrawer === 'function' && document.getElementById('drawer-paciente')) {
            abrirDrawer(Number(idPaciente));
        }
        carregar();
    });

    document.getElementById('btn-marcar-todas').addEventListener('click', async () => {
        await marcar('todas');
        carregar();
    });

    carregar();
    setInterval(carregar, 60000);
})();
