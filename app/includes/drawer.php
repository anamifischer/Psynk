<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drawer</title>
</head>
<body>

    <link rel="stylesheet" type="text/css" href="/app/css/drawer.css">
    
    <div class="drawer-overlay" id="drawer-overlay" onclick="fecharDrawer()"></div>

    <div class="drawer" id="drawer-paciente">
        <div class="drawer-header">
            <div class="drawer-info">
                <h2 class="drawer-nome" id="drawer-nome">Ana Lima</h2>
                <span class="badge badge-verde" id="drawer-status">Ativo</span>
            </div>
            <button class="drawer-fechar" onclick="fecharDrawer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="drawer-abas">
            <button class="drawer-aba active" data-aba="dados">Dados</button>
            <button class="drawer-aba" data-aba="anotacoes">Anotações</button>
            <button class="drawer-aba" data-aba="pagamentos">Pagamentos</button>
            <button class="btn-imprimir" id="btn-imprimir" title="Imprimir prontuário">
                <img src="/imgs/icons/imprimir.png" alt="Imprimir prontuário" width="18" height="18">
            </button>
        </div>

        <div class="drawer-conteudo">

            <!-- Aba Dados -->
            <div class="drawer-painel active" id="aba-dados">
                <div class="drawer-campo">
                    <span class="drawer-label">Data de nascimento</span>
                    <span class="drawer-valor" id="drawer-nascimento">12/03/1990</span>
                </div>
                <div class="drawer-campo">
                    <span class="drawer-label">Idade</span>
                    <span class="drawer-valor" id="drawer-idade">36 anos</span>
                </div>
                <div class="drawer-campo">
                    <span class="drawer-label">Psicólogo responsável</span>
                    <span class="drawer-valor" id="drawer-psicologo">Dr. Carlos Souza</span>
                </div>
            </div>

            <!-- Aba Anotações -->
            <div class="drawer-painel" id="aba-anotacoes">
                <div class="sessao-item">
                    <div class="sessao-header">
                        <span class="sessao-data">05/08/2026</span>
                        <span class="sessao-hora">14:00</span>
                    </div>
                    <p class="sessao-anotacao">Paciente relatou melhora significativa na qualidade do sono. Continuamos trabalhando técnicas de respiração.</p>
                </div>
                <div class="sessao-item">
                    <div class="sessao-header">
                        <span class="sessao-data">29/07/2026</span>
                        <span class="sessao-hora">14:00</span>
                    </div>
                    <p class="sessao-anotacao">Sessão focada em reestruturação cognitiva. Paciente demonstrou boa adesão às técnicas propostas.</p>
                </div>
            </div>

            <!-- Aba Pagamentos -->
            <div class="drawer-painel" id="aba-pagamentos">
                <div class="pagamento-item">
                    <div class="pagamento-info">
                        <span class="pagamento-data">05/08/2026</span>
                        <span class="badge badge-verde">Pago</span>
                    </div>
                    <span class="pagamento-valor">R$ 180,00</span>
                </div>
                <div class="pagamento-item">
                    <div class="pagamento-info">
                        <span class="pagamento-data">29/07/2026</span>
                        <span class="badge badge-verde">Pago</span>
                    </div>
                    <span class="pagamento-valor">R$ 180,00</span>
                </div>
                <div class="pagamento-item">
                    <div class="pagamento-info">
                        <span class="pagamento-data">22/07/2026</span>
                        <span class="badge badge-vermelho">Atrasado</span>
                    </div>
                    <span class="pagamento-valor">R$ 180,00</span>
                </div>
            </div>

            <!-- Drawer anotações -->
            <div class="modal-overlay" id="modal-overlay" onclick="fecharModal()"></div>

            <div class="modal" id="modal-anotacao">
                <div class="modal-header">
                    <div>
                        <h3 class="modal-titulo">Lançar Anotação</h3>
                        <p class="modal-subtitulo" id="modal-subtitulo"></p>
                    </div>
                    <button class="drawer-fechar" onclick="fecharModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-info">
                    <div class="drawer-campo">
                        <span class="drawer-label">Paciente</span>
                        <span class="drawer-valor" id="modal-paciente"></span>
                    </div>
                    <div class="drawer-campo">
                        <span class="drawer-label">Data da consulta</span>
                        <span class="drawer-valor" id="modal-data"></span>
                    </div>
                    <div class="drawer-campo">
                        <span class="drawer-label">Psicólogo</span>
                        <span class="drawer-valor" id="modal-psicologo"></span>
                    </div>
                </div>
                <textarea class="modal-textarea" id="modal-textarea" placeholder="Digite a anotação clínica..."></textarea>
                <div class="modal-rodape">
                    <button class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                    <button class="btn btn-primary" onclick="salvarAnotacao()">Salvar anotação</button>
                </div>
            </div>

        </div>
    </div>

</body>
</html>