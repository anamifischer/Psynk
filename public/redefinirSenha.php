<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha — PsySink</title>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="../app/css/variables.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-panel">

    <div class="login-img">
        <img src="../imgs/logotipo-escuro.png" alt="logotipo PsySink">
    </div>

    <!-- ETAPA 1: solicitar e-mail -->
    <div id="etapa-email">
        <h1>Redefinir senha</h1>
        <p>Digite o e-mail da sua conta. Enviaremos um link para redefinir sua senha.</p>

        <form onsubmit="avancarEtapa(event)">
            <label for="email">E-mail</label>
            <input type="email" id="email" placeholder="seu@email.com" required>

            <button type="submit" class="btn-primary">Enviar link</button>
        </form>

        <a href="login.php" class="forgot-link">← Voltar para o login</a>
    </div>

    <!-- ETAPA 2: confirmação de envio -->
    <div id="etapa-confirmacao" style="display:none">
        <h1>E-mail enviado!</h1>
        <p>Verifique sua caixa de entrada e clique no link para redefinir sua senha.</p>
        <p>Não recebeu? Verifique a pasta de spam ou <span class="reenviar" onclick="voltarEtapa()">tente novamente</span>.</p>

        <a href="login.php" class="forgot-link">← Voltar para o login</a>
    </div>

</div>

<script>
function avancarEtapa(e) {
    e.preventDefault();
    document.getElementById('etapa-email').style.display = 'none';
    document.getElementById('etapa-confirmacao').style.display = 'block';
}

function voltarEtapa() {
    document.getElementById('etapa-confirmacao').style.display = 'none';
    document.getElementById('etapa-email').style.display = 'block';
}
</script>

</body>
</html>