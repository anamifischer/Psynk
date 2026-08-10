<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" type="text/css" href="/css/menu.css">
    <link rel="stylesheet" type="text/css" href="/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/css/dashboard.css">


    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/includes/menuAdmin.php");
    ?>


</head>
<body>
 
    <div class="main">
        <header class="barraSuperior">
            <h1>Visão Geral</h1>
            <p>07 de Agosto de 2026</p>
        </header>

        <h1 class="cumprimento"> Olá Frederico </h1>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('Lista Consultas')">
                <div class="cartaoTitulo">Consultas Hoje</div>
                <div class="cartaoValor">8</div>
                <div class="cartaoLegenda">confirmadas</div>
            </div>

            <div class="cartao" onclick="toggleList('listaConsultas')">
                <div class="cartaoTitulo">Consultas Hoje</div>
                <div class="cartaoValor">8</div>
                <div class="cartaoLegenda">confirmadas</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAguardaConfirmação')">
                <div class="cartaoTitulo">Aguardando Confirmação</div>
                <div class="cartaoValor">2</div>
            </div>

            <div class="calendario">
                <div class="diasSemana">
                    <div>Dom</div>
                    <div>Segunda</div>
                    <div>Ter</div>
                    <div>Quar</div>
                    <div>Quin</div>
                    <div>Sex/div>
                    <div>Sab</div>
                </div>  

                
            </div>
    
</body>
</html>