<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$idClinica  = (int) $_SESSION['id_clinica'];
$idConsulta = (int) ($_POST['id_consulta'] ?? 0);
$acao       = $_POST['acao'] ?? '';

$consulta = buscarConsultaPorId($conexao, $idConsulta, $idClinica);
if (!$consulta) responder(404, ['erro' => 'Consulta não encontrada.']);

$perm = permissoesDaConsulta($consulta);

if ($acao === 'concluir') {
    if (!$perm['podeConcluir']) responder(403, ['erro' => 'Não é possível concluir esta consulta.']);
    atualizarStatusConsulta($conexao, $idConsulta, 'concluida', date('Y-m-d H:i:s'));
} elseif ($acao === 'desconcluir') {
    if (!$perm['podeDesconcluir']) responder(403, ['erro' => 'O prazo de 3 horas para desfazer já passou.']);
    atualizarStatusConsulta($conexao, $idConsulta, 'atrasada', null);
} elseif ($acao === 'confirmar' || $acao === 'desconfirmar') {
    if (!$perm['podeConfirmar']) responder(403, ['erro' => 'Não é possível alterar a confirmação desta consulta.']);
    $momento = $acao === 'confirmar' ? date('Y-m-d H:i:s') : null;
    $comando = $conexao->prepare("UPDATE Consulta SET confirmada_em = ? WHERE idconsulta = ?");
    $comando->bind_param("si", $momento, $idConsulta);
    $comando->execute();
} elseif ($acao === 'cancelar') {
    if (!$perm['podeCancelar']) responder(403, ['erro' => 'Não é possível cancelar esta consulta.']);
    $conexao->begin_transaction();
    try {
        cancelarConsulta($conexao, $idConsulta);
        $conexao->commit();
    } catch (Throwable $e) {
        $conexao->rollback();
        responder(500, ['erro' => 'Não foi possível cancelar a consulta.']);
    }
    if ((int) $consulta['id_psicologo'] !== (int) $_SESSION['id']) {
        $pac = buscarPacientePorId($conexao, (int) $consulta['id_paciente'], $idClinica);
        criarNotificacao($conexao, (int) $consulta['id_psicologo'],
            $_SESSION['nome'] . ' cancelou a consulta de ' . ($pac['nome'] ?? 'paciente') . ' de '
            . date('d/m', strtotime($consulta['data'])) . ' às ' . substr($consulta['hora_inicio'], 0, 5) . '.',
            (int) $consulta['id_paciente'], 'consulta', $idConsulta);
    }
} else {
    responder(422, ['erro' => 'Ação inválida.']);
}

responder(200, ['ok' => true]);
