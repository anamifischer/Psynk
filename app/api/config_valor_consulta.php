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

// Só quem atende pacientes tem valor de consulta
if (!in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true)) {
    http_response_code(403);
    echo json_encode(['erro' => 'Sem permissão']);
    exit;
}

$valor = filter_var($_POST['valor'] ?? null, FILTER_VALIDATE_FLOAT);
if ($valor === false || $valor < 0 || $valor > 99999999.99) {
    http_response_code(400);
    echo json_encode(['erro' => 'Valor inválido']);
    exit;
}

salvarValorConsulta($conexao, (int) $_SESSION['id'], round($valor, 2));

echo json_encode(['ok' => true]);