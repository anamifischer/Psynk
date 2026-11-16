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

    <div class ="barraItens">
        <div class="item">
            <img src = "/imgs/icons/home.png">
            <a href="/app/pages/recepcionista/dashboard.php">Início</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/agenda.png">
            <a href="/app/pages/recepcionista/agenda.php">Agenda</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/pacientes.png">
            <a href="/app/pages/recepcionista/pacientes.php">Pacientes</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/financeiro.png">
            <a href="/app/pages/recepcionista/financeiro.php">Financeiro</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/configuracoes.png">
            <a href="/app/pages/recepcionista/configuracoes.php">Configurações</a>
        </div>
    </div>

    <div class = "barraUser">
        <div class="avatar-iniciais"><?= htmlspecialchars($iniciaisMenu) ?></div>
        <section id=user-info>
            <p><?= htmlspecialchars($_SESSION['nome']) ?></p>
            <a href="/config/logout.php">Sair</a>
        </section>
    </div>

</nav>
