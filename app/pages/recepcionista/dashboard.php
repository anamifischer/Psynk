<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["RECEPCIONISTA"]);
    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";
    $tituloPagina = 'Dashboard';

    $idClinica = (int) $_SESSION['id_clinica'];

    // A recepção enxerga a clínica toda
    $consultasHoje       = buscarConsultasDeHoje($conexao, $idClinica);
    $consultasAguardando = buscarConsultasAguardandoConfirmacao($conexao, $idClinica);
    $vencendoHoje        = buscarPagamentos($conexao, $idClinica, 'vencendo');
    $atrasados           = buscarPagamentos($conexao, $idClinica, 'atrasados');
    $totalAtrasado       = array_sum(array_column($atrasados, 'valor'));

    // Aviso: próxima consulta de hoje que ainda não começou
    $proximaConsulta = null;
    foreach ($consultasHoje as $c) {
        if (in_array($c['status'], ['aguarda data', 'atrasada'], true) && $c['hora_inicio'] >= date('H:i:s')) {
            $proximaConsulta = $c;
            break;
        }
    }

    // abre o drawer do paciente já na aba de pagamentos
    function botaoReceber(int $idPaciente): string {
        return '<button class="btn btn-sm btn-secondary" onclick="abrirDrawer(' . $idPaciente . ').then(() => irParaAba(\'pagamentos\'))">Receber</button>';
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Dashboard</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" type="text/css" href="/app/css/dashboard.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>
</head>
<body>

    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>
        <h1 class="cumprimento">Olá, <?= htmlspecialchars($_SESSION['nome']) ?></h1>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('listaConsultas')">
                <div class="cartaoTitulo">Consultas Hoje</div>
                <div class="cartaoValor"><?= count($consultasHoje) ?></div>
                <div class="cartaoLegenda">na clínica</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAguardaConfirmacao')">
                <div class="cartaoTitulo">Aguardando Confirmação</div>
                <div class="cartaoValor"><?= count($consultasAguardando) ?></div>
                <div class="cartaoLegenda">para confirmar com o paciente</div>
            </div>

            <div class="cartao" onclick="toggleList('listaVencendo')">
                <div class="cartaoTitulo">Vencendo Hoje</div>
                <div class="cartaoValor"><?= count($vencendoHoje) ?></div>
                <div class="cartaoLegenda">pagamentos a receber</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAtrasados')">
                <div class="cartaoTitulo">Atrasados</div>
                <div class="cartaoValor"><?= count($atrasados) ?></div>
                <div class="cartaoLegenda">R$ <?= number_format($totalAtrasado, 2, ',', '.') ?> para cobrar</div>
            </div>
        </div>

        <div class="calendario-e-avisos">
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
            <div class="cartao-aviso">
                <div class="cartaoTitulo">Avisos</div>
                <?php if ($proximaConsulta): ?>
                    <div class="cartaoLegenda">
                        Próxima consulta: <?= htmlspecialchars($proximaConsulta['nome_paciente']) ?>
                        às <?= formatarHora($proximaConsulta['hora_inicio']) ?>
                        (<?= htmlspecialchars($proximaConsulta['nome_psicologo']) ?>)
                    </div>
                    <div class="link"><a href="agenda.php">Abrir agenda 🡪 </a></div>
                <?php else: ?>
                    <div class="cartaoLegenda">Nenhuma consulta pendente para o resto do dia.</div>
                    <div class="link"><a href="agenda.php">Abrir agenda 🡪 </a></div>
                <?php endif; ?>
            </div> 
        </div>

        <div id="listaConsultas" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Consultas de hoje</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Horário</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultasHoje)): ?>
                        <tr><td colspan="4">Nenhuma consulta hoje.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($consultasHoje as $consulta): ?>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer(<?= (int) $consulta['id_paciente'] ?>)"><?= htmlspecialchars($consulta['nome_paciente']) ?></span></td>
                            <td><?= htmlspecialchars($consulta['nome_psicologo']) ?></td>
                            <td><?= formatarHora($consulta['hora_inicio']) ?></td>
                            <td><?= situacaoDaConsulta($consulta) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="listaAguardaConfirmacao" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Aguardando confirmação</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Telefone</th>
                        <th>Psicólogo(a)</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultasAguardando)): ?>
                        <tr><td colspan="6">Nenhuma consulta aguardando confirmação.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($consultasAguardando as $consulta): ?>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer(<?= (int) $consulta['id_paciente'] ?>)"><?= htmlspecialchars($consulta['nome_paciente']) ?></span></td>
                            <td><?= htmlspecialchars($consulta['telefone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($consulta['nome_psicologo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($consulta['data'])) ?></td>
                            <td><?= formatarHora($consulta['hora_inicio']) ?></td>
                            <td><button class="btn btn-sm btn-secondary" onclick="confirmarConsulta(<?= (int) $consulta['idconsulta'] ?>)">Confirmar</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="listaVencendo" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos vencendo hoje</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Telefone</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vencendoHoje)): ?>
                        <tr><td colspan="4">Nenhum pagamento vencendo hoje.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($vencendoHoje as $pagamento): ?>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer(<?= (int) $pagamento['idpaciente'] ?>)"><?= htmlspecialchars($pagamento['nome_paciente']) ?></span></td>
                            <td><?= htmlspecialchars($pagamento['telefone'] ?? '') ?></td>
                            <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                            <td><?= botaoReceber((int) $pagamento['idpaciente']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="listaAtrasados" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos atrasados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Telefone</th>
                        <th>Vencimento</th>
                        <th>Atraso</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($atrasados)): ?>
                        <tr><td colspan="6">Nenhum pagamento atrasado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($atrasados as $pagamento):
                        $diasAtraso = (int) floor((strtotime(date('Y-m-d')) - strtotime($pagamento['data_vencimento'])) / 86400);
                    ?>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer(<?= (int) $pagamento['idpaciente'] ?>)"><?= htmlspecialchars($pagamento['nome_paciente']) ?></span></td>
                            <td><?= htmlspecialchars($pagamento['telefone'] ?? '') ?></td>
                            <td><?= date('d/m/Y', strtotime($pagamento['data_vencimento'])) ?></td>
                            <td><?= $diasAtraso ?> <?= $diasAtraso === 1 ? 'dia' : 'dias' ?></td>
                            <td>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></td>
                            <td><?= botaoReceber((int) $pagamento['idpaciente']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script src="/app/js/utils.js"></script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>

</body>
</html>