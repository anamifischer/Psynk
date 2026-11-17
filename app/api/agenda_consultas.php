<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";

header('Content-Type: application/json; charset=utf-8');

$idClinica = (int) $_SESSION['id_clinica'];

// psicólogo só vê a própria agenda
$filtroPsicologo = $_SESSION['permissao'] === 'PSICOLOGO' ? 'AND consulta.id_psicologo = ' . (int) $_SESSION['id'] : '';

$sql = "SELECT consulta.idconsulta,
               consulta.data,
               consulta.hora_inicio,
               consulta.status,
               consulta.confirmada_em,
               consulta.id_psicologo,
               consulta.id_paciente,
               paciente.nome AS nome_paciente,
               paciente.idPsicologo AS id_responsavel,
               (SELECT a.conteudo
                  FROM AnotacaoClinica a
                 WHERE a.Consulta_idconsulta = consulta.idconsulta
                 ORDER BY a.idAnotacaoClinica DESC
                 LIMIT 1) AS anotacao
        FROM Consulta consulta
        JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
        WHERE consulta.Clinica_idtable1 = ?
          {$filtroPsicologo}
        ORDER BY consulta.data, consulta.hora_inicio";

$comando = $conexao->prepare($sql);
$comando->bind_param("i", $idClinica);
$comando->execute();


$podeVerAnotacoes = in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true);

// confirmado (verde), pendente (amarelo), pendente hoje (laranja) ou cancelado (cinza)
function statusNaAgenda(array $c): string {
    if ($c['status'] === 'cancelada') return 'cancelado';
    if ($c['status'] === 'concluida' || !empty($c['confirmada_em'])) return 'confirmado';
    return $c['data'] === date('Y-m-d') ? 'pendente-hoje' : 'pendente';
}

$lista = [];
foreach ($comando->get_result()->fetch_all(MYSQLI_ASSOC) as $c) {
    $lista[] = [
        'id'          => (int) $c['idconsulta'],
        'data'        => $c['data'],
        'hora'        => substr($c['hora_inicio'], 0, 5),
        'pacienteId'  => (int) $c['id_paciente'],
        'paciente'    => $c['nome_paciente'],
        'psicologoId' => (int) $c['id_psicologo'],
        'status'      => statusNaAgenda($c),
        'anotacao'    => ($podeVerAnotacoes && (int) $c['id_responsavel'] === (int) $_SESSION['id'])
                            ? $c['anotacao'] : null,
    ];
}

echo json_encode($lista, JSON_UNESCAPED_UNICODE);