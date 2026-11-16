<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);

$permissao = $_SESSION['permissao'];
$idUsuario = (int) $_SESSION['id'];
$idClinica = (int) $_SESSION['id_clinica'];

if (!in_array($permissao, ['ADMIN', 'PSICOLOGO', 'RECEPCIONISTA'], true)) responder(403, ['erro' => 'Sem permissão.']);

$dados      = lerDadosPaciente();
$cpfDigitos = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$idResp     = (int) ($_POST['id_psicologo'] ?? 0);

if (!cpfValido($cpfDigitos)) responder(422, ['erro' => 'CPF inválido.']);

// psicólogo só cadastra para si; admin e recepcionista escolhem
if ($permissao === 'PSICOLOGO') $idResp = $idUsuario;
$responsavelValido = false;
foreach (buscarPsicologosDaClinica($conexao, $idClinica) as $ps) {
    if ((int) $ps['idUsuario'] === $idResp) $responsavelValido = true;
}
if (!$responsavelValido) responder(422, ['erro' => 'Escolha o psicólogo responsável.']);

// o CPF não pode repetir na clínica
$comando = $conexao->prepare("SELECT 1 FROM Paciente WHERE idClinica = ? AND REPLACE(REPLACE(cpf, '.', ''), '-', '') = ? LIMIT 1");
$comando->bind_param("is", $idClinica, $cpfDigitos);
$comando->execute();
if ($comando->get_result()->num_rows > 0) responder(409, ['erro' => 'Já existe um paciente com esse CPF.']);

$cpf = substr($cpfDigitos, 0, 3) . '.' . substr($cpfDigitos, 3, 3) . '.' . substr($cpfDigitos, 6, 3) . '-' . substr($cpfDigitos, 9, 2);

try {
    $id = inserirPaciente($conexao, $idClinica, $idResp, $dados['nome'], $cpf, $dados['status'], $dados['telefone'],
                          $dados['endereco'], $dados['observacoes'], $dados['nascimento']);
} catch (Throwable $e) {
    responder(500, ['erro' => 'Não foi possível cadastrar o paciente.']);
}

responder(200, ['ok' => true, 'id' => $id]);
