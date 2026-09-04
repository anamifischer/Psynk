<?php
    session_start();
    require_once "conexao.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_POST["email"];
        $senha = $_POST["senha"];
    
        $sql = "SELECT * FROM usuarios WHERE email = ?";

        $consulta = $conexao->prepare($sql);
        $consulta->bind_param("s", $email);
        $consulta->execute();

        $resultado = $consulta->get_result();
        $usuario = $resultado->fetch_assoc();

        if ($usuario && password_verify($senha, $usuario["senha"])) {
            $_SESSION["id"] = $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["permissao"] = $usuario["permissao"];

            if ($usuario["permissao"] === "ADMIN"){
                header("Location: ../pages/admin/dashboard.php");
            }
            else if ($usuario["permissao"] === "PSICOLOGO"){
                header("Location: ../pages/admin/dashboard.php");
            }
            else if ($usuario["permissao"] === "RECEPCIONISTA"){
                header("Location: ../pages/recepcionista/dashboard.php");
            }
            else{
                header("Location: ../pages/auth/login.php?erro=1");
                exit;
            }
            
        } else {
            header("Location: ../pages/auth/login.php?erro=2");
            exit;
        }
    }

    function verificarLogin(){
        if(!isset($_SESSION["id"])){
            header("Location: ../pages/auth/login.php");
            exit;
        }
    }

    function verificarPermissao($permissao){
        verificarLogin();
        if($_SESSION["permissao"] !== $permissao){
            header("Location: ../pages/auth/login.php?erro=3");
            exit;
        }
    }

?>