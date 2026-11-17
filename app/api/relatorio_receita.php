<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarPermissao(["ADMIN", "PSICOLOGO"]);
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

$idClinica = (int) $_SESSION['id_clinica'];
$ano       = (int) ($_GET['ano'] ?? date('Y'));

$idPsicologo = null;
if ($_SESSION['permissao'] === 'PSICOLOGO') {
    // psicólogo só enxerga os próprios números
    $idPsicologo = (int) $_SESSION['id'];
} elseif (!empty($_GET['psicologo'])) {
    foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
        if ((int) $ps['idUsuario'] === (int) $_GET['psicologo']) {
            $idPsicologo = (int) $ps['idUsuario'];
        }
    }
}

echo json_encode(receitaMensal($conexao, $idClinica, $ano, $idPsicologo));