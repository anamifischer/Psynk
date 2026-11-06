<?php

// ============ CONSULTAS ============
function buscarConsultasDeHoje(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT consulta.idconsulta,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.hora_inicio,
                   consulta.status
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE consulta.Clinica_idtable1 = ?
              AND consulta.data = CURDATE()
            ORDER BY consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function buscarConsultasAtrasadas(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT consulta.idconsulta,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.data,
                   consulta.hora_inicio
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            WHERE consulta.Clinica_idtable1 = ?
              AND consulta.status = 'atrasada'
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function buscarConsultasSemAnotacao(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT consulta.idconsulta,
                   paciente.nome AS nome_paciente,
                   psicologo.nome AS nome_psicologo,
                   consulta.data,
                   consulta.hora_inicio
            FROM Consulta consulta
            JOIN Paciente paciente ON paciente.idpaciente = consulta.id_paciente
            JOIN Usuario psicologo ON psicologo.idUsuario = consulta.id_psicologo
            LEFT JOIN AnotacaoClinica anotacao ON anotacao.Consulta_idconsulta = consulta.idconsulta
            WHERE consulta.Clinica_idtable1 = ?
              AND consulta.status = 'concluida'
              AND anotacao.idAnotacaoClinica IS NULL
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function contarConsultasConcluidasNoMes(mysqli $conexao, int $idClinica): int {
    $sql = "SELECT COUNT(*)
            FROM Consulta
            WHERE Clinica_idtable1 = ?
              AND status = 'concluida'
              AND YEAR(data) = YEAR(CURDATE())
              AND MONTH(data) = MONTH(CURDATE())";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return (int) $comando->get_result()->fetch_row()[0];
}

function formatarHora(string $hora): string {
    return date('H\hi', strtotime($hora));
}


// ============ PACIENTES ============
const STATUS_PACIENTE_VALIDOS = ['ativo', 'em_espera', 'pausado', 'encerrado', 'inativo'];

function buscarPacientes(mysqli $conexao, int $idClinica, ?int $idPsicologo = null, ?string $status = null): array {
    $sql = "SELECT paciente.idpaciente,
                   paciente.nome AS nome_paciente,
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

function buscarPsicologosDaClinica(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT idUsuario, nome
            FROM Usuario
            WHERE id_clinica = ?
              AND permissao = 'PSICOLOGO'
            ORDER BY nome";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============ DRAWER DO PACIENTE ============
function buscarPacientePorId(mysqli $conexao, int $idPaciente, int $idClinica): ?array {
    $sql = "SELECT paciente.idpaciente, paciente.nome, paciente.telefone, paciente.endereco,
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

function buscarSessoesDoPaciente(mysqli $conexao, int $idPaciente): array {
    $sql = "SELECT consulta.idconsulta, consulta.data, consulta.hora_inicio, consulta.status,
                   anotacao.conteudo
            FROM Consulta consulta
            LEFT JOIN AnotacaoClinica anotacao ON anotacao.Consulta_idconsulta = consulta.idconsulta
            WHERE consulta.id_paciente = ?
            ORDER BY consulta.data DESC, consulta.hora_inicio DESC";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idPaciente);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

function buscarPagamentosDoPaciente(mysqli $conexao, int $idPaciente): array {
    $sql = "SELECT valor, status, data_vencimento, data_pagamento
            FROM Pagamento
            WHERE Paciente_idpaciente = ?
            ORDER BY data_vencimento DESC";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idPaciente);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

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