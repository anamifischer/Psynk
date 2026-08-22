<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda — Psynk</title>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/agenda.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>
    <?php $papel = 'recepcionista'; ?>

</head>
<body>

<div class="main">

    <aside class="painel-filtros">

        
        <div class="filtro-secao">
            <div class="alternador-view">
                <button class="botao-view ativo" data-view="mes">Mês</button>
                <button class="botao-view" data-view="semana">Semana</button>
                <button class="botao-view" data-view="ano">Ano</button>
            </div>
        </div>

        
        <div class="filtro-secao">
            <p class="filtro-titulo">Psicólogos</p>
            <div class="lista-psicologos" id="lista-psicologos">
                
            </div>
        </div>

       
        <div class="filtro-secao">
            <p class="filtro-titulo">Status</p>
            <div class="legenda-status">
                <div class="legenda-item">
                    <span class="legenda-bolinha confirmado"></span>
                    <span>Confirmado</span>
                </div>
                <div class="legenda-item">
                    <span class="legenda-bolinha pendente"></span>
                    <span>Pendente</span>
                </div>
                <div class="legenda-item">
                    <span class="legenda-bolinha cancelado"></span>
                    <span>Cancelado</span>
                </div>
            </div>
        </div>

    </aside>

   
    <div class="area-principal">

        
        <header class="barra-topo">
            <div class="topo-esquerda">
                <button class="botao-nav" id="btn-anterior">‹</button>
                <h2 class="periodo-atual" id="periodo-atual"></h2>
                <button class="botao-nav" id="btn-proximo">›</button>
            </div>
            <button class="btn" id="btn-consulta" title="Agendar Consulta" onclick="abrirModalConsulta()">
                <img src="/imgs/icons/add.png" alt="Agendar Consulta" width="18" height="18">
            </button>
        </header>

        
        <div class="cabecalho-semana" id="cabecalho-semana">
            <span>Dom</span><span>Seg</span><span>Ter</span>
            <span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
        </div>

    
        <div class="grade-calendario" id="grade-calendario"></div>

    </div>

</div>
<script src="/app/js/utils.js"></script>
<script src="/app/js/agenda.js"></script>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>
</body>
</html>