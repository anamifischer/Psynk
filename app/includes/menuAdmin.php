<?php
    // Iniciais do usuário logado (primeiras letras dos dois primeiros nomes)
    $iniciaisMenu = '';
    foreach (array_slice(preg_split('/\s+/', trim($_SESSION['nome'] ?? '')), 0, 2) as $parteNome) {
        $iniciaisMenu .= mb_strtoupper(mb_substr($parteNome, 0, 1));
    }
?>
   <link rel="stylesheet" type="text/css" href="/app/css/menu.css">
   <link rel="icon" type="image/png" href="/imgs/logo.png">
    <script src="/app/js/menu.js"></script>

    <nav class="barraLateral">

        <button class="btn-retrair" id="btn-retrair">
            <img src="/imgs/icons/menu.png">
        </button>


        <div class="barraLogo">
            <img class="logo-completo" src = "/imgs/logotipo-claro.png">
            <img class="logo-icone" src="/imgs/logo.png">
        </div>

        <div class="barraItens">
            <a class="item" href="/app/pages/admin/dashboard.php">
                <img src="/imgs/icons/home.png">
                <span>Início</span>
            </a>
            <a class="item" href="/app/pages/admin/agenda.php">
                <img src="/imgs/icons/agenda.png">
                <span>Agenda</span>
            </a>
            <a class="item" href="/app/pages/admin/pacientes.php">
                <img src="/imgs/icons/pacientes.png">
                <span>Pacientes</span>
            </a>
            <a class="item" href="/app/pages/admin/financeiro.php">
                <img src="/imgs/icons/financeiro.png">
                <span>Financeiro</span>
            </a>
            <a class="item" href="/app/pages/admin/relatorios.php">
                <img src="/imgs/icons/relatorio.png">
                <span>Relatórios</span>
            </a>
            <a class="item" href="/app/pages/admin/configuracoes.php">
                <img src="/imgs/icons/configuracoes.png">
                <span>Configurações</span>
            </a>
        </div>

        <div class = "barraUser">
            <div class="avatar-iniciais"><?= htmlspecialchars($iniciaisMenu) ?></div>
            <section id=user-info>
                <p><?= htmlspecialchars($_SESSION['nome']) ?></p>
                <a href="/config/logout.php">Sair</a>
            </section>
        </div>

    </nav>