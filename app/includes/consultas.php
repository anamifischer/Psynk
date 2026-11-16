<?php

// ------------------------------------------------------------
// Comum
// ------------------------------------------------------------

// 14:30 vira 14h30
function formatarHora(string $hora): string {
    return date('H\hi', strtotime($hora));
}

// ------------------------------------------------------------
// Dashboard
// ------------------------------------------------------------

// consultas de hoje (menos as canceladas)
function buscarConsultasDeHoje(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $sql = "SELECT consulta.idconsulta,
                   consulta.id_paciente,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.hora_inicio,
                   consulta.status
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE consulta.Clinica_idtable1 = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)
              AND consulta.data = CURDATE()
              AND consulta.status <> 'cancelada'
            ORDER BY consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// consultas futuras que o paciente ainda não confirmou
function buscarConsultasAguardandoConfirmacao(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $agora = date('Y-m-d H:i:s');
    $sql = "SELECT consulta.idconsulta,
                   consulta.id_paciente,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.data,
                   consulta.hora_inicio
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE consulta.Clinica_idtable1 = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)
              AND consulta.status IN ('aguarda data', 'atrasada')
              AND consulta.confirmada_em IS NULL
              AND TIMESTAMP(consulta.data, consulta.hora_inicio) > ?
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iiis", $idClinica, $idPsicologo, $idPsicologo, $agora);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// consultas que já começaram, não foram concluídas e estão sem anotação
function buscarConsultasSemAnotacao(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $agora = date('Y-m-d H:i:s');
    $sql = "SELECT consulta.idconsulta,
                   consulta.id_paciente,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.data,
                   consulta.hora_inicio
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            LEFT JOIN AnotacaoClinica anotacao ON anotacao.Consulta_idconsulta = consulta.idconsulta
            WHERE consulta.Clinica_idtable1 = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)
              AND consulta.status IN ('aguarda data', 'atrasada')
              AND TIMESTAMP(consulta.data, consulta.hora_inicio) <= ?
              AND anotacao.idAnotacaoClinica IS NULL
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iiis", $idClinica, $idPsicologo, $idPsicologo, $agora);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// consultas concluídas no mês atual
function buscarConsultasConcluidasNoMes(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $sql = "SELECT consulta.idconsulta,
                   consulta.id_paciente,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.data,
                   consulta.hora_inicio
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE consulta.Clinica_idtable1 = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)
              AND consulta.status = 'concluida'
              AND YEAR(consulta.data) = YEAR(CURDATE())
              AND MONTH(consulta.data) = MONTH(CURDATE())
            ORDER BY consulta.data DESC, consulta.hora_inicio DESC";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// só a contagem das concluídas no mês
function contarConsultasConcluidasNoMes(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): int {
    $sql = "SELECT COUNT(*)
            FROM Consulta
            WHERE Clinica_idtable1 = ?
              AND (? IS NULL OR id_psicologo = ?)
              AND status = 'concluida'
              AND YEAR(data) = YEAR(CURDATE())
              AND MONTH(data) = MONTH(CURDATE())";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return (int) $comando->get_result()->fetch_row()[0];
}

// ------------------------------------------------------------
// Pacientes
// ------------------------------------------------------------

const STATUS_PACIENTE_VALIDOS = ['ativo', 'em_espera', 'pausado', 'encerrado', 'inativo'];

