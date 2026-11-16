<?php
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/api.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['erro' => 'Método não permitido']);
if ($_SESSION['permissao'] !== 'ADMIN') responder(403, ['erro' => 'Só o administrador pode alterar os dados da clínica.']);

$idClinica = (int) $_SESSION['id_clinica'];
$secao     = $_POST['secao'] ?? '';

if ($secao === 'repasse') {
    $percentual = filter_var($_POST['percentual'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($percentual === false || $percentual < 0 || $percentual > 100) {
        responder(422, ['erro' => 'O percentual deve estar entre 0 e 100.']);
    }
    atualizarPercentualRepasse($conexao, $idClinica, round($percentual, 2));
    responder(200, ['ok' => true]);
}

if ($secao === 'dados') {
    $atual    = buscarClinica($conexao, $idClinica);
    $nome     = trim($_POST['nome'] ?? '');
    $cnpj     = trim($_POST['cnpj'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '' || mb_strlen($nome) > 45) responder(422, ['erro' => 'Informe o nome da clínica (até 45 caracteres).']);
    if (mb_strlen($endereco) > 60)  responder(422, ['erro' => 'O endereço pode ter no máximo 60 caracteres.']);
    if (mb_strlen($telefone) > 45)  responder(422, ['erro' => 'Telefone muito longo.']);

    if ($cnpj !== '' && $cnpj !== ($atual['CNPJ'] ?? '')) {
        $d = preg_replace('/\D/', '', $cnpj);
        if (!cnpjValido($d)) responder(422, ['erro' => 'CNPJ inválido.']);
        $cnpj = substr($d,0,2).'.'.substr($d,2,3).'.'.substr($d,5,3).'/'.substr($d,8,4).'-'.substr($d,12,2);
    }

    atualizarDadosClinica($conexao, $idClinica, $nome,
        $cnpj !== '' ? $cnpj : null,
        $endereco !== '' ? $endereco : null,
        $telefone !== '' ? $telefone : null);
    responder(200, ['ok' => true]);
}

responder(422, ['erro' => 'Seção inválida.']);
