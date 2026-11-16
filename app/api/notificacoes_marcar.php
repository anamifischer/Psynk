<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

// Cada pessoa só mexe nas próprias notificações
$idUsuario = (int) $_SESSION['id'];

if (($_POST['id'] ?? '') === 'todas') {
    marcarNotificacoesComoLidas($conexao, $idUsuario, null);
} else {
    marcarNotificacoesComoLidas($conexao, $idUsuario, (int) ($_POST['id'] ?? 0));
}

echo json_encode(['ok' => true]);
