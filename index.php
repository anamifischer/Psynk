<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PsySink</title>
 
    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" type="text/css" href="/css/index.css">
</head>
<body>
 
<div class="login-page">
 
    <!-- Painel esquerdo: branding (visível só em telas maiores) -->
    <div class="login-branding">
        <span class="logo-mark">✦</span>
        <span class="brand-name">PsySink</span>
        <p class="brand-tagline">Organize informações.<br>Apoie transformações.</p>
    </div>
 
    <!-- Painel direito: formulário -->
    <div class="login-panel">
        <div class="login-box">
 
            <div class="login-header">
                <h1>Bem-vindo(a) ao PsySink</h1>
                <p>Faça login para acessar os recursos do sistema.</p>
            </div>
 
            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="seu@email.com">
            </div>
 
            <div class="field">
                <label for="senha">Senha</label>
                <input type="password" id="senha" placeholder="••••••••">
            </div>
 
            <a href="redefinir-senha.php" class="forgot-link">Esqueci minha senha →</a>
 
            <form action="Psynk/pages/admin/dashboard.php" method="get">
                <button type="submit" id="botao-login" class="btn-primary"> Entrar </button>
            </form>

        </div>
    </div>
 
</div>
 
</body>
</html>