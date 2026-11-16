<?php
// Coisas que os arquivos de app/api usam em comum
require_once __DIR__ . "/consultas.php";

// responde em JSON e encerra
function responder(int $codigo, array $corpo): void {
    http_response_code($codigo);
    echo json_encode($corpo, JSON_UNESCAPED_UNICODE);
    exit;
}

// ------------------------------------------------------------
// Documentos
// ------------------------------------------------------------

function cpfValido(string $digitos): bool {
    if (strlen($digitos) !== 11 || preg_match('/^(\d)\1{10}$/', $digitos)) return false;
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) $soma += (int) $digitos[$i] * ($t + 1 - $i);
        if ((int) $digitos[$t] !== ((10 * $soma) % 11) % 10) return false;
    }
    return true;
}

function cnpjValido(string $d): bool {
    if (strlen($d) !== 14 || preg_match('/^(\d)\1{13}$/', $d)) return false;
    foreach ([12, 13] as $t) {
        $pesos = $t === 12 ? [5,4,3,2,9,8,7,6,5,4,3,2] : [6,5,4,3,2,9,8,7,6,5,4,3,2];
        $soma = 0;
        for ($i = 0; $i < $t; $i++) $soma += (int) $d[$i] * $pesos[$i];
        $resto = $soma % 11;
        if ((int) $d[$t] !== ($resto < 2 ? 0 : 11 - $resto)) return false;
    }
    return true;
}

// ------------------------------------------------------------
// Consulta (usado ao agendar e ao remarcar)
// ------------------------------------------------------------

// confere data, hora e duração. Devolve [inicio, fim] como HH:MM:SS
function validarDataHoraConsulta(string $data, string $hora, int $duracao): array {
    $dt = DateTime::createFromFormat('Y-m-d', $data);
    if (!$dt || $dt->format('Y-m-d') !== $data) responder(422, ['erro' => 'Data inválida.']);
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) responder(422, ['erro' => 'Horário inválido.']);
    if (!in_array($duracao, [30, 45, 50, 60], true)) responder(422, ['erro' => 'Duração inválida.']);

    $inicio = $hora . ':00';
    $fim    = date('H:i:s', strtotime($data . ' ' . $inicio) + $duracao * 60);
    if ($fim <= $inicio) responder(422, ['erro' => 'A consulta não pode passar da meia-noite.']);
    if (strtotime($data . ' ' . $inicio) < time()) responder(422, ['erro' => 'A consulta não pode ser no passado.']);

    return [$inicio, $fim];
}

// o psicólogo atende nesse dia e horário e não tem outra consulta em cima?
function validarDisponibilidade(mysqli $conexao, int $idPsicologo, string $data, string $hora, string $inicio,
                                string $fim, ?int $ignorarConsulta = null): void {
    $dia      = (int) date('N', strtotime($data));
    $horarios = buscarHorariosAtendimento($conexao, $idPsicologo);
    if (!isset($horarios[$dia])) responder(422, ['erro' => 'O psicólogo não atende nesse dia da semana.']);
    if ($hora < $horarios[$dia]['inicio'] || substr($fim, 0, 5) > $horarios[$dia]['fim']) {
        responder(422, ['erro' => 'Fora do horário de atendimento (' . $horarios[$dia]['inicio'] . ' às ' . $horarios[$dia]['fim'] . ').']);
    }

    $comando = $conexao->prepare("SELECT intervalo_consulta FROM Usuario WHERE idUsuario = ?");
    $comando->bind_param("i", $idPsicologo);
    $comando->execute();
    $intervalo = (int) ($comando->get_result()->fetch_assoc()['intervalo_consulta'] ?? 0);

    if (psicologoOcupado($conexao, $idPsicologo, $data, $inicio, $fim, $intervalo, $ignorarConsulta)) {
        responder(409, ['erro' => 'Horário indisponível: já há consulta nesse período'
            . ($intervalo > 0 ? " (intervalo mínimo de {$intervalo} min entre consultas)." : '.')]);
    }
}

// ------------------------------------------------------------
// Paciente (usado ao cadastrar e ao atualizar)
// ------------------------------------------------------------

// lê e valida os campos do paciente que vêm por POST
function lerDadosPaciente(): array {
    $nome        = trim($_POST['nome'] ?? '');
    $status      = $_POST['status'] ?? '';
    $telefone    = trim($_POST['telefone'] ?? '');
    $endereco    = trim($_POST['endereco'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');
    $nascimento  = trim($_POST['nascimento'] ?? '');

    if ($nome === '' || mb_strlen($nome) > 45) responder(422, ['erro' => 'Informe o nome (até 45 caracteres).']);
    if (!in_array($status, STATUS_PACIENTE_VALIDOS, true)) responder(422, ['erro' => 'Status inválido.']);
    if ($telefone === '' || mb_strlen($telefone) > 20) responder(422, ['erro' => 'Informe o telefone (até 20 caracteres).']);
    if (mb_strlen($endereco) > 60) responder(422, ['erro' => 'O endereço pode ter no máximo 60 caracteres.']);
    if (mb_strlen($observacoes) > 5000) responder(422, ['erro' => 'Observações muito longas.']);

    if ($nascimento === '') {
        $nascimento = null;
    } else {
        $dt = DateTime::createFromFormat('Y-m-d', $nascimento);
        if (!$dt || $dt->format('Y-m-d') !== $nascimento || $nascimento > date('Y-m-d')) {
            responder(422, ['erro' => 'Data de nascimento inválida.']);
        }
    }

    return [
        'nome'        => $nome,
        'status'      => $status,
        'telefone'    => $telefone,
        'endereco'    => $endereco !== '' ? $endereco : null,
        'observacoes' => $observacoes !== '' ? $observacoes : null,
        'nascimento'  => $nascimento,
    ];
}
