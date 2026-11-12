<?php
// ====================================================================
// COMUM
// ====================================================================

function formatarHora(string $hora): string {
    return date('H\hi', strtotime($hora));
}

// Psicólogos e admins da clínica (os admins também atendem pacientes).
// Usada em: filtro de pacientes, filtro do financeiro, agenda e drawer.
function buscarPsicologosDaClinica(mysqli $conexao, int $idClinica): array {
    $sql = "SELECT idUsuario, nome
            FROM Usuario
            WHERE id_clinica = ?
              AND permissao IN ('ADMIN', 'PSICOLOGO')
            ORDER BY nome";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}


// ====================================================================
// DASHBOARD
// ====================================================================

// Tabela "Consultas de hoje"
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
            ORDER BY consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Tabela "Aguardando confirmação"
function buscarConsultasAtrasadas(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
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
              AND consulta.status = 'atrasada'
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Tabela "Anotações pendentes de lançar"
function buscarConsultasSemAnotacao(mysqli $conexao, int $idClinica, ?int $idPsicologo = null): array {
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
              AND consulta.status = 'concluida'
              AND anotacao.idAnotacaoClinica IS NULL
            ORDER BY consulta.data, consulta.hora_inicio";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("iii", $idClinica, $idPsicologo, $idPsicologo);
    $comando->execute();
    return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Tabela "Consultas concluídas neste mês"
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

// Número do cartão "Consultas concluídas neste mês"
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


// ====================================================================
// PACIENTES
// ====================================================================

const STATUS_PACIENTE_VALIDOS = ['ativo', 'em_espera', 'pausado', 'encerrado', 'inativo'];

// Tabelas da página de pacientes (com filtros opcionais)
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

// Números dos cartões da página de pacientes
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

// Barra de busca do header (api/paciente_buscar.php)
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


// ====================================================================
// DRAWER DO PACIENTE
// ====================================================================

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

// Aba "Anotações" (o texto só deve ser enviado ao psicólogo responsável)
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

// Aba "Pagamentos"
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


// ====================================================================
// FINANCEIRO
// ====================================================================

// Os 4 cartões: receita do mês, pendente, atrasados e vencendo hoje
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

// As 4 tabelas do financeiro.
// $tipo: 'recebidos', 'pendentes', 'atrasados' ou 'vencendo'
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

// ====================================================================
// CONFIGURAÇÕES
// ====================================================================

// Valor padrão da consulta do profissional
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

// ====================================================================
// RELATÓRIOS
// ====================================================================

// Receita e pendente de cada mês do ano (12 posições cada).
// Se $idPsicologo for null, soma a clínica toda.
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

// Consultas do mês atual: total, concluídas e canceladas
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

// Percentual de repasse definido pela clínica
function buscarPercentualRepasse(mysqli $conexao, int $idClinica): float {
    $sql = "SELECT percentual_repasse FROM Clinica WHERE idtable1 = ?";

    $comando = $conexao->prepare($sql);
    $comando->bind_param("i", $idClinica);
    $comando->execute();
    return (float) $comando->get_result()->fetch_row()[0];
}