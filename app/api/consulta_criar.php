<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$permissao = $_SESSION['permissao'];
$idUsuario = (int) $_SESSION['id'];
$idClinica = (int) $_SESSION['id_clinica'];

$idPaciente  = (int) ($_POST['id_paciente'] ?? 0);
$idPsicologo = (int) ($_POST['id_psicologo'] ?? 0);
$data        = trim($_POST['data'] ?? '');
$hora        = trim($_POST['hora_inicio'] ?? '');
$duracao     = (int) ($_POST['duracao'] ?? 0);
$anotacao    = trim($_POST['anotacao'] ?? '');

[$inicio, $fim] = validarDataHoraConsulta($data, $hora, $duracao);

// paciente da clínica e que ainda pode ter consulta
$paciente = buscarPacientePorId($conexao, $idPaciente, $idClinica);
if (!$paciente) responder(404, ['erro' => 'Paciente não encontrado.']);
if (in_array($paciente['status'], ['encerrado', 'inativo'], true)) {
    responder(422, ['erro' => 'Paciente encerrado ou inativo não pode ter consultas.']);
}

// psicólogo precisa ser da clínica
$psicologoValido = false;
foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
    if ((int) $ps['idUsuario'] === $idPsicologo) $psicologoValido = true;
}
if (!$psicologoValido) responder(422, ['erro' => 'Psicólogo inválido.']);

// psicólogo só agenda para os próprios pacientes
if ($permissao === 'PSICOLOGO' && (int) $paciente['idPsicologo'] !== $idUsuario) {
    responder(403, ['erro' => 'Você só pode agendar para seus pacientes.']);
}

validarDisponibilidade($conexao, $idPsicologo, $data, $hora, $inicio, $fim);

// só o responsável pelo paciente anota
$ehResponsavel = in_array($permissao, ['ADMIN', 'PSICOLOGO'], true) && (int) $paciente['idPsicologo'] === $idUsuario;
if ($anotacao !== '') {
    if (!$ehResponsavel) responder(403, ['erro' => 'Só o psicólogo responsável pode anotar.']);
    if (mb_strlen($anotacao) > 500) responder(422, ['erro' => 'A anotação pode ter no máximo 500 caracteres.']);
}

$conexao->begin_transaction();
try {
    $idConsulta = inserirConsulta($conexao, $idClinica, $idPaciente, $idPsicologo, $data, $inicio, $fim);
    if ($anotacao !== '') inserirAnotacao($conexao, $idConsulta, $idPaciente, $idUsuario, $data, $anotacao);
    $conexao->commit();
} catch (Throwable $e) {
    $conexao->rollback();
    responder(500, ['erro' => 'Não foi possível agendar a consulta.']);
}

if ($idPsicologo !== $idUsuario) {
    criarNotificacao($conexao, $idPsicologo,
        $_SESSION['nome'] . ' agendou uma consulta com ' . $paciente['nome'] . ' para '
        . date('d/m', strtotime($data)) . ' às ' . $hora . '.',
        $idPaciente, 'consulta', $idConsulta);
}

responder(200, ['ok' => true, 'id' => $idConsulta]);
