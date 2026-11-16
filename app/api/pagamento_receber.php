<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$idClinica   = (int) $_SESSION['id_clinica'];
$idPagamento = (int) ($_POST['id_pagamento'] ?? 0);
$acao        = $_POST['acao'] ?? '';

$pagamento = buscarPagamentoPorId($conexao, $idPagamento, $idClinica);
if (!$pagamento) responder(404, ['erro' => 'Pagamento não encontrado.']);
if (!podeReceberPagamento($pagamento)) responder(403, ['erro' => 'Você não pode alterar este pagamento.']);

if ($acao === 'receber') {
    if ($pagamento['status'] === 'pago') responder(409, ['erro' => 'Este pagamento já foi recebido.']);
    marcarPagamentoRecebido($conexao, $idPagamento, date('Y-m-d'));
} elseif ($acao === 'desfazer') {
    if ($pagamento['status'] !== 'pago') responder(409, ['erro' => 'Este pagamento não está como recebido.']);
    desfazerPagamentoRecebido($conexao, $idPagamento);
    sincronizarStatusPagamentos($conexao);
} else {
    responder(422, ['erro' => 'Ação inválida.']);
}

responder(200, ['ok' => true]);
