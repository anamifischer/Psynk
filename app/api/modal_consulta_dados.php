<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

$idClinica = (int) $_SESSION['id_clinica'];

$pacientes = [];
foreach (buscarPacientes($conexao, $idClinica) as $p) {
    // paciente encerrado ou inativo não recebe novas consultas
    if (in_array($p['status'], ['encerrado', 'inativo'], true)) {
        continue;
    }
    $pacientes[] = [
        'id'          => (int) $p['idpaciente'],
        'nome'        => $p['nome_paciente'],
        'idPsicologo' => (int) $p['idPsicologo'],
    ];
}

$psicologos = [];
foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
    $psicologos[] = ['id' => (int) $ps['idUsuario'], 'nome' => $ps['nome']];
}

echo json_encode(
    ['pacientes' => $pacientes, 'psicologos' => $psicologos],
    JSON_UNESCAPED_UNICODE
);