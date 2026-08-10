<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/agenda.css">

    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>

</head>
<body>

    <div class="calendario">
        <div class="calendario-header">
            <button id="mes-anterior">‹</button>
            <h3 id="mes-atual"></h3>
            <button id="mes-proximo">›</button>
        </div>

        <div class="dias-semana">
            <span>DOM</span>
            <span>SEG</span>
            <span>TER</span>
            <span>QUA</span>
            <span>QUI</span>
            <span>SEX</span>
            <span>SÁB</span>
        </div>

        <div id="dias" class="dias"></div>
    </div>

    <div class="painel-detalhe" id="painel-detalhe">
        <div class="painel-header">
            <h3>Detalhes</h3>
            <button id="fechar-painel">✕</button>
        </div>
        <div class="painel-conteudo" id="painel-conteudo">

        </div>
    </div>
    

    <script src="/app/js/agenda.js"></script>
</body>
</html>