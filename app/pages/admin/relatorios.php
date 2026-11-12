<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["ADMIN"]);

    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";
    $tituloPagina = 'Relatórios';

    $idClinica = (int) $_SESSION['id_clinica'];
        $psicologosDaClinica = buscarPsicologosDaClinica($conexao, $idClinica);

    // Filtro de profissional: null = clínica toda
    $idPsicologoFiltro = null;
    $nomeFiltro        = null;
    if (isset($_GET['psicologo'])) {
        foreach ($psicologosDaClinica as $psicologo) {
            if ((int) $psicologo['idUsuario'] === (int) $_GET['psicologo']) {
                $idPsicologoFiltro = (int) $psicologo['idUsuario'];
                $nomeFiltro        = $psicologo['nome'];
            }
        }
    }

    $resumo              = resumoFinanceiro($conexao, $idClinica, $idPsicologoFiltro);
    $pagamentosRecebidos = buscarPagamentos($conexao, $idClinica, 'recebidos', $idPsicologoFiltro);

    $consultasMes = resumoConsultasDoMes($conexao, $idClinica, $idPsicologoFiltro);
    $pacientes    = contarPacientesPorStatus($conexao, $idClinica, $idPsicologoFiltro);

    $aReceber = $resumo['pendente'] + $resumo['atrasado_valor'];
    $taxaCancelamento = $consultasMes['total'] > 0
        ? round($consultasMes['canceladas'] / $consultasMes['total'] * 100)
        : 0;
    $percentualRepasse = buscarPercentualRepasse($conexao, $idClinica);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Relatórios</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" type="text/css" href="/app/css/relatorios.css">
  
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>

</head>
<body>
    <div class="main">

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>
        
        <div class="grafico">
            <div class="filtro-dropdown-wrapper">
                <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    <?= htmlspecialchars($nomeFiltro ?? 'Toda a clínica') ?>
                </button>

                <div class="filtro-dropdown" id="filtro-dropdown">
                    <div class="filtro-grupo">
                        <p class="filtro-titulo">Profissional</p>
                        <div class="lista-chips">
                            <a class="chip <?= $idPsicologoFiltro === null ? 'active' : '' ?>"
                            href="relatorios.php">Toda a clínica</a>
                            <?php foreach ($psicologosDaClinica as $psicologo): ?>
                                <a class="chip <?= $idPsicologoFiltro === (int) $psicologo['idUsuario'] ? 'active' : '' ?>"
                                href="relatorios.php?psicologo=<?= (int) $psicologo['idUsuario'] ?>">
                                    <?= htmlspecialchars($psicologo['nome']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
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
            <h1><?= $nomeFiltro ? 'Faturamento — ' . htmlspecialchars($nomeFiltro) : 'Faturamento Geral' ?></h1>
            <canvas id="graficoReceita" data-psicologo="<?= (int) $idPsicologoFiltro ?>"></canvas>
        </div>

        <h2 class="grupo-titulo">Financeiro</h2>
        <div class="cartoes">
            <div class="cartao" onclick="toggleList('faturamentoClinica')">
                <div class="cartaoTitulo"><?= $nomeFiltro ? 'Faturamento de ' . htmlspecialchars($nomeFiltro) : 'Faturamento Clínica' ?></div>
                <div class="cartaoValor">R$ <?= number_format($resumo['receita'], 2, ',', '.') ?></div>
                <div class="cartaoLegenda">mês</div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">A receber</div>
                <div class="cartaoValor">R$ <?= number_format($aReceber, 2, ',', '.') ?></div>
                <div class="cartaoLegenda">pendentes + atrasados</div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">Percentual definido sobre consultas</div>
                <div class="cartaoValor"><?= rtrim(rtrim(number_format($percentualRepasse, 2, ',', ''), '0'), ',') ?>%</div>
            </div>
        </div>

        <div class="collapse-list" id="faturamentoClinica">
            <table class="faturamento-tabela">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Data do pagamento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagamentosRecebidos)): ?>
                        <tr><td colspan="5">Nenhum pagamento recebido neste mês.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($pagamentosRecebidos as $pagamento): ?>
                        <tr class="faturamento-linha linha-clicavel"
                            onclick="abrirDrawer(<?= (int) $pagamento['idpaciente'] ?>)">
                            <td><?= htmlspecialchars($pagamento['nome_paciente']) ?></td>
                            <td><?= htmlspecialchars($pagamento['nome_psicologo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($pagamento['data_pagamento'])) ?></td>
                            <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                            <td><span class="badge badge-green">Pago</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="grupo-titulo">Atendimento</h2>
        <div class="cartoes">
            <div class="cartao">
                <div class="cartaoTitulo">Consultas concluídas</div>
                <div class="cartaoValor"><?= (int) $consultasMes['concluidas'] ?></div>
                <div class="cartaoLegenda">neste mês</div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">Taxa de cancelamento</div>
                <div class="cartaoValor"><?= $taxaCancelamento ?>%</div>
                <div class="cartaoLegenda"><?= (int) $consultasMes['canceladas'] ?> de <?= (int) $consultasMes['total'] ?> consultas no mês</div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">Pacientes ativos</div>
                <div class="cartaoValor"><?= (int) $pacientes['ativo'] ?></div>
                <div class="cartaoLegenda">em acompanhamento</div>
            </div>
        </div>
    </div>

    <script src="/app/js/relatorios.js"></script>
    <script src="/app/js/utils.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>


</body>
</html>