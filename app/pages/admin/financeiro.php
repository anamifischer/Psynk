<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["ADMIN"]);
    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";

    $idClinica = (int) $_SESSION['id_clinica'];
        $psicologosDaClinica = buscarPsicologosDaClinica($conexao, $idClinica);

    // Filtro de psicólogo (só aceita quem é mesmo da clínica)
    $idPsicologoFiltro = null;
    if (isset($_GET['psicologo'])) {
        foreach ($psicologosDaClinica as $psicologo) {
            if ((int) $psicologo['idUsuario'] === (int) $_GET['psicologo']) {
                $idPsicologoFiltro = (int) $psicologo['idUsuario'];
            }
        }
    }

    $resumo = resumoFinanceiro($conexao, $idClinica, $idPsicologoFiltro);

    function mostrarLinhasPagamentos(array $pagamentos, string $rotulo, string $classeBadge): void {
        if (empty($pagamentos)) {
            echo '<tr><td colspan="5">Nenhum pagamento encontrado.</td></tr>';
            return;
        }
        foreach ($pagamentos as $pagamento) {
            $data = $pagamento['data_pagamento'] ?: $pagamento['data_vencimento'];

            echo '<tr class="linha-clicavel" onclick="abrirDrawer(' . (int) $pagamento['idpaciente'] . ')">';
echo '<td>' . htmlspecialchars($pagamento['nome_paciente']) . '</td>';
            echo '<td>' . htmlspecialchars($pagamento['nome_psicologo']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($data)) . '</td>';
            echo '<td>R$ ' . number_format($pagamento['valor'], 2, ',', '.') . '</td>';
            echo '<td><span class="badge ' . $classeBadge . '">' . $rotulo . '</span></td>';
            echo '</tr>';
        }
    }
?>

<!DOCTYPE html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro</title>

        <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
  

    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>
</head>
<body>

    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>

        <div class="filtro-secao">
            <div class="filtro-dropdown-wrapper">
                <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    Profissional
                </button>

                <div class="filtro-dropdown" id="filtro-dropdown">
                    <div class="filtro-grupo">
                        <p class="filtro-titulo">Psicólogo</p>
                        <div class="lista-chips" id="filtro-profissional">
                            <a class="chip <?= $idPsicologoFiltro === null ? 'active' : '' ?>"
                            href="financeiro.php">Todos</a>
                            <?php foreach ($psicologosDaClinica as $psicologo): ?>
                                <a class="chip <?= $idPsicologoFiltro === (int) $psicologo['idUsuario'] ? 'active' : '' ?>"
                                href="financeiro.php?psicologo=<?= (int) $psicologo['idUsuario'] ?>">
                                    <?= htmlspecialchars($psicologo['nome']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('listaReceita')">
                <div class="cartaoTitulo">Receita</div>
                <div class="cartaoValor" id="valor-receita">R$ <?= number_format($resumo['receita'], 2, ',', '.') ?></div>
                <div class="cartaoLegenda">total mês</div>
            </div>

            <div class="cartao" onclick="toggleList('listaPendentes')">
                <div class="cartaoTitulo">Pendente</div>
                <div class="cartaoValor" id="valor-pendente">R$ <?= number_format($resumo['pendente'], 2, ',', '.') ?></div>
                <div class="cartaoLegenda">pagamentos pendentes</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAtrasados')">
                <div class="cartaoValor" id="valor-atrasados"><?= (int) $resumo['atrasados'] ?></div>
                <div class="cartaoLegenda">pagamentos atrasados</div>
            </div>

            <div class="cartao" onclick="toggleList('listaVencendo')">
                <div class="cartaoValor" id="valor-vencendo"><?= (int) $resumo['vencendo_hoje'] ?></div>
                <div class="cartaoLegenda">pagamentos vencendo hoje</div>
            </div>
        </div>

        <div id="listaReceita" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Receita do mês</span>
            </div>
            <table class="data-table">
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
                    <?php mostrarLinhasPagamentos(buscarPagamentos($conexao, $idClinica, 'recebidos', $idPsicologoFiltro), 'Pago', 'badge-verde'); ?>
                </tbody>
            </table>
        </div>

        <div id="listaPendentes" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Pendentes</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPagamentos(buscarPagamentos($conexao, $idClinica, 'pendentes', $idPsicologoFiltro), 'Pendente', 'badge-laranja'); ?>
                </tbody>
            </table>
        </div>

        <div id="listaAtrasados" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Atrasados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPagamentos(buscarPagamentos($conexao, $idClinica, 'atrasados', $idPsicologoFiltro), 'Atrasado', 'badge-vermelho'); ?>
                </tbody>
            </table>
        </div>

        <div id="listaVencendo" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Vencendo Hoje</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPagamentos(buscarPagamentos($conexao, $idClinica, 'vencendo', $idPsicologoFiltro), 'Vence hoje', 'badge-laranja'); ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="/app/js/utils.js"></script>
    <script src="/app/js/financeiro.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>
    
</body>
</html>