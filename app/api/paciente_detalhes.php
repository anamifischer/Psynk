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
    $s['id_responsavel'] = $paciente['idPsicologo'];
    $perm = permissoesDaConsulta($s);
    $sessoes[] = [
        'id'              => (int) $s['idconsulta'],
        'data'            => date('d/m/Y', strtotime($s['data'])),
        'dataIso'         => $s['data'],
        'hora'            => substr($s['hora_inicio'], 0, 5),
        'duracao'         => (int) round((strtotime($s['hora_fim']) - strtotime($s['hora_inicio'])) / 60),
        'psicologoId'     => (int) $s['id_psicologo'],
        'status'          => $s['status'],
        'pagamentoPago'   => (int) $s['pagamento_pago'] > 0,
        'anotacao'        => $mostrarAnotacoes ? $s['conteudo'] : null,
        'jaComecou'       => $perm['jaComecou'],
        'podeConcluir'    => $perm['podeConcluir'],
        'podeDesconcluir' => $perm['podeDesconcluir'],
        'podeAnotar'      => $perm['podeAnotar'],
        'podeRemarcar'    => $perm['podeRemarcar'],
        'podeCancelar'    => $perm['podeCancelar'],
        'podeConfirmar'   => $perm['podeConfirmar'],
        'confirmada'      => !empty($s['confirmada_em']),
    ];
}

// Histórico de troca de responsável (só quem vê as anotações; não vai para o prontuário)
$historico = [];
if ($mostrarAnotacoes) {
    foreach (buscarHistoricoResponsavel($conexao, $idPaciente) as $h) {
        $historico[] = [
            'quando' => $h['criado_em'],
            'data'   => date('d/m/Y', strtotime($h['criado_em'])),
            'texto'  => 'Usuário ' . $h['nome_admin'] . ' alterou o responsável do paciente para ' . $h['nome_novo'],
        ];
    }
}

$pagamentos = [];
foreach (buscarPagamentosDoPaciente($conexao, $idPaciente) as $p) {
    $pagamentos[] = [
        'id'     => (int) $p['idPagamento'],
        'podeReceber' => podeReceberPagamento($p),
        'dataPagamento' => $p['data_pagamento'] ? date('d/m/Y', strtotime($p['data_pagamento'])) : null,
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
    'cpf'              => $paciente['cpf'],
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
    'historicoResponsavel' => $historico,
    'podeAlterarResponsavel' => $_SESSION['permissao'] === 'ADMIN',
    'pagamentos'       => $pagamentos,
    'idPsicologo' => (int) $paciente['idPsicologo'],
    'psicologos'  => $psicologos,
], JSON_UNESCAPED_UNICODE);