<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$idClinica  = (int) $_SESSION['id_clinica'];
$idUsuario  = (int) $_SESSION['id'];
$idConsulta = (int) ($_POST['id_consulta'] ?? 0);

$consulta = buscarConsultaPorId($conexao, $idConsulta, $idClinica);
if (!$consulta) responder(404, ['erro' => 'Consulta não encontrada.']);

$perm = permissoesDaConsulta($consulta);

$data     = trim($_POST['data'] ?? '');
$hora     = trim($_POST['hora_inicio'] ?? '');
$duracao  = (int) ($_POST['duracao'] ?? 0);
$anotacao = trim($_POST['anotacao'] ?? '');

$duracaoAtual = (int) round((strtotime($consulta['hora_fim']) - strtotime($consulta['hora_inicio'])) / 60);
$mudouHorario = $data !== $consulta['data']
             || $hora !== substr($consulta['hora_inicio'], 0, 5)
             || $duracao !== $duracaoAtual;

if ($mudouHorario) {
    if (!$perm['podeRemarcar']) responder(403, ['erro' => 'Não é possível remarcar esta consulta.']);
    [$inicio, $fim] = validarDataHoraConsulta($data, $hora, $duracao);
    validarDisponibilidade($conexao, (int) $consulta['id_psicologo'], $data, $hora, $inicio, $fim, $idConsulta);
}

$anotacaoAtual = buscarAnotacaoDaConsulta($conexao, $idConsulta);
$mudouAnotacao = $anotacao !== ($anotacaoAtual['conteudo'] ?? '');

if ($mudouAnotacao) {
    if (!$perm['podeAnotar']) responder(403, ['erro' => 'Você não pode alterar a anotação desta consulta.']);
    if (mb_strlen($anotacao) > 500) responder(422, ['erro' => 'A anotação pode ter no máximo 500 caracteres.']);
    if ($anotacao === '' && $anotacaoAtual) responder(422, ['erro' => 'Uma anotação existente não pode ser apagada.']);
}

if (!$mudouHorario && !$mudouAnotacao) responder(200, ['ok' => true]);

$dataFinal = $mudouHorario ? $data : $consulta['data'];

$conexao->begin_transaction();
try {
    if ($mudouHorario) atualizarHorarioConsulta($conexao, $idConsulta, $data, $inicio, $fim);
    if ($mudouAnotacao && $anotacao !== '') {
        if ($anotacaoAtual) atualizarAnotacao($conexao, (int) $anotacaoAtual['idAnotacaoClinica'], $anotacao);
        else inserirAnotacao($conexao, $idConsulta, (int) $consulta['id_paciente'], $idUsuario, $dataFinal, $anotacao);
    }
    $conexao->commit();
} catch (Throwable $e) {
    $conexao->rollback();
    responder(500, ['erro' => 'Não foi possível salvar as alterações.']);
}

if ($mudouHorario && (int) $consulta['id_psicologo'] !== $idUsuario) {
    $paciente = buscarPacientePorId($conexao, (int) $consulta['id_paciente'], $idClinica);
    criarNotificacao($conexao, (int) $consulta['id_psicologo'],
        $_SESSION['nome'] . ' remarcou a consulta de ' . ($paciente['nome'] ?? 'paciente') . ' para '
        . date('d/m', strtotime($data)) . ' às ' . $hora . '.',
        (int) $consulta['id_paciente'], 'consulta', $idConsulta);
}

responder(200, ['ok' => true]);
