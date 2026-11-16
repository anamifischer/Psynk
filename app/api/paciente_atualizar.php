<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$permissao  = $_SESSION['permissao'];
$idUsuario  = (int) $_SESSION['id'];
$idClinica  = (int) $_SESSION['id_clinica'];
$idPaciente = (int) ($_POST['id_paciente'] ?? 0);

$paciente = buscarPacientePorId($conexao, $idPaciente, $idClinica);
if (!$paciente) responder(404, ['erro' => 'Paciente não encontrado.']);

if (!in_array($permissao, ['ADMIN', 'PSICOLOGO', 'RECEPCIONISTA'], true)) responder(403, ['erro' => 'Sem permissão.']);
if ($permissao === 'PSICOLOGO' && (int) $paciente['idPsicologo'] !== $idUsuario) {
    responder(403, ['erro' => 'Você só pode editar seus pacientes.']);
}

$dados      = lerDadosPaciente();
$idNovoResp = (int) ($_POST['id_psicologo'] ?? 0);

// só o admin troca o responsável
$trocouResponsavel = $idNovoResp !== (int) $paciente['idPsicologo'];
if ($trocouResponsavel) {
    if ($permissao !== 'ADMIN') responder(403, ['erro' => 'Só o administrador pode alterar o responsável.']);
    $responsavelValido = false;
    foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
        if ((int) $ps['idUsuario'] === $idNovoResp) $responsavelValido = true;
    }
    if (!$responsavelValido) responder(422, ['erro' => 'Psicólogo responsável inválido.']);
}

$conexao->begin_transaction();
try {
    atualizarDadosPaciente($conexao, $idPaciente, $dados['nome'], $dados['status'], $dados['telefone'],
                           $dados['endereco'], $dados['observacoes'], $dados['nascimento']);
    if ($trocouResponsavel) trocarResponsavelDoPaciente($conexao, $idPaciente, $idNovoResp, $idUsuario);
    $conexao->commit();
} catch (Throwable $e) {
    $conexao->rollback();
    responder(500, ['erro' => 'Não foi possível salvar os dados.']);
}

if ($trocouResponsavel && $idNovoResp !== $idUsuario) {
    criarNotificacao($conexao, $idNovoResp,
        $_SESSION['nome'] . ' transferiu o paciente ' . $dados['nome'] . ' para você.',
        $idPaciente, 'responsavel');
}

responder(200, ['ok' => true]);
