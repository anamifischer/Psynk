<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

// Cada pessoa só altera a própria senha
$idUsuario = (int) $_SESSION['id'];

$atual     = $_POST['senha_atual'] ?? '';
$nova      = $_POST['nova_senha'] ?? '';
$confirmar = $_POST['confirmar_senha'] ?? '';

if ($atual === '' || $nova === '' || $confirmar === '') responder(422, ['erro' => 'Preencha todos os campos.']);
if ($nova !== $confirmar)        responder(422, ['erro' => 'A confirmação não é igual à nova senha.']);
if (mb_strlen($nova) < 8)        responder(422, ['erro' => 'A nova senha precisa ter pelo menos 8 caracteres.']);
if (strlen($nova) > 72)          responder(422, ['erro' => 'A nova senha pode ter no máximo 72 caracteres.']);
if ($nova === $atual)            responder(422, ['erro' => 'A nova senha precisa ser diferente da atual.']);

$c = $conexao->prepare("SELECT senha FROM Usuario WHERE idUsuario = ?");
$c->bind_param("i", $idUsuario);
$c->execute();
$linha = $c->get_result()->fetch_assoc();

if (!$linha || !password_verify($atual, $linha['senha'])) {
    responder(403, ['erro' => 'A senha atual está incorreta.']);
}

$hash = password_hash($nova, PASSWORD_DEFAULT);
$c = $conexao->prepare("UPDATE Usuario SET senha = ? WHERE idUsuario = ?");
$c->bind_param("si", $hash, $idUsuario);
$c->execute();

// Renova o identificador da sessão por segurança (continua logada)
session_regenerate_id(true);

responder(200, ['ok' => true]);
