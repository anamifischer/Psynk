<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

// Só quem atende pacientes tem horário de atendimento
if (!in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true)) {
    responder(403, ['erro' => 'Sem permissão.']);
}
$idUsuario = (int) $_SESSION['id'];

$duracao   = (int) ($_POST['duracao'] ?? 0);
$intervalo = isset($_POST['intervalo']) ? (int) $_POST['intervalo'] : -1;
if (!in_array($duracao, [30, 45, 50, 60], true))   responder(422, ['erro' => 'Duração inválida.']);
if (!in_array($intervalo, [0, 10, 15, 30], true))  responder(422, ['erro' => 'Intervalo inválido.']);

$dias = $_POST['dias'] ?? [];
if (!is_array($dias)) responder(422, ['erro' => 'Horários inválidos.']);

$nomes = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado', 7 => 'Domingo'];
$horarios = [];
foreach ($dias as $dia => $faixa) {
    $dia = (int) $dia;
    if ($dia < 1 || $dia > 7 || !is_array($faixa)) responder(422, ['erro' => 'Dia da semana inválido.']);
    $inicio = $faixa['inicio'] ?? '';
    $fim    = $faixa['fim'] ?? '';
    $ok = '/^([01]\d|2[0-3]):[0-5]\d$/';
    if (!preg_match($ok, $inicio) || !preg_match($ok, $fim)) {
        responder(422, ['erro' => "{$nomes[$dia]}: informe o início e o fim."]);
    }
    if ($inicio >= $fim) {
        responder(422, ['erro' => "{$nomes[$dia]}: o início precisa ser antes do fim."]);
    }
    $horarios[$dia] = [$inicio . ':00', $fim . ':00'];
}

$conexao->begin_transaction();
try {
    salvarHorariosAtendimento($conexao, $idUsuario, $horarios, $duracao, $intervalo);
    $conexao->commit();
} catch (Throwable $e) {
    $conexao->rollback();
    responder(500, ['erro' => 'Não foi possível salvar os horários.']);
}

responder(200, ['ok' => true]);
