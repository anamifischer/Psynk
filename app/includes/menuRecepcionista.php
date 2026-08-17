
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

    <div class ="barraItens">
        <div class="item">
            <img src = "/imgs/icons/home.png">
            <a href="../../pages/recepcionista/dashboard.php">Início</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/agenda.png">
            <a href="../../pages/recepcionista/agenda.php">Agenda</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/pacientes.png">
            <a href="../../pages/recepcionista/pacientes.php">Pacientes</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/financeiro.png">
            <a href="../../pages/recepcionista/financeiro.php">Financeiro</a>
        </div>
        <div class="item">
            <img src = "/imgs/icons/configuracoes.png">
            <a href="../../pages/recepcionista/configuracoes.php">Configurações</a>
        </div>
    </div>

    <div class = "barraUser">

        <img src="/imgs/avatarExemplo.jpg">
        <p>Nome User</p>
        <a href="/logout.php">Sair</a>
    </div>

</nav>

</body>
</html>