// lista de pacientes, com filtro opcional de psicólogo e status
function buscarPacientes(mysqli $conexao, int $idClinica, ?int $idPsicologo = null, ?string $status = null): array {
    $sql = "SELECT paciente.idpaciente,
                   paciente.nome AS nome_paciente,
                   paciente.idPsicologo,
                   psicologo.nome AS nome_psicologo,
                   paciente.status
            FROM Paciente paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = paciente.idPsicologo
            WHERE paciente.idClinica = ?";

    $tipos      = "i";
    $parametros = [$idClinica];

    if ($idPsicologo !== null) {
        $sql .= " AND paciente.idPsicologo = ?";
        $tipos .= "i";
        $parametros[] = $idPsicologo;
    }

    if ($status !== null && in_array($status, STATUS_PACIENTE_VALIDOS, true)) {
        $sql .= " AND paciente.status = ?";
        $tipos .= "s";
        $parametros[] = $status;
    }

    $sql .= " ORDER BY paciente.nome";

    $comando = $conexao->prepare($sql);
    $comando->bind_param($tipos, ...$parametros);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// quantos pacientes em cada status, mais o total
function contarPacientesPorStatus(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $sql = "SELECT status, COUNT(*) AS total
            FROM Paciente
            WHERE idClinica = ?";

    $tipos      = "i";
    $parametros = [$idClinica];

    if ($idPsicologo !== null) {
        $sql .= " AND idPsicologo = ?";
        $tipos .= "i";
        $parametros[] = $idPsicologo;
    }

    $sql .= " GROUP BY status";

    $comando = $conexao->prepare($sql);
    $comando->bind_param($tipos, ...$parametros);
    $comando->execute();
    $linhas = $comando->get_result()->fetch_all(MYSQLI_ASSOC);

    $contagem = array_fill_keys(STATUS_PACIENTE_VALIDOS, 0);
    foreach ($linhas as $linha) {
        $contagem[$linha['status']] = (int) $linha['total'];
    }
    $contagem['todos'] = array_sum($contagem);
    return $contagem;
}

// busca da barra do topo
function buscarPacientesPorNome(mysqli $conexao, int $idClinica, string $termo = ''): array {
    $sql = "SELECT paciente.idpaciente, paciente.nome, paciente.status,
                   paciente.idPsicologo, psicologo.nome AS nome_psicologo
            FROM Paciente paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = paciente.idPsicologo
            WHERE paciente.idClinica = ? AND paciente.nome LIKE ?
            ORDER BY paciente.nome
            LIMIT 20";

    $padrao  = '%' . $termo . '%';
    $comando = $conexao->prepare($sql);
    $comando->bind_param("is", $idClinica, $padrao);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// dados do paciente para o drawer
function buscarPacientePorId(mysqli $conexao, int $idPaciente, int $idClinica): ?array {
    $sql = "SELECT paciente.idpaciente, paciente.nome, paciente.cpf, paciente.telefone, paciente.endereco,
                   paciente.status, paciente.idPsicologo, paciente.data_nascimento, paciente.observacoes,
                   psicologo.nome AS nome_psicologo
            FROM Paciente paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = paciente.idPsicologo
            WHERE paciente.idpaciente = ? AND paciente.idClinica = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("ii", $idPaciente, $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

// cadastra o paciente e devolve o id
function inserirPaciente(mysqli $conexao, int $idClinica, int $idPsicologo, string $nome, string $cpf, string $status,
                         string $telefone, ?string $endereco, ?string $observacoes, ?string $nascimento): int {
    $sql = "INSERT INTO Paciente (nome, cpf, telefone, endereco, status, idClinica, idPsicologo, data_nascimento, observacoes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("sssssiiss", $nome, $cpf, $telefone, $endereco, $status, $idClinica, $idPsicologo, $nascimento, $observacoes);
    $comando->execute();
    return (int) $conexao->insert_id;
}

// atualiza os dados do paciente (o CPF não muda)
function atualizarDadosPaciente(mysqli $conexao, int $idPaciente, string $nome, string $status, string $telefone,
                                ?string $endereco, ?string $observacoes, ?string $nascimento): void {
    $sql = "UPDATE Paciente
            SET nome = ?, status = ?, telefone = ?, endereco = ?, observacoes = ?, data_nascimento = ?
            WHERE idpaciente = ?";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("ssssssi", $nome, $status, $telefone, $endereco, $observacoes, $nascimento, $idPaciente);
    $comando->execute();
}

// pacientes do profissional que ainda não estão encerrados nem inativos
function contarPacientesEmAcompanhamento(mysqli $conexao, int $idPsicologo): int {
    $comando = $conexao->prepare("SELECT COUNT(*) AS total FROM Paciente WHERE idPsicologo = ? AND status NOT IN ('encerrado', 'inativo')");
    $comando->bind_param("i", $idPsicologo);
    $comando->execute();
    return (int) $comando->get_result()->fetch_assoc()['total'];
}

// troca o responsável e guarda quem fez a troca
function trocarResponsavelDoPaciente(mysqli $conexao, int $idPaciente, int $idNovoResponsavel, int $idAdmin): void {
    $comando = $conexao->prepare("UPDATE Paciente SET idPsicologo = ? WHERE idpaciente = ?");
    $comando->bind_param("ii", $idNovoResponsavel, $idPaciente);
    $comando->execute();

    $agora = date('Y-m-d H:i:s');
    $comando = $conexao->prepare("INSERT INTO HistoricoResponsavel (Paciente_idpaciente, id_admin, id_novo_responsavel, criado_em) VALUES (?, ?, ?, ?)");
    $comando->bind_param("iiis", $idPaciente, $idAdmin, $idNovoResponsavel, $agora);
    $comando->execute();
}

// trocas de responsável do paciente, da mais recente para a mais antiga
function buscarHistoricoResponsavel(mysqli $conexao, int $idPaciente): array {
    $sql = "SELECT historico.criado_em, admin.nome AS nome_admin, novo.nome AS nome_novo
            FROM HistoricoResponsavel historico
            JOIN Usuario admin ON admin.idUsuario = historico.id_admin
            JOIN Usuario novo  ON novo.idUsuario  = historico.id_novo_responsavel
            WHERE historico.Paciente_idpaciente = ?
            ORDER BY historico.criado_em DESC";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idPaciente);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ------------------------------------------------------------
// Consultas
// ------------------------------------------------------------

// depois de concluída, a consulta só pode ser desfeita ou editada por 3 horas
const JANELA_EDICAO_SEGUNDOS = 3 * 3600;

// consultas do paciente, com a anotação e se o pagamento já foi feito
function buscarSessoesDoPaciente(mysqli $conexao, int $idPaciente): array {
    $sql = "SELECT consulta.idconsulta, consulta.data, consulta.hora_inicio, consulta.hora_fim, consulta.status,
                   consulta.id_psicologo, consulta.concluida_em, consulta.confirmada_em,
                   anotacao.conteudo,
                   (SELECT COUNT(*) FROM Pagamento pg WHERE pg.Consulta_idconsulta = consulta.idconsulta AND pg.status = 'pago') AS pagamento_pago
            FROM Consulta consulta
            LEFT JOIN AnotacaoClinica anotacao ON anotacao.Consulta_idconsulta = consulta.idconsulta
            WHERE consulta.id_paciente = ?
            ORDER BY consulta.data DESC, consulta.hora_inicio DESC";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idPaciente);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// consulta da clínica, junto com o responsável do paciente
function buscarConsultaPorId(mysqli $conexao, int $idConsulta, int $idClinica): ?array {
    $sql = "SELECT consulta.*, paciente.idPsicologo AS id_responsavel
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            WHERE consulta.idconsulta = ? AND consulta.Clinica_idtable1 = ?";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("ii", $idConsulta, $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

// true se o psicólogo já tem consulta nesse horário (conta o intervalo entre consultas, em minutos)
function psicologoOcupado(mysqli $conexao, int $idPsicologo, string $data, string $inicio, string $fim,
                          int $intervalo = 0, ?int $ignorarConsulta = null): bool {
    $ignorar = $ignorarConsulta ?? 0;
    $segundos = $intervalo * 60;
    $sql = "SELECT 1 FROM Consulta
            WHERE id_psicologo = ? AND data = ? AND status <> 'cancelada'
              AND idconsulta <> ?
              AND hora_inicio < ADDTIME(?, SEC_TO_TIME(?))
              AND hora_fim > SUBTIME(?, SEC_TO_TIME(?))
            LIMIT 1";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("isisisi", $idPsicologo, $data, $ignorar, $fim, $segundos, $inicio, $segundos);
    $comando->execute();
    return $comando->get_result()->num_rows > 0;
}

// o pagamento é criado pelo trigger do banco
function inserirConsulta(mysqli $conexao, int $idClinica, int $idPaciente, int $idPsicologo,
                         string $data, string $inicio, string $fim): int {
    $sql = "INSERT INTO Consulta (data, hora_inicio, hora_fim, status, id_psicologo, id_paciente, Clinica_idtable1)
            VALUES (?, ?, ?, 'aguarda data', ?, ?, ?)";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("sssiii", $data, $inicio, $fim, $idPsicologo, $idPaciente, $idClinica);
    $comando->execute();
    return (int) $conexao->insert_id;
}

// remarcar: muda data e hora, tira a confirmação, volta para "aguarda data" e move o vencimento do pagamento (se ainda não foi pago)
function atualizarHorarioConsulta(mysqli $conexao, int $idConsulta, string $data, string $inicio, string $fim): void {
    $comando = $conexao->prepare("UPDATE Consulta SET data = ?, hora_inicio = ?, hora_fim = ?, status = 'aguarda data', concluida_em = NULL, confirmada_em = NULL WHERE idconsulta = ?");
    $comando->bind_param("sssi", $data, $inicio, $fim, $idConsulta);
    $comando->execute();

    $comando = $conexao->prepare("UPDATE Pagamento SET data_vencimento = ?, status = 'pendente' WHERE Consulta_idconsulta = ? AND status <> 'pago'");
    $comando->bind_param("si", $data, $idConsulta);
    $comando->execute();

    $comando = $conexao->prepare("UPDATE AnotacaoClinica SET data = ? WHERE Consulta_idconsulta = ?");
    $comando->bind_param("si", $data, $idConsulta);
    $comando->execute();
}

// $concluidaEm = null limpa o campo
function atualizarStatusConsulta(mysqli $conexao, int $idConsulta, string $status, ?string $concluidaEm): void {
    $comando = $conexao->prepare("UPDATE Consulta SET status = ?, concluida_em = ? WHERE idconsulta = ?");
    $comando->bind_param("ssi", $status, $concluidaEm, $idConsulta);
    $comando->execute();
}

// cancela mas deixa no histórico; pagamento pendente ou atrasado é apagado, o pago continua
function cancelarConsulta(mysqli $conexao, int $idConsulta): void {
    $comando = $conexao->prepare("DELETE FROM Pagamento WHERE Consulta_idconsulta = ? AND status <> 'pago'");
    $comando->bind_param("i", $idConsulta);
    $comando->execute();

    atualizarStatusConsulta($conexao, $idConsulta, 'cancelada', null);
}

// o que o usuário logado pode fazer com a consulta
function permissoesDaConsulta(array $consulta): array {
    $permissao = $_SESSION['permissao'];
    $idUsuario = (int) $_SESSION['id'];

    $aguardando = in_array($consulta['status'], ['aguarda data', 'atrasada'], true);
    $concluida  = $consulta['status'] === 'concluida';
    $jaComecou  = strtotime($consulta['data'] . ' ' . $consulta['hora_inicio']) <= time();
    $naJanela   = $concluida && !empty($consulta['concluida_em'])
                  && (time() - strtotime($consulta['concluida_em'])) <= JANELA_EDICAO_SEGUNDOS;

    $ehResponsavel = in_array($permissao, ['ADMIN', 'PSICOLOGO'], true)
                     && (int) $consulta['id_responsavel'] === $idUsuario;
    $podeGerenciarAgenda = in_array($permissao, ['ADMIN', 'RECEPCIONISTA'], true)
                     || ($permissao === 'PSICOLOGO' && (int) $consulta['id_psicologo'] === $idUsuario);

    return [
        'jaComecou'       => $jaComecou,
        'podeConcluir'    => $ehResponsavel && $aguardando && $jaComecou,
        'podeDesconcluir' => $ehResponsavel && $naJanela,
        'podeAnotar'      => $ehResponsavel && ($aguardando || $naJanela),
        'podeRemarcar'    => $podeGerenciarAgenda && $aguardando,
        'podeCancelar'    => $podeGerenciarAgenda && $aguardando,
        'podeConfirmar'   => $podeGerenciarAgenda && $aguardando && !$jaComecou,
    ];
}

function buscarAnotacaoDaConsulta(mysqli $conexao, int $idConsulta): ?array {
    $comando = $conexao->prepare("SELECT idAnotacaoClinica, conteudo FROM AnotacaoClinica WHERE Consulta_idconsulta = ? LIMIT 1");
    $comando->bind_param("i", $idConsulta);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

function inserirAnotacao(mysqli $conexao, int $idConsulta, int $idPaciente, int $idUsuario,
                         string $data, string $conteudo): void {
    $sql = "INSERT INTO AnotacaoClinica (data, conteudo, criadoEm, Consulta_idconsulta, Usuario_idUsuario, Paciente_idpaciente)
            VALUES (?, ?, CURDATE(), ?, ?, ?)";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("ssiii", $data, $conteudo, $idConsulta, $idUsuario, $idPaciente);
    $comando->execute();
}

function atualizarAnotacao(mysqli $conexao, int $idAnotacao, string $conteudo): void {
    $comando = $conexao->prepare("UPDATE AnotacaoClinica SET conteudo = ? WHERE idAnotacaoClinica = ?");
    $comando->bind_param("si", $conteudo, $idAnotacao);
    $comando->execute();
}

// ------------------------------------------------------------
// Pagamentos
// ------------------------------------------------------------

// pagamentos do paciente (aba do drawer)
function buscarPagamentosDoPaciente(mysqli $conexao, int $idPaciente): array {
    $sql = "SELECT idPagamento, Usuario_idUsuario, valor, status, data_vencimento, data_pagamento
            FROM Pagamento
            WHERE Paciente_idpaciente = ?
            ORDER BY data_vencimento DESC";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idPaciente);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function buscarPagamentoPorId(mysqli $conexao, int $idPagamento, int $idClinica): ?array {
    $sql = "SELECT pag.idPagamento, pag.status, pag.Usuario_idUsuario
            FROM Pagamento pag
            JOIN Paciente paciente ON paciente.idpaciente = pag.Paciente_idpaciente
            WHERE pag.idPagamento = ? AND paciente.idClinica = ?";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("ii", $idPagamento, $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

// admin e recepcionista em qualquer um; psicólogo só nos seus
function podeReceberPagamento(array $pagamento): bool {
    $permissao = $_SESSION['permissao'];
    if (in_array($permissao, ['ADMIN', 'RECEPCIONISTA'], true)) return true;
    return $permissao === 'PSICOLOGO' && (int) $pagamento['Usuario_idUsuario'] === (int) $_SESSION['id'];
}

function marcarPagamentoRecebido(mysqli $conexao, int $idPagamento, string $dataPagamento): void {
    $comando = $conexao->prepare("UPDATE Pagamento SET status = 'pago', data_pagamento = ? WHERE idPagamento = ?");
    $comando->bind_param("si", $dataPagamento, $idPagamento);
    $comando->execute();
}

// volta para pendente; quem decide se vira atrasado é a sincronização por data
function desfazerPagamentoRecebido(mysqli $conexao, int $idPagamento): void {
    $comando = $conexao->prepare("UPDATE Pagamento SET status = 'pendente', data_pagamento = NULL WHERE idPagamento = ?");
    $comando->bind_param("i", $idPagamento);
    $comando->execute();
}

// números dos cartões do financeiro
function resumoFinanceiro(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $sql = "SELECT
                COALESCE(SUM(CASE WHEN pag.status = 'pago'
                                   AND YEAR(pag.data_pagamento)  = YEAR(CURDATE())
                                   AND MONTH(pag.data_pagamento) = MONTH(CURDATE())
                                  THEN pag.valor END), 0) AS receita,
                COALESCE(SUM(CASE WHEN pag.status = 'pendente' THEN pag.valor END), 0) AS pendente,
                COALESCE(SUM(CASE WHEN pag.status = 'atrasado' THEN pag.valor END), 0) AS atrasado_valor,
                COUNT(CASE WHEN pag.status = 'atrasado' THEN 1 END) AS atrasados,
                COUNT(CASE WHEN pag.status = 'pendente'
                            AND pag.data_vencimento = CURDATE() THEN 1 END) AS vencendo_hoje
            FROM Pagamento pag
            JOIN Paciente paciente ON paciente.idpaciente = pag.Paciente_idpaciente
            JOIN Consulta consulta ON consulta.idconsulta = pag.Consulta_idconsulta
            WHERE paciente.idClinica = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_assoc();
}

// tabelas do financeiro. $tipo: recebidos, pendentes, atrasados ou vencendo
function buscarPagamentos(mysqli $conexao, int $idClinica, string $tipo, ?int $idPsicologo = null): array {
    $filtros = [
        'recebidos' => "pag.status = 'pago'
                        AND YEAR(pag.data_pagamento)  = YEAR(CURDATE())
                        AND MONTH(pag.data_pagamento) = MONTH(CURDATE())",
        'pendentes' => "pag.status = 'pendente'",
        'atrasados' => "pag.status = 'atrasado'",
        'vencendo'  => "pag.status = 'pendente' AND pag.data_vencimento = CURDATE()",
    ];
    if (!isset($filtros[$tipo])) {
        return [];
    }

    $sql = "SELECT pag.idPagamento, pag.valor, pag.data_vencimento, pag.data_pagamento,
                   paciente.idpaciente, paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo
            FROM Pagamento pag
            JOIN Paciente paciente ON paciente.idpaciente = pag.Paciente_idpaciente
            JOIN Consulta consulta ON consulta.idconsulta = pag.Consulta_idconsulta
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE paciente.idClinica = ?
              AND (? IS NULL OR consulta.id_psicologo = ?)
              AND {$filtros[$tipo]}
            ORDER BY pag.data_vencimento";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ------------------------------------------------------------
// Relatórios
// ------------------------------------------------------------

// receita, pendente e atrasado de cada mês do ano (12 posições). Sem psicólogo, soma a clínica toda
function receitaMensal(mysqli $conexao, int $idClinica, int $ano, ?int $idPsicologo = null): array {
    $tipos = [
        'receita'  => ["pag.status = 'pago'",     'pag.data_pagamento'],
        'pendente' => ["pag.status = 'pendente'", 'pag.data_vencimento'],
        'atrasado' => ["pag.status = 'atrasado'", 'pag.data_vencimento'],
    ];

    $resultado = [];
    foreach ($tipos as $chave => [$condicao, $coluna]) {
        $sql = "SELECT MONTH($coluna) AS mes, SUM(pag.valor) AS total
                FROM Pagamento pag
                JOIN Paciente paciente ON paciente.idpaciente = pag.Paciente_idpaciente
                JOIN Consulta consulta ON consulta.idconsulta = pag.Consulta_idconsulta
                WHERE paciente.idClinica = ?
                  AND (? IS NULL OR consulta.id_psicologo = ?)
                  AND $condicao
                  AND YEAR($coluna) = ?
                GROUP BY mes";

        $comando = $conexao->prepare($sql);
        $comando->bind_param("iiii", $idClinica, $idPsicologo, $idPsicologo, $ano);
        $comando->execute();

        $meses = array_fill(0, 12, 0.0);
        foreach ($comando->get_result()->fetch_all(MYSQLI_ASSOC) as $linha) {
            $meses[(int) $linha['mes'] - 1] = (float) $linha['total'];
        }
        $resultado[$chave] = $meses;
    }
    return $resultado;
}

// total, concluídas e canceladas do mês atual
function resumoConsultasDoMes(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
    $sql = "SELECT COUNT(*) AS total,
                   COUNT(CASE WHEN status = 'concluida' THEN 1 END) AS concluidas,
                   COUNT(CASE WHEN status = 'cancelada' THEN 1 END) AS canceladas
            FROM Consulta
            WHERE Clinica_idtable1 = ?
              AND (? IS NULL OR id_psicologo = ?)
              AND YEAR(data) = YEAR(CURDATE())
              AND MONTH(data) = MONTH(CURDATE())";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_assoc();
}

function buscarPercentualRepasse(mysqli $conexao, int $idClinica): float {
    $sql = "SELECT percentual_repasse FROM Clinica WHERE idtable1 = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return (float) $comando->get_result()->fetch_row()[0];
}

// ------------------------------------------------------------
// Usuários
// ------------------------------------------------------------

// psicólogos e admins da clínica (admin também atende)
function buscarPsicologosDaClinica(mysqli $conexao, int $idClinica, bool $incluirInativos = false): array {
    $filtroAtivo = $incluirInativos ? '' : 'AND ativo = 1';
    $sql = "SELECT idUsuario, nome
            FROM Usuario
            WHERE id_clinica = ?
              AND permissao IN ('ADMIN', 'PSICOLOGO')
              {$filtroAtivo}
            ORDER BY nome";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// todos os usuários da clínica, sem a senha
function buscarUsuariosDaClinica(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT idUsuario, nome, email, telefone, crp, permissao, ativo
            FROM Usuario
            WHERE id_clinica = ?
            ORDER BY ativo DESC, nome";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// dados de um usuário, sem a senha
function buscarUsuarioPorId(mysqli $conexao, int $idUsuario): ?array {
    $sql = "SELECT idUsuario, nome, email, telefone, crp, permissao,
                   duracao_consulta, intervalo_consulta
            FROM Usuario
            WHERE idUsuario = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idUsuario);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

function buscarUsuarioNaClinica(mysqli $conexao, int $idUsuario, int $idClinica): ?array {
    $comando = $conexao->prepare("SELECT idUsuario, nome, email, permissao, ativo FROM Usuario WHERE idUsuario = ? AND id_clinica = ?");
    $comando->bind_param("ii", $idUsuario, $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

// o e-mail é o login, então não pode repetir
function emailEmUso(mysqli $conexao, string $email, int $ignorarUsuario = 0): bool {
    $comando = $conexao->prepare("SELECT 1 FROM Usuario WHERE email = ? AND idUsuario <> ? LIMIT 1");
    $comando->bind_param("si", $email, $ignorarUsuario);
    $comando->execute();
    return $comando->get_result()->num_rows > 0;
}

function inserirUsuario(mysqli $conexao, int $idClinica, string $nome, string $email, string $senhaHash,
                        ?string $telefone, ?string $crp, string $permissao): int {
    $sql = "INSERT INTO Usuario (nome, email, senha, telefone, crp, permissao, id_clinica) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("ssssssi", $nome, $email, $senhaHash, $telefone, $crp, $permissao, $idClinica);
    $comando->execute();
    return (int) $conexao->insert_id;
}

// $senhaHash = null mantém a senha atual
function atualizarUsuario(mysqli $conexao, int $idUsuario, string $nome, string $email, ?string $telefone,
                          ?string $crp, string $permissao, ?string $senhaHash): void {
    if ($senhaHash !== null) {
        $comando = $conexao->prepare("UPDATE Usuario SET nome = ?, email = ?, telefone = ?, crp = ?, permissao = ?, senha = ? WHERE idUsuario = ?");
        $comando->bind_param("ssssssi", $nome, $email, $telefone, $crp, $permissao, $senhaHash, $idUsuario);
    } else {
        $comando = $conexao->prepare("UPDATE Usuario SET nome = ?, email = ?, telefone = ?, crp = ?, permissao = ? WHERE idUsuario = ?");
        $comando->bind_param("sssssi", $nome, $email, $telefone, $crp, $permissao, $idUsuario);
    }
    $comando->execute();
}

// ativar e desativar (usuário nunca é apagado)
function alterarAtivoUsuario(mysqli $conexao, int $idUsuario, bool $ativo): void {
    $valor = $ativo ? 1 : 0;
    $comando = $conexao->prepare("UPDATE Usuario SET ativo = ? WHERE idUsuario = ?");
    $comando->bind_param("ii", $valor, $idUsuario);
    $comando->execute();
}

// o próprio usuário alterando os seus dados
function atualizarPerfil(mysqli $conexao, int $idUsuario, string $nome, string $email, ?string $telefone, ?string $crp): void {
    $comando = $conexao->prepare("UPDATE Usuario SET nome = ?, email = ?, telefone = ?, crp = ? WHERE idUsuario = ?");
    $comando->bind_param("ssssi", $nome, $email, $telefone, $crp, $idUsuario);
    $comando->execute();
}

// ------------------------------------------------------------
// Configurações
// ------------------------------------------------------------

// dados da clínica (sem o logo, que é pesado)
function buscarClinica(mysqli $conexao, int $idClinica): ?array {
    $sql = "SELECT nome, CNPJ, Endereco, telefone, percentual_repasse
            FROM Clinica
            WHERE idtable1 = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_assoc() ?: null;
}

function atualizarDadosClinica(mysqli $conexao, int $idClinica, string $nome, ?string $cnpj, ?string $endereco, ?string $telefone): void {
    $comando = $conexao->prepare("UPDATE Clinica SET nome = ?, CNPJ = ?, Endereco = ?, telefone = ? WHERE idtable1 = ?");
    $comando->bind_param("ssssi", $nome, $cnpj, $endereco, $telefone, $idClinica);
    $comando->execute();
}

function atualizarPercentualRepasse(mysqli $conexao, int $idClinica, float $percentual): void {
    $comando = $conexao->prepare("UPDATE Clinica SET percentual_repasse = ? WHERE idtable1 = ?");
    $comando->bind_param("di", $percentual, $idClinica);
    $comando->execute();
}

// valor padrão da consulta do profissional
function buscarValorConsulta(mysqli $conexao, int $idUsuario): float {
    $sql = "SELECT valor_consulta FROM Usuario WHERE idUsuario = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idUsuario);
    $comando->execute();
    return (float) $comando->get_result()->fetch_row()[0];
}

function salvarValorConsulta(mysqli $conexao, int $idUsuario, float $valor): void {
    $sql = "UPDATE Usuario SET valor_consulta = ? WHERE idUsuario = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("di", $valor, $idUsuario);
    $comando->execute();
}

// devolve [dia_semana => [inicio, fim]], com 1 = segunda e 7 = domingo
function buscarHorariosAtendimento(mysqli $conexao, int $idUsuario): array {
    $sql = "SELECT dia_semana, hora_inicio, hora_fim
            FROM HorarioAtendimento
            WHERE Usuario_idUsuario = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idUsuario);
    $comando->execute();

    $horarios = [];
    foreach ($comando->get_result()->fetch_all(MYSQLI_ASSOC) as $linha) {
        $horarios[(int) $linha['dia_semana']] = [
            'inicio' => substr($linha['hora_inicio'], 0, 5),
            'fim'    => substr($linha['hora_fim'], 0, 5),
        ];
    }
    return $horarios;
}

// apaga os horários antigos e grava os novos. $horarios = [dia => [inicio, fim]]
function salvarHorariosAtendimento(mysqli $conexao, int $idUsuario, array $horarios, int $duracao, int $intervalo): void {
    $comando = $conexao->prepare("DELETE FROM HorarioAtendimento WHERE Usuario_idUsuario = ?");
    $comando->bind_param("i", $idUsuario);
    $comando->execute();

    $comando = $conexao->prepare("INSERT INTO HorarioAtendimento (dia_semana, hora_inicio, hora_fim, Usuario_idUsuario) VALUES (?, ?, ?, ?)");
    foreach ($horarios as $dia => [$inicio, $fim]) {
        $dia = (int) $dia;
        $comando->bind_param("issi", $dia, $inicio, $fim, $idUsuario);
        $comando->execute();
    }

    $comando = $conexao->prepare("UPDATE Usuario SET duracao_consulta = ?, intervalo_consulta = ? WHERE idUsuario = ?");
    $comando->bind_param("iii", $duracao, $intervalo, $idUsuario);
    $comando->execute();
}

// ------------------------------------------------------------
// Notificações
// ------------------------------------------------------------

// se falhar, só não notifica (a ação principal não é afetada)
function criarNotificacao(mysqli $conexao, int $idDestinatario, string $mensagem, ?int $idPaciente = null,
                          string $tipo = 'geral', ?int $idConsulta = null): void {
    try {
        $agora = date('Y-m-d H:i:s');
        $mensagem = mb_substr($mensagem, 0, 250);
        $comando = $conexao->prepare("INSERT INTO Notificacao (mensagem, criado_em, Usuario_idUsuario, id_paciente, tipo, id_consulta)
                                      VALUES (?, ?, ?, ?, ?, ?)");
        $comando->bind_param("ssiisi", $mensagem, $agora, $idDestinatario, $idPaciente, $tipo, $idConsulta);
        $comando->execute();
    } catch (Throwable $e) {
        // sem notificação, mas a ação principal continua
    }
}

function buscarNotificacoes(mysqli $conexao, int $idUsuario, int $limite = 20): array {
    $comando = $conexao->prepare("SELECT idNotificacao, mensagem, criado_em, lida, id_paciente
                                  FROM Notificacao WHERE Usuario_idUsuario = ?
                                  ORDER BY criado_em DESC, idNotificacao DESC LIMIT ?");
    $comando->bind_param("ii", $idUsuario, $limite);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function contarNotificacoesNaoLidas(mysqli $conexao, int $idUsuario): int {
    $comando = $conexao->prepare("SELECT COUNT(*) AS total FROM Notificacao WHERE Usuario_idUsuario = ? AND lida = 0");
    $comando->bind_param("i", $idUsuario);
    $comando->execute();
    return (int) $comando->get_result()->fetch_assoc()['total'];
}

// $idNotificacao = null marca todas
function marcarNotificacoesComoLidas(mysqli $conexao, int $idUsuario, ?int $idNotificacao): void {
    if ($idNotificacao === null) {
        $comando = $conexao->prepare("UPDATE Notificacao SET lida = 1 WHERE Usuario_idUsuario = ?");
        $comando->bind_param("i", $idUsuario);
    } else {
        $comando = $conexao->prepare("UPDATE Notificacao SET lida = 1 WHERE Usuario_idUsuario = ? AND idNotificacao = ?");
        $comando->bind_param("ii", $idUsuario, $idNotificacao);
    }
    $comando->execute();
}

// avisa uma vez por consulta que passou sem anotação (só dos últimos 7 dias, para não trazer o histórico antigo)
function gerarNotificacoesDeAnotacaoPendente(mysqli $conexao): void {
    $agora  = date('Y-m-d H:i:s');
    $limite = date('Y-m-d', strtotime('-7 days'));
    $sql = "INSERT INTO Notificacao (mensagem, criado_em, Usuario_idUsuario, id_paciente, tipo, id_consulta)
            SELECT CONCAT('Anotação pendente: consulta de ', paciente.nome, ' em ',
                          DATE_FORMAT(consulta.data, '%d/%m'), ' às ', TIME_FORMAT(consulta.hora_inicio, '%H:%i'), '.'),
                   ?, paciente.idPsicologo, paciente.idpaciente, 'anotacao_pendente', consulta.idconsulta
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            LEFT JOIN AnotacaoClinica anotacao ON anotacao.Consulta_idconsulta = consulta.idconsulta
            WHERE consulta.status IN ('aguarda data', 'atrasada')
              AND anotacao.idAnotacaoClinica IS NULL
              AND consulta.data >= ?
              AND TIMESTAMP(consulta.data, consulta.hora_inicio) <= ?
              AND NOT EXISTS (SELECT 1 FROM Notificacao n
                              WHERE n.tipo = 'anotacao_pendente' AND n.id_consulta = consulta.idconsulta)";
    $comando = $conexao->prepare($sql);
    $comando->bind_param("sss", $agora, $limite, $agora);
    $comando->execute();
}
