<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["PSICOLOGO"]);

    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";
    $tituloPagina = 'Relatórios';

    $idClinica = (int) $_SESSION['id_clinica'];

    // O psicólogo só vê os próprios números
    $idPsicologoFiltro = (int) $_SESSION['id'];
    $nomeFiltro        = null;

    // Mês dos cards (padrão: mês atual)
    $anoRef = (int) ($_GET['ano'] ?? date('Y'));
    $mesRef = (int) ($_GET['mes'] ?? date('n'));
    $mesCompleto = normalizarMes(sprintf('%04d-%02d', $anoRef, $mesRef));
    $anoRef = (int) substr($mesCompleto, 0, 4);
    $mesRef = (int) substr($mesCompleto, 5, 2);
    $nomesMeses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                   'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    $rotuloMes = $nomesMeses[$mesRef] . ' de ' . $anoRef;

    $resumo              = resumoFinanceiro($conexao, $idClinica, $idPsicologoFiltro, $mesCompleto);
    $pagamentosRecebidos = buscarPagamentos($conexao, $idClinica, 'recebidos', $idPsicologoFiltro, $mesCompleto);

    $consultasMes = resumoConsultasDoMes($conexao, $idClinica, $idPsicologoFiltro, $mesCompleto);
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
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuPsicologo.php");
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
            <h1>Meu faturamento</h1>
            <canvas id="graficoReceita" data-psicologo="<?= (int) $idPsicologoFiltro ?>"></canvas>
        </div>

        <form class="filtro-mes" method="get">
            <?php if ($idPsicologoFiltro !== null && false): ?>
                <input type="hidden" name="psicologo" value="<?= (int) $idPsicologoFiltro ?>">
            <?php endif; ?>
            <span>Período dos cards:</span>
            <select name="mes" onchange="this.form.submit()">
                <?php foreach ($nomesMeses as $numero => $nome): ?>
                    <option value="<?= $numero ?>" <?= $numero === $mesRef ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
            <select name="ano" onchange="this.form.submit()">
                <?php for ($a = (int) date('Y'); $a >= (int) date('Y') - 3; $a--): ?>
                    <option value="<?= $a ?>" <?= $a === $anoRef ? 'selected' : '' ?>><?= $a ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <h2 class="grupo-titulo">Financeiro</h2>
        <div class="cartoes">
            <div class="cartao" onclick="toggleList('faturamentoClinica')">
                <div class="cartaoTitulo">Meu faturamento</div>
                <div class="cartaoValor">R$ <?= number_format($resumo['receita'], 2, ',', '.') ?></div>
                <div class="cartaoLegenda"><?= $rotuloMes ?></div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">A receber</div>
                <div class="cartaoValor">R$ <?= number_format($aReceber, 2, ',', '.') ?></div>
                <div class="cartaoLegenda">pendentes + atrasados (atual)</div>
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
                                                <th>Data do pagamento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagamentosRecebidos)): ?>
                        <tr><td colspan="4">Nenhum pagamento recebido neste período.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($pagamentosRecebidos as $pagamento): ?>
                        <tr class="faturamento-linha linha-clicavel"
                            onclick="abrirDrawer(<?= (int) $pagamento['idpaciente'] ?>)">
                            <td><?= htmlspecialchars($pagamento['nome_paciente']) ?></td>
                            <td><?= date('d/m/Y', strtotime($pagamento['data_pagamento'])) ?></td>
                            <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                            <td><span class="badge badge-verde">Pago</span></td>
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
                <div class="cartaoLegenda"><?= $rotuloMes ?></div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">Taxa de cancelamento</div>
                <div class="cartaoValor"><?= $taxaCancelamento ?>%</div>
                <div class="cartaoLegenda"><?= (int) $consultasMes['canceladas'] ?> de <?= (int) $consultasMes['total'] ?> consultas em <?= $rotuloMes ?></div>
            </div>

            <div class="cartao">
                <div class="cartaoTitulo">Pacientes ativos</div>
                <div class="cartaoValor"><?= (int) $pacientes['ativo'] ?></div>
                <div class="cartaoLegenda">em acompanhamento (atual)</div>
            </div>
        </div>
    </div>

    <script src="/app/js/relatorios.js"></script>
    <script src="/app/js/utils.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>


</body>
</html>