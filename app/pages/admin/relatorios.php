<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/relatorios.css">
  
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>

</head>
<body>

    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>
        <div class="grafico">
            <div class="ano-dropdown-wrapper">
                <button class="btn btn-secondary" id="btn-ano" onclick="toggleAnoDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="btn-ano-label">2026</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <div class="ano-dropdown" id="ano-dropdown">
                    <button class="ano-opcao" data-ano="2024">2024</button>
                    <button class="ano-opcao" data-ano="2025">2025</button>
                    <button class="ano-opcao active" data-ano="2026">2026</button>
                </div>
            </div>
        </div>

        <div class="grafico-container">
            <canvas id="graficoReceita"></canvas>
        </div>


    </div>

    <script src="/app/js/relatorios.js"></script>
    <script src="/app/js/utils.js"></script>


</body>
</html>