<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["RECEPCIONISTA"]);

    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";

    $perfil = buscarUsuarioPorId($conexao, (int) $_SESSION['id']);

    // Iniciais para o avatar (primeiras letras dos dois primeiros nomes)
    $iniciais = '';
    foreach (array_slice(preg_split('/\s+/', trim($perfil['nome'])), 0, 2) as $parte) {
        $iniciais .= mb_strtoupper(mb_substr($parte, 0, 1));
    }


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Configurações</title>

    <?php $papelUsuario = "recepcionista"; ?>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" href="/app/css/configuracoes.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>

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
                    <div class="config-avatar" id="config-avatar-preview"><?= $iniciais ?></div>
                </div>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Nome completo</label>
                        <input type="text" id="perfil-nome" maxlength="45" value="<?= htmlspecialchars($perfil['nome']) ?>" placeholder="Seu nome completo">
                    </div>
                    <div class="config-campo">
                        <label>E-mail</label>
                        <input type="email" id="perfil-email" maxlength="45" value="<?= htmlspecialchars($perfil['email']) ?>" placeholder="seu@email.com">
                    </div>
                    <div class="config-campo">
                        <label>Telefone</label>
                        <input type="tel" id="perfil-telefone" maxlength="45" value="<?= htmlspecialchars($perfil['telefone'] ?? '') ?>" placeholder="(00) 00000-0000">
                    </div>
                    <?php if ($papelUsuario === 'psicologo' || $papelUsuario === 'admin'): ?>
                    <div class="config-campo">
                        <label>CRP</label>
                        <input type="text" id="perfil-crp" maxlength="20" value="<?= htmlspecialchars($perfil['crp'] ?? '') ?>" placeholder="00/00000">
                    </div>
                    <?php endif; ?>
                    <div class="config-acoes">
                        <button class="btn btn-primary" id="btn-salvar-perfil">Salvar alterações</button>
                    </div>
                </div>
            </section>

            <section class="config-secao" id="seguranca">
                <h2 class="config-titulo">Segurança</h2>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Senha atual</label>
                        <input type="password" id="senha-atual" autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <div class="config-campo">
                        <label>Nova senha</label>
                        <input type="password" id="senha-nova" autocomplete="new-password" placeholder="••••••••">
                    </div>
                    <div class="config-campo">
                        <label>Confirmar nova senha</label>
                        <input type="password" id="senha-confirmar" autocomplete="new-password" placeholder="••••••••">
                    </div>
                    <p class="config-dica">A nova senha precisa ter pelo menos 8 caracteres.</p>
                    <div class="config-acoes">
                        <button class="btn btn-primary" id="btn-alterar-senha">Alterar senha</button>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<script src="/app/js/config.js"></script>

</body>
</html>