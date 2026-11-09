<?php
    require_once __DIR__ . "/../../config/sessao.php";
    verificarLogin();
    require_once __DIR__ . "/../../config/conexao.php";
    require_once __DIR__ . "/../includes/consultas.php";

    header('Content-Type: application/json; charset=utf-8');

    $idClinica = (int) $_SESSION['id_clinica'];
    $termo     = trim($_GET['q'] ?? '');

    if ($termo === '') {
        echo json_encode([]);
        exit;
    }

    $pacientes = buscarPacientesPorNome($conexao, $idClinica, $termo);

    echo json_encode($pacientes, JSON_UNESCAPED_UNICODE);
?>