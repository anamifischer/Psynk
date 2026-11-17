<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["PSICOLOGO"]);
    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";
    $tituloPagina = 'Financeiro';

    $idClinica = (int) $_SESSION['id_clinica'];
    // O psicólogo só vê o próprio financeiro
    $idPsicologoFiltro = (int) $_SESSION['id'];

    $resumo = resumoFinanceiro($conexao, $idClinica, $idPsicologoFiltro);

    function mostrarLinhasPagamentos(array $pagamentos, string $rotulo, string $classeBadge, bool $avisarVazio = true): void {
        if (empty($pagamentos)) {
            if (!$avisarVazio) return;
            echo '<tr><td colspan="4">Nenhum pagamento encontrado.</td></tr>';
            return;
        }
        foreach ($pagamentos as $pagamento) {
            $data = $pagamento['data_pagamento'] ?: $pagamento['data_vencimento'];

            echo '<tr class="linha-clicavel" onclick="abrirDrawer(' . (int) $pagamento['idpaciente'] . ')">';
            echo '<td>' . htmlspecialchars($pagamento['nome_paciente']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($data)) . '</td>';
            echo '<td>R$ ' . number_format($pagamento['valor'], 2, ',', '.') . '</td>';
            echo '<td><span class="badge ' . $classeBadge . '">' . $rotulo . '</span></td>';
            echo '</tr>';
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Financeiro</title>

        <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
  

    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuPsicologo.php");
    ?>
</head>
<body>

    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('listaPendentes')">
                <div class="cartaoTitulo">Pendente</div>
                <div class="cartaoValor" id="valor-pendente">R$ <?= number_format($resumo['pendente'] + $resumo['atrasado_valor'], 2, ',', '.') ?></div>
                <div class="cartaoLegenda">pendentes + atrasados</div>
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

        <div id="listaPendentes" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Pendentes e Atrasados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // atrasados primeiro (vencimento mais antigo), depois os pendentes
                        $atrasados = buscarPagamentos($conexao, $idClinica, 'atrasados', $idPsicologoFiltro);
                        $pendentes = buscarPagamentos($conexao, $idClinica, 'pendentes', $idPsicologoFiltro);
                        if (empty($atrasados) && empty($pendentes)) {
                            mostrarLinhasPagamentos([], '', '');
                        }
                        mostrarLinhasPagamentos($atrasados, 'Atrasado', 'badge-vermelho', false);
                        mostrarLinhasPagamentos($pendentes, 'Pendente', 'badge-laranja', false);
                    ?>
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>
    
</body>
</html>