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