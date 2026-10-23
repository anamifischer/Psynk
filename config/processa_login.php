<?php
require_once "conexao.php";
require_once "sessao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/auth/login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

$sql = "SELECT idUsuario, nome, senha, permissao, id_clinica
        FROM Usuario
        WHERE email = ?
        LIMIT 1";

$consulta = $conexao->prepare($sql);
$consulta->bind_param("s", $email);
$consulta->execute();
$usuario = $consulta->get_result()->fetch_assoc();

if ($usuario
    && password_verify($senha, $usuario["senha"])
    && isset(PAGINAS_POR_PERMISSAO[$usuario["permissao"]])) {

    session_regenerate_id(true);

    $_SESSION["id"]         = $usuario["idUsuario"];
    $_SESSION["nome"]       = $usuario["nome"];
    $_SESSION["permissao"]  = $usuario["permissao"];
    $_SESSION["id_clinica"] = $usuario["id_clinica"];

    header("Location: " . PAGINAS_POR_PERMISSAO[$usuario["permissao"]]);
    exit;
}

// Mesma mensagem para e-mail inexistente ou senha errada
header("Location: ../pages/auth/login.php?erro=1");
exit;