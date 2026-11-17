<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["PSICOLOGO"]);
    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";
    $tituloPagina = 'Pacientes';

    $idClinica = (int) $_SESSION['id_clinica'];

    // O psicólogo só vê os próprios pacientes
    $idPsicologoFiltro = (int) $_SESSION['id'];

    // Filtro de status
    $statusFiltro = null;
    if (isset($_GET['status']) && in_array($_GET['status'], STATUS_PACIENTE_VALIDOS, true)) {
        $statusFiltro = $_GET['status'];
    }

    $contagem = contarPacientesPorStatus($conexao, $idClinica, $idPsicologoFiltro);

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

    // cor do badge de cada status
    $coresStatus = [
        'ativo' => 'verde', 'em_espera' => 'azul', 'pausado' => 'amarelo',
        'encerrado' => 'cinza', 'inativo' => 'cinza',
    ];

    // Monta o link de um chip mantendo o outro filtro
    function montarLinkFiltro(?string $status): string {
        return 'pacientes.php' . ($status !== null ? '?' . http_build_query(['status' => $status]) : '');
    }

    // Desenha as linhas de uma tabela de pacientes
    function mostrarLinhasPacientes(array $pacientes, array $nomesDosStatus, array $coresStatus): void {
        if (empty($pacientes)) {
            echo '<tr><td colspan="2">Nenhum paciente encontrado.</td></tr>';
            return;
        }
        foreach ($pacientes as $paciente) {
            echo '<tr onclick="abrirDrawer(' . (int) $paciente['idpaciente'] . ')" style="cursor:pointer">';
            echo '<td>' . htmlspecialchars($paciente['nome_paciente']) . '</td>';
            echo '<td><span class="badge badge-' . ($coresStatus[$paciente['status']] ?? 'cinza') . '">' . htmlspecialchars($nomesDosStatus[$paciente['status']]) . '</span></td>';
            echo '</tr>';
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Pacientes</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" type="text/css" href="/app/css/pacientes.css">
  
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuPsicologo.php");
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
        
            <div class="filtro-secao" style="display:flex; gap:0.75rem; align-items:center;">
                <button class="btn btn-primary" onclick="abrirDrawerNovo()">+ Novo paciente</button>
                <div class="filtro-dropdown-wrapper">
                    <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                        Filtrar
                    </button>
                    <div class="filtro-dropdown" id="filtro-dropdown">
                        <div class="filtro-grupo">
                            <p class="filtro-titulo">Status</p>
                            <div class="lista-chips" id="filtro-status">
                                <a class="chip <?= $statusFiltro === null ? 'active' : '' ?>"
                                href="<?= montarLinkFiltro(null) ?>">Todos</a>
                                <?php foreach ($nomesDosStatus as $valor => $nome): ?>
                                    <a class="chip <?= $statusFiltro === $valor ? 'active' : '' ?>"
                                    href="<?= montarLinkFiltro($valor) ?>">
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
                        
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesCadastrados, $nomesDosStatus, $coresStatus); ?>
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
                        
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesEmEspera, $nomesDosStatus, $coresStatus); ?>
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
                        
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarLinhasPacientes($pacientesAtivos, $nomesDosStatus, $coresStatus); ?>
                </tbody>
            </table>
        </div>

        <script src="/app/js/utils.js"></script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/modal-consulta.php"); ?>
</div>
    
</body>
</html>