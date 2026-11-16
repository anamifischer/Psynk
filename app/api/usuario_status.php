<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarPermissao(["ADMIN"]);
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$idClinica = (int) $_SESSION['id_clinica'];
$idUsuario = (int) ($_POST['id_usuario'] ?? 0);
$acao      = $_POST['acao'] ?? '';

$alvo = buscarUsuarioNaClinica($conexao, $idUsuario, $idClinica);
if (!$alvo) responder(404, ['erro' => 'Usuário não encontrado.']);

if ($acao === 'desativar') {
    if ($idUsuario === (int) $_SESSION['id']) responder(422, ['erro' => 'Você não pode desativar a si mesmo.']);
    $pacientes = contarPacientesEmAcompanhamento($conexao, $idUsuario);
    if ($pacientes > 0) {
        responder(409, ['erro' => "Esse usuário ainda é responsável por {$pacientes} paciente(s). Transfira ou encerre antes de desativar."]);
    }
    alterarAtivoUsuario($conexao, $idUsuario, false);
} elseif ($acao === 'ativar') {
    alterarAtivoUsuario($conexao, $idUsuario, true);
} else {
    responder(422, ['erro' => 'Ação inválida.']);
}

responder(200, ['ok' => true]);
