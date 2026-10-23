<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const PAGINAS_POR_PERMISSAO = [
    "ADMIN"         => "../pages/admin/dashboard.php",
    "PSICOLOGO"     => "../pages/psicologo/dashboard.php",
    "RECEPCIONISTA" => "../pages/recepcionista/dashboard.php",
];

function verificarLogin() {
    if (!isset($_SESSION["id"])) {
        header("Location: ../pages/auth/login.php");
        exit;
    }
}

function verificarPermissao(array $permitidas) {
    verificarLogin();

    if (!in_array($_SESSION["permissao"], $permitidas, true)) {
        $destino = PAGINAS_POR_PERMISSAO[$_SESSION["permissao"]] ?? "../pages/auth/login.php";
        header("Location: " . $destino);
        exit;
    }
}

function logout() {
    $_SESSION = [];
    session_destroy();
    header("Location: ../pages/auth/login.php");
    exit;
}