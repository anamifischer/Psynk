<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PsySink</title>
 
    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="../app/css/variables.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
 
<div class="login-panel">

    <div class="login-img">
        <img src="../imgs/logo.png" alt="logotipo PsySink">
    </div>
    <h1>Bem-vindo(a) ao PsySink</h1>
    <p>Faça parte da maior plataforma de gerenciamento para psicólogos do pais</p>

    <form action="../app/pages/admin/dashboard.php" method="get">

        <label for="email">nome completo</label>
        <input type="text" name="nome" placeholder="Seu Nome">

        <label for="email">Informe seu E-mail</label>
        <input type="email" id="email" name="email" placeholder="seu@email.com">

        <label for="senha">Crie uma Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••">

        <label for="senha">Confirme sua senha</label>
        <input type="password" id="confirmacaosenha" name="senha" placeholder="••••••••">

        <a href="login.php" class="link">
            Já tem uma conta? Faça login aqui →
        </a>

        <button type="submit" id="botao-login" class="btn-primary">
            Criar Conta
        </button>

    </form>

</div>
 
</body>
</html>