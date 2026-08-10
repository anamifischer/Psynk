<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PsySink</title>
 
    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" type="text/css" href="login.css">
    <link rel="stylesheet" type="text/css" href="../app/css/variables.css">
</head>
<body>
 
<div class="login-panel">

    <h1>Bem-vindo(a) ao PsySink</h1>
    <p>Organize informações. Apoie transformações.</p>
    <p>Faça login para acessar os recursos do sistema.</p>

    <form action="/pages/admin/dashboard.php" method="get">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="seu@email.com">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••">

        <a href="redefinir-senha.php" class="forgot-link">
            Esqueci minha senha →
        </a>

        <button type="submit" id="botao-login" class="btn-primary">
            Entrar
        </button>

    </form>

</div>
 
</body>
</html>