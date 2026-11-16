<?php
    $nomesMeses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                   'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    $dataHoje = date('d') . ' de ' . $nomesMeses[(int) date('n') - 1] . ' de ' . date('Y');
?>

<link rel="stylesheet" type="text/css" href="/app/css/header.css">

    <header class="barraSuperior">
        <h1><?= $tituloPagina ?? 'Dashboard' ?></h1>
        <div class="barraSuperior-direita">
            <div class="search-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-paciente" class="search-input" placeholder="Buscar paciente...">
                <div class="search-resultados" id="search-resultados"></div>
            </div>
            <div class="notificacoes-wrapper">
                <button class="btn-notificacoes" id="btn-notificacoes" title="Notificações">
                    <img src="/imgs/icons/notificacoes.png" alt="Notificações" width="20" height="20">
                    <span class="selo-notificacoes" id="selo-notificacoes" style="display:none">0</span>
                </button>
                <div class="painel-notificacoes" id="painel-notificacoes">
                    <div class="painel-notificacoes-topo">
                        <span>Notificações</span>
                        <button id="btn-marcar-todas">Marcar todas como lidas</button>
                    </div>
                    <div id="lista-notificacoes"></div>
                </div>
            </div>
            <p class="barraSuperior-data"><?= $dataHoje ?></p>
        </div>
    </header>
<script src="/app/js/notificacoes.js"></script>
