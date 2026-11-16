<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

$idUsuario = (int) $_SESSION['id'];

$itens = [];
foreach (buscarNotificacoes($conexao, $idUsuario, 20) as $n) {
    $itens[] = [
        'id'         => (int) $n['idNotificacao'],
        'mensagem'   => $n['mensagem'],
        'quando'     => date('d/m H:i', strtotime($n['criado_em'])),
        'lida'       => (int) $n['lida'] === 1,
        'idPaciente' => $n['id_paciente'] !== null ? (int) $n['id_paciente'] : null,
    ];
}

echo json_encode([
    'naoLidas' => contarNotificacoesNaoLidas($conexao, $idUsuario),
    'itens'    => $itens,
], JSON_UNESCAPED_UNICODE);
