<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página inicial</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" type="text/css" href="public/index.css">
    <link rel="stylesheet" type="text/css" href="app/css/variables.css">

</head>
<body onload="mostrarPagina('inicio')">

    <div class="main">
        <div class="imgPsySink">
            <img src="imgs/imgIndex.png" alt="Psicólogo(a) com prancheta e logotipo PsySink">
        </div>

        <div class="conteudo">
            <nav>
                <img src="imgs/logotipo-escuro.png" alt="logotipo PsySink">
                <button onclick="mostrarPagina('inicio')">Início</button>
                <button onclick="mostrarPagina('sobre')">Sobre Nós</button>
                <button onclick="mostrarPagina('funcionalidades')">Funcionalidades</button>
                <button onclick="mostrarPagina('planos')">Planos</button>
                <button onclick="window.location.href='/public/login.php'">Login

                </button>
            </nav>

            <main id="conteudo">

            </main>
        </div>
    </div>
        
    <script src="public/index.js"></script>

</body>
</html>