<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["ADMIN"]);
    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";

    $idClinica = (int) $_SESSION['id_clinica'];

    $psicologosDaClinica = buscarPsicologosDaClinica($conexao, $idClinica);

    // Filtro de psicólogo
    $idPsicologoFiltro = null;
    if (isset($_GET['psicologo'])) {
        foreach ($psicologosDaClinica as $psicologo) {
            if ((int) $psicologo['idUsuario'] === (int) $_GET['psicologo']) {
                $idPsicologoFiltro = (int) $psicologo['idUsuario'];
            }
        }
    }

    // Filtro de status
    $statusFiltro = null;
    if (isset($_GET['status']) && in_array($_GET['status'], STATUS_PACIENTE_VALIDOS, true)) {
        $statusFiltro = $_GET['status'];
    }

    $contagem = contarPacientesPorStatus($conexao, $idClinica);

    $pacientesCadastrados = buscarPacientes($conexao, $idClinica, $idPsicologoFiltro, $statusFiltro);
    $pacientesEmEspera    = buscarPacientes($conexao, $idClinica, $idPsicologoFiltro, 'em_espera');
    $pacientesAtivos      = buscarPacientes($conexao, $idClinica, $idPsicologoFiltro, 'ativo');

    $nomesDosStatus = [
        'ativo'     => 'Ativo',
        'em_espera' => 'Em espera',
        'pausado'   => 'Pausado',
        'encerrado' => 'Encerrado',
        'inativo'   => 'Inativo',
    ];

    // Monta o link de um chip mantendo o outro filtro
    function montarLinkFiltro(?int $psicologo, ?string $status): string {
        $parametros = [];
        if ($psicologo !== null) $parametros['psicologo'] = $psicologo;
        if ($status !== null)    $parametros['status']    = $status;
        return 'pacientes.php' . ($parametros ? '?' . http_build_query($parametros) : '');
    }

    // Desenha as linhas de uma tabela de pacientes
    function mostrarLinhasPacientes(array $pacientes, array $nomesDosStatus): void {
        if (empty($pacientes)) {
            echo '<tr><td colspan="3">Nenhum paciente encontrado.</td></tr>';
            return;
        }
        foreach ($pacientes as $paciente) {
            echo '<tr onclick="abrirDrawer(' . (int) $paciente['idpaciente'] . ')" style="cursor:pointer">';
            echo '<td>' . htmlspecialchars($paciente['nome_paciente']) . '</td>';
            echo '<td>' . htmlspecialchars($paciente['nome_psicologo']) . '</td>';
            echo '<td><span class="badge badge-green">' . htmlspecialchars($nomesDosStatus[$paciente['status']]) . '</span></td>';
            echo '</tr>';
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes Cadastrados</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" type="text/css" href="/app/css/pacientes.css">
  
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>

</head>
<body>
        <div class="main">

            <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>

            <div class="cartoes">
                <div class="cartao" onclick="toggleList('listaCadastrados')">
                    <div class="cartaoValor"><?= $contagem['todos'] ?></div>
                    <div class="cartaoTitulo">Pacientes Cadastrados</div>
                </div>

                <div class="cartao" onclick="toggleList('listaEspera')">
                    <div class="cartaoValor"><?= $contagem['em_espera'] ?></div>
                    <div class="cartaoTitulo">Pacientes em fila de espera</div>
                </div>

                <div class="cartao" onclick="toggleList('listaAtivos')">
                    <div class="cartaoValor"><?= $contagem['ativo'] ?></div>
                    <div class="cartaoTitulo">Pacientes ativos</div>
                </div>
            </div>
        
            <div class="filtro-secao">
                <div class="filtro-dropdown-wrapper">
                    <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                        Filtrar
                    </button>
                    <div class="filtro-dropdown" id="filtro-dropdown">
                        <div class="filtro-grupo">
                            <p class="filtro-titulo">Psicólogo</p>
                            <div class="lista-chips" id="filtro-psicologo">
                                <a class="chip <?= $idPsicologoFiltro === null ? 'active' : '' ?>"
                                href="<?= montarLinkFiltro(null, $statusFiltro) ?>">Todos</a>
                                <?php foreach ($psicologosDaClinica as $psicologo): ?>
                                    <a class="chip <?= $idPsicologoFiltro === (int) $psicologo['idUsuario'] ? 'active' : '' ?>"
                                    href="<?= montarLinkFiltro((int) $psicologo['idUsuario'], $statusFiltro) ?>">
                                        <?= htmlspecialchars($psicologo['nome']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="filtro-grupo">
                            <p class="filtro-titulo">Status</p>
                            <div class="lista-chips" id="filtro-status">
                                <a class="chip <?= $statusFiltro === null ? 'active' : '' ?>"
                                href="<?= montarLinkFiltro($idPsicologoFiltro, null) ?>">Todos</a>
                                <?php foreach ($nomesDosStatus as $valor => $nome): ?>
                                    <a class="chip <?= $statusFiltro === $valor ? 'active' : '' ?>"
                                    href="<?= montarLinkFiltro($idPsicologoFiltro, $valor) ?>">
                                        <?= $nome ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    
        <div id="listaCadastrados" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pacientes Cadastrados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesCadastrados, $nomesDosStatus); ?>
                </tbody>
            </table>
        </div>

        <div id="listaEspera" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Lista de Espera</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesEmEspera, $nomesDosStatus); ?>
                </tbody>
            </table>
        </div>

        <div id="listaAtivos" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Lista de Ativos (Em acompanhamento Regular)</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesAtivos, $nomesDosStatus); ?>
                </tbody>
            </table>
        </div>

        <script src="/app/js/utils.js"></script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>
</div>
    
</body>
</html>