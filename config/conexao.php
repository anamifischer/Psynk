<?php

$servidor = "db";            // nome do serviço do MySQL no docker-compose.yml
$usuario  = "psynk_user";
$senha    = "psynk_pass";
$banco    = "psynk";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

// Deixa o status dos pagamentos de acordo com a data (a cada acesso):
// até o dia do vencimento = pendente; depois dele = atrasado. Pago nunca muda.
function sincronizarStatusPagamentos(mysqli $conexao): void {
    $hoje = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');

    $comando = $conexao->prepare("UPDATE Pagamento SET status = 'atrasado' WHERE status = 'pendente' AND data_vencimento < ?");
    $comando->bind_param("s", $hoje);
    $comando->execute();

    $comando = $conexao->prepare("UPDATE Pagamento SET status = 'pendente' WHERE status = 'atrasado' AND data_vencimento >= ?");
    $comando->bind_param("s", $hoje);
    $comando->execute();
}

// Usuário desativado perde o acesso na hora, mesmo que esteja logado
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id'])) {
    $verificaAtivo = $conexao->prepare("SELECT ativo FROM Usuario WHERE idUsuario = ?");
    $verificaAtivo->bind_param("i", $_SESSION['id']);
    $verificaAtivo->execute();
    $linhaAtivo = $verificaAtivo->get_result()->fetch_assoc();
    if (!$linhaAtivo || (int) $linhaAtivo['ativo'] !== 1) {
        $_SESSION = [];
        session_destroy();
        header('Location: /public/login.php');
        exit;
    }
}

sincronizarStatusPagamentos($conexao);

// Avisos automáticos de anotação pendente (ver notificações no sino do cabeçalho)
if (function_exists('gerarNotificacoesDeAnotacaoPendente') === false) {
    // a função mora em app/includes/consultas.php; carregamos só se ainda não estiver disponível
    require_once __DIR__ . "/../app/includes/consultas.php";
}
try {
    gerarNotificacoesDeAnotacaoPendente($conexao);
} catch (Throwable $e) {
    // não deixa uma falha de aviso derrubar a página
}
