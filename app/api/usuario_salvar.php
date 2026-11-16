<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarPermissao(["ADMIN"]);
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$idClinica  = (int) $_SESSION['id_clinica'];
$idLogado   = (int) $_SESSION['id'];
$idUsuario  = (int) ($_POST['id_usuario'] ?? 0);   // 0 = novo usuário

$nome      = trim($_POST['nome'] ?? '');
$email     = mb_strtolower(trim($_POST['email'] ?? ''));
$telefone  = trim($_POST['telefone'] ?? '');
$crp       = trim($_POST['crp'] ?? '');
$permissao = $_POST['permissao'] ?? '';
$senha     = $_POST['senha'] ?? '';

if ($nome === '' || mb_strlen($nome) > 45) responder(422, ['erro' => 'Informe o nome (até 45 caracteres).']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 45) responder(422, ['erro' => 'E-mail inválido.']);
if (mb_strlen($telefone) > 45) responder(422, ['erro' => 'Telefone muito longo.']);
if (mb_strlen($crp) > 20) responder(422, ['erro' => 'O CRP pode ter no máximo 20 caracteres.']);
if (!in_array($permissao, ['ADMIN', 'PSICOLOGO', 'RECEPCIONISTA'], true)) responder(422, ['erro' => 'Escolha o papel do usuário.']);
if ($permissao === 'RECEPCIONISTA') $crp = '';

if ($senha !== '' && (mb_strlen($senha) < 8 || strlen($senha) > 72)) {
    responder(422, ['erro' => 'A senha precisa ter de 8 a 72 caracteres.']);
}
if (emailEmUso($conexao, $email, $idUsuario)) responder(409, ['erro' => 'Esse e-mail já está em uso.']);

$telefone = $telefone !== '' ? $telefone : null;
$crp      = $crp !== '' ? $crp : null;

if ($idUsuario === 0) {
    if ($senha === '') responder(422, ['erro' => 'Defina uma senha provisória (mínimo 8 caracteres).']);
    $id = inserirUsuario($conexao, $idClinica, $nome, $email, password_hash($senha, PASSWORD_DEFAULT), $telefone, $crp, $permissao);
    responder(200, ['ok' => true, 'id' => $id]);
}

$alvo = buscarUsuarioNaClinica($conexao, $idUsuario, $idClinica);
if (!$alvo) responder(404, ['erro' => 'Usuário não encontrado.']);

if ($permissao !== $alvo['permissao']) {
    if ($idUsuario === $idLogado) responder(422, ['erro' => 'Você não pode alterar o seu próprio papel.']);
    if (in_array($alvo['permissao'], ['ADMIN', 'PSICOLOGO'], true) && $permissao === 'RECEPCIONISTA'
        && contarPacientesEmAcompanhamento($conexao, $idUsuario) > 0) {
        responder(409, ['erro' => 'Esse profissional ainda tem pacientes. Transfira os pacientes antes de mudar o papel.']);
    }
}

atualizarUsuario($conexao, $idUsuario, $nome, $email, $telefone, $crp, $permissao,
                 $senha !== '' ? password_hash($senha, PASSWORD_DEFAULT) : null);

if ($idUsuario === $idLogado) $_SESSION['nome'] = $nome;

responder(200, ['ok' => true]);
