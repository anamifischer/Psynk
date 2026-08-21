<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações — Psynk</title>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/globals.css">
    <link rel="stylesheet" href="/app/css/configuracoes.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>
    <?php $papelUsuario = "recepcionista"; ?>

    <script>
        const papelUsuario = "<?= $papelUsuario ?>";
    </script>

</head>
<body>

<div class="main">

    <div class="config-wrapper">

        <aside class="config-nav">
            <button class="config-nav-item ativo" data-secao="dados-pessoais">Dados pessoais</button>
            <button class="config-nav-item" data-secao="seguranca">Segurança</button>
        </aside>

        <div class="config-conteudo">

            <section class="config-secao ativa" id="dados-pessoais">
                <h2 class="config-titulo">Dados pessoais</h2>

                <div class="config-avatar-wrapper">
                    <div class="config-avatar">CA</div>
                    <div>
                        <button class="btn btn-secondary btn-sm">Alterar foto</button>
                        <p class="config-dica">JPG ou PNG, máximo 2MB</p>
                    </div>
                </div>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Nome completo</label>
                        <input type="text" value="Carol Andrade" placeholder="Seu nome completo">
                    </div>
                    <div class="config-campo">
                        <label>E-mail</label>
                        <input type="email" value="carol@psynk.com" placeholder="seu@email.com">
                    </div>
                    <div class="config-campo">
                        <label>Telefone</label>
                        <input type="tel" value="(51) 98888-0000" placeholder="(00) 00000-0000">
                    </div>
                    <div class="config-acoes">
                        <button class="btn btn-primary">Salvar alterações</button>
                    </div>
                </div>
            </section>

            <section class="config-secao" id="seguranca">
                <h2 class="config-titulo">Segurança</h2>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Senha atual</label>
                        <input type="password" placeholder="••••••••">
                    </div>
                    <div class="config-campo">
                        <label>Nova senha</label>
                        <input type="password" placeholder="••••••••">
                    </div>
                    <div class="config-campo">
                        <label>Confirmar nova senha</label>
                        <input type="password" placeholder="••••••••">
                    </div>
                    <div class="config-acoes">
                        <button class="btn btn-primary">Alterar senha</button>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('.config-nav-item').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.config-nav-item').forEach(b => b.classList.remove('ativo'));
        document.querySelectorAll('.config-secao').forEach(s => s.classList.remove('ativa'));
        btn.classList.add('ativo');
        document.getElementById(btn.dataset.secao).classList.add('ativa');
    });
});
</script>

<script src="/app/js/utils.js"></script>

</body>
</html>