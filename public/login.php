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

    <script>
        function fazerLogin() {
            const email = document.getElementById('email').value.trim().toLowerCase();
            
            if (email === 'administrador') {
                window.location.href = '/app/pages/admin/dashboard.php';
            } else if (email === 'psicologo') {
                window.location.href = '/app/pages/psicologo/dashboard.php';
            } else if (email === 'recepcionista') {
                window.location.href = '/app/pages/recepcionista/dashboard.php';
            } else {
                alert('Usuário não encontrado. Tente: administrador, psicologo ou recepcionista');
            }
        }
    </script>

    <div class="login-img">
        <img src="../imgs/logo.png" alt="logotipo PsySink">
    </div>
    <h1>Bem-vindo(a) ao PsySink</h1>
    <p>Faça login para acessar os recursos do sistema.</p>

    <form action="../app/pages/admin/dashboard.php" method="get">

        <label for="email">E-mail</label>
        <input type="text" id="email" name="email" placeholder="seu@email.com">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••">

        <a href="redefinirSenha.php" class="link">
            Esqueci minha senha →
        </a>
        <a href="cadastrar.php" class="link">
            Ainda não tem uma conta? →
        </a>

        <button type="button" onclick="fazerLogin()" class="btn-primary">
            Entrar
        </button>

    </form>

</div>
 


</body>
</html>