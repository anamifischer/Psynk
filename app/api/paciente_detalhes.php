<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";

header('Content-Type: application/json; charset=utf-8');

$idClinica  = (int) $_SESSION['id_clinica'];
$idPaciente = (int) ($_GET['id'] ?? 0);

$paciente = buscarPacientePorId($conexao, $idPaciente, $idClinica);
if (!$paciente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Paciente não encontrado']);
    exit;
}

// Regra do seu README: só o psicólogo responsável vê as anotações
$mostrarAnotacoes = in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true)
    && (int) $paciente['idPsicologo'] === (int) $_SESSION['id'];

$nomesStatus = [
    'ativo' => 'Ativo', 'em_espera' => 'Em espera', 'pausado' => 'Pausado',
    'encerrado' => 'Encerrado', 'inativo' => 'Inativo',
];

$sessoes = [];
foreach (buscarSessoesDoPaciente($conexao, $idPaciente) as $s) {
    $sessoes[] = [
        'data'      => date('d/m/Y', strtotime($s['data'])),
        'hora'      => substr($s['hora_inicio'], 0, 5),
        'realizada' => $s['status'] === 'concluida',
        'futuro'    => $s['status'] === 'aguarda data',
        'anotacao'  => $mostrarAnotacoes ? $s['conteudo'] : null,
    ];
}

$pagamentos = [];
foreach (buscarPagamentosDoPaciente($conexao, $idPaciente) as $p) {
    $pagamentos[] = [
        'data'   => date('d/m/Y', strtotime($p['data_vencimento'])),
        'valor'  => (float) $p['valor'],
        'status' => ucfirst($p['status']),
    ];
}

$psicologos = [];
foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
    $psicologos[] = ['id' => (int) $ps['idUsuario'], 'nome' => $ps['nome']];
}

echo json_encode([
    'id'               => (int) $paciente['idpaciente'],
    'nome'             => $paciente['nome'],
    'status'           => $nomesStatus[$paciente['status']],
    'statusValor'      => $paciente['status'],
    'nascimento'       => $paciente['data_nascimento']
                            ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : null,
    'telefone'         => $paciente['telefone'],
    'endereco'         => $paciente['endereco'],
    'observacoes'      => $paciente['observacoes'],
    'psicologo'        => $paciente['nome_psicologo'],
    'mostrarAnotacoes' => $mostrarAnotacoes,
    'sessoes'          => $sessoes,
    'pagamentos'       => $pagamentos,
    'idPsicologo' => (int) $paciente['idPsicologo'],
    'psicologos'  => $psicologos,
], JSON_UNESCAPED_UNICODE);