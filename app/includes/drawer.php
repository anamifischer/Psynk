<link rel="stylesheet" type="text/css" href="/app/css/drawer.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/app/css/drawer.css') ?>">
    
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
            <button class="drawer-aba" data-aba="anotacoes">Consultas</button>
            <button class="drawer-aba" data-aba="pagamentos">Pagamentos</button>
            <button class="btn" id="btn-imprimir" title="Imprimir prontuário">
                <img src="/imgs/icons/imprimir.png" alt="Imprimir prontuário" width="18" height="18">
            </button>
            <button class="btn" id="btn-consulta" title="Agendar Consulta" onclick="abrirModalConsulta(pacienteAbertoId)">
                <img src="/imgs/icons/add.png" alt="Agendar Consulta" width="18" height="18">
            </button>
        </div>

        <div class="drawer-conteudo">

            <!-- Aba Dados -->
            <div class="drawer-painel active" id="aba-dados">

                <div class="drawer-campo">
                    <span class="drawer-label">Nome completo</span>
                    <span class="drawer-valor" id="drawer-nome-dados"></span>
                    <input class="drawer-input" type="text" id="drawer-nome-input">
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">CPF</span>
                    <input class="drawer-input" type="text" id="drawer-cpf" placeholder="000.000.000-00" maxlength="14">
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Status</span>
                    <select class="drawer-select" id="drawer-status-select">
                        <option value="ativo">Ativo</option>
                        <option value="em_espera">Em espera</option>
                        <option value="pausado">Pausado</option>
                        <option value="encerrado">Encerrado</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Data de nascimento</span>
                    <input class="drawer-input" type="date" id="drawer-nascimento">
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Telefone</span>
                    <input class="drawer-input" type="tel" id="drawer-telefone" placeholder="(00) 00000-0000">
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Endereço</span>
                    <input class="drawer-input" type="text" id="drawer-endereco" placeholder="Rua, número — Cidade, UF">
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Psicólogo responsável</span>
                    <select class="drawer-select" id="drawer-psicologo-select"></select>
                </div>

                <div class="drawer-campo">
                    <span class="drawer-label">Observações gerais</span>
                    <textarea class="drawer-textarea" id="drawer-observacoes" placeholder="Observações sobre o paciente..."></textarea>
                </div>

                <div class="drawer-acoes">
                    <button class="btn btn-primary" onclick="salvarDadosPaciente()">Atualizar dados</button>
                </div>

            </div>

            <!-- Aba Consultas -->
            <div class="drawer-painel" id="aba-anotacoes"></div>

            <!-- Aba Pagamentos -->
            <div class="drawer-painel" id="aba-pagamentos"></div>
        </div>
    </div>

    <script>
        const papelUsuario = "<?= $papel ?? 'admin' ?>";
    </script>

</body>
</html>