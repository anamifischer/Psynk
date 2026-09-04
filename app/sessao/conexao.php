<?php

$host   = "localhost";
$banco  = "mydb";
$usuario = "root";
$senha  = "";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro ao conectar: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");