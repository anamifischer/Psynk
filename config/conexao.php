<?php

$servidor = "mysql";
$usuario = "root";
$senha = "1234";
$banco = "mydb";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

?>