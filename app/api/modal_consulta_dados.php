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
    // psicólogo só agenda para os próprios pacientes
    if ($_SESSION['permissao'] === 'PSICOLOGO' && (int) $p['idPsicologo'] !== (int) $_SESSION['id']) {
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
    if ($_SESSION['permissao'] === 'PSICOLOGO' && (int) $ps['idUsuario'] !== (int) $_SESSION['id']) {
        continue;
    }
    $psicologos[] = ['id' => (int) $ps['idUsuario'], 'nome' => $ps['nome']];
}

echo json_encode(
    ['pacientes' => $pacientes, 'psicologos' => $psicologos,
     'usuario' => ['id' => (int) $_SESSION['id'], 'permissao' => $_SESSION['permissao']]],
    JSON_UNESCAPED_UNICODE
);