
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Admin</title>

</head>


<body>
    <link rel="stylesheet" type="text/css" href="/app/css/menu.css">
    <script src="/app/js/menu.js"></script>

<nav class="barraLateral">

        <button class="btn-retrair" id="btn-retrair">
            <img src="/imgs/icons/menu.png">
        </button>
    
        <div class="barraLogo">
            <img class="logo-completo" src = "/imgs/logotipo-escuro.png">
            <img class="logo-icone" src="/imgs/logo.png">
        </div>

        <div class="barraItens">
            <a class="item" href="../../pages/recepcionista/dashboard.php">
                <img src="/imgs/icons/home.png">
                <span>Início</span>
            </a>
            <a class="item" href="../../pages/recepcionista/agenda.php">
                <img src="/imgs/icons/agenda.png">
                <span>Agenda</span>
            </a>
            <a class="item" href="../../pages/recepcionista/pacientes.php">
                <img src="/imgs/icons/pacientes.png">
                <span>Pacientes</span>
            </a>
            <a class="item" href="../../pages/recepcionista/financeiro.php">
                <img src="/imgs/icons/financeiro.png">
                <span>Financeiro</span>
            </a>
            <a class="item" href="../../pages/recepcionista/relatorios.php">
                <img src="/imgs/icons/relatorio.png">
                <span>Relatórios</span>
            </a>
            <a class="item" href="../../pages/recepcionista/configuracoes.php">
                <img src="/imgs/icons/configuracoes.png">
                <span>Configurações</span>
            </a>
        </div>

    <div class = "barraUser">

        <img src="/imgs/avatarExemplo.jpg">
        <p>Nome User</p>
        <a href="/logout.php">Sair</a>
    </div>

</nav>

</body>
</html>