<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

// Cada pessoa só altera os próprios dados
$idUsuario = (int) $_SESSION['id'];

$nome     = trim($_POST['nome'] ?? '');
$email    = mb_strtolower(trim($_POST['email'] ?? ''));
$telefone = trim($_POST['telefone'] ?? '');
$crp      = trim($_POST['crp'] ?? '');

if ($nome === '' || mb_strlen($nome) > 45) responder(422, ['erro' => 'Informe seu nome (até 45 caracteres).']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 45) responder(422, ['erro' => 'E-mail inválido.']);
if (mb_strlen($telefone) > 45) responder(422, ['erro' => 'Telefone muito longo.']);
if (mb_strlen($crp) > 20)      responder(422, ['erro' => 'O CRP pode ter no máximo 20 caracteres.']);

// Recepcionista não tem CRP
if (!in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true)) $crp = '';

// O e-mail é o login: não pode repetir
$c = $conexao->prepare("SELECT 1 FROM Usuario WHERE email = ? AND idUsuario <> ? LIMIT 1");
$c->bind_param("si", $email, $idUsuario);
$c->execute();
if ($c->get_result()->num_rows > 0) responder(409, ['erro' => 'Esse e-mail já está em uso por outro usuário.']);

atualizarPerfil($conexao, $idUsuario, $nome, $email, $telefone !== '' ? $telefone : null, $crp !== '' ? $crp : null);

$_SESSION['nome'] = $nome;

responder(200, ['ok' => true, 'nome' => $nome]);
