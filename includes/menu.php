<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>
<body>
    <a href="/pages/<?= $_SESSION['tipo_usuario'] ?>/dashboard.php">Início</a>
    <a href="/pages/<?= $_SESSION['tipo_usuario'] ?>/agenda.php">Agenda</a>
    <a href="/pages/<?= $_SESSION['tipo_usuario'] ?>/pacientes.php">Pacientes</a>
    <a href="/pages/<?= $_SESSION['tipo_usuario'] ?>/financeiro.php">Financeiro</a>
    <a href="/pages/<?= $_SESSION['tipo_usuario'] ?>/configuracoes.php">Configurações</a>

    <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
        <a href="/pages/admin/relatorios.php">Relatórios</a>
    <?php elseif ($_SESSION['tipo_usuario'] === 'psicologo'): ?>
        <a href="/pages/admin/relatorios.php">Relatórios</a>
    <?php endif; ?>
    

</body>
</html>