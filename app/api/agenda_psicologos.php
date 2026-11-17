<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

$idClinica = (int) $_SESSION['id_clinica'];

$lista = [];
foreach (buscarPsicologosDaClinica($conexao, $idClinica, true) as $ps) {
    if ($_SESSION['permissao'] === 'PSICOLOGO' && (int) $ps['idUsuario'] !== (int) $_SESSION['id']) continue;
    $lista[] = ['id' => (int) $ps['idUsuario'], 'nome' => $ps['nome']];
}

echo json_encode($lista, JSON_UNESCAPED_UNICODE);