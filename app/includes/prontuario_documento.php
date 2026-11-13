<?php
// Documento HTML do prontuário. Usado só pelo api/prontuario_pdf.php.
// Variáveis esperadas: $clinica, $dadosClinica, $paciente, $camposPaciente,
//                      $profissional, $registros, $emitidoEm
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuário — <?= htmlspecialchars($paciente['nome']) ?></title>
    <?php // O estilo mora em css/prontuario.css; aqui só é lido para o Dompdf. ?>
    <style><?= file_get_contents(__DIR__ . '/../css/prontuario.css') ?></style>
</head>
<body>

    <div id="cabecalho">
        <div class="clinica-nome"><?= htmlspecialchars($clinica['nome']) ?></div>
        <?php if ($dadosClinica): ?>
            <div class="clinica-dados"><?= htmlspecialchars(implode(' · ', $dadosClinica)) ?></div>
        <?php endif; ?>
    </div>

    <div id="rodape">
        Documento confidencial, protegido por sigilo profissional.
        As informações aqui contidas não devem ser compartilhadas sem autorização.<br>
        <?= htmlspecialchars($clinica['nome']) ?> · Emitido em <?= $emitidoEm ?>
        por <?= htmlspecialchars($profissional['nome']) ?>
    </div>

    <h1>Prontuário psicológico</h1>

    <table class="dados">
        <?php foreach ($camposPaciente as $rotulo => $valor): ?>
            <?php if (!empty($valor)): ?>
                <tr>
                    <td class="rotulo"><?= $rotulo ?></td>
                    <td class="valor"><?= htmlspecialchars($valor) ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <td class="rotulo">Profissional</td>
            <td class="valor">
                <?= htmlspecialchars($profissional['nome']) ?>
                <?= !empty($profissional['crp']) ? '(CRP ' . htmlspecialchars($profissional['crp']) . ')' : '' ?>
            </td>
        </tr>
    </table>

    <h2>Registros de atendimento</h2>

    <?php if (empty($registros)): ?>
        <p class="vazio">Nenhuma anotação registrada até o momento.</p>
    <?php endif; ?>

    <?php foreach ($registros as $registro): ?>
        <div class="registro">
            <div class="registro-data">
                <?= date('d/m/Y', strtotime($registro['data'])) ?>
                · <?= date('H\hi', strtotime($registro['hora_inicio'])) ?>
            </div>
            <div class="registro-texto"><?= nl2br(htmlspecialchars($registro['conteudo'])) ?></div>
        </div>
    <?php endforeach; ?>

    <div class="assinatura">
        <div class="linha"></div>
        <strong><?= htmlspecialchars($profissional['nome']) ?></strong><br>
        <?php if (!empty($profissional['crp'])): ?>
            <small>CRP <?= htmlspecialchars($profissional['crp']) ?></small>
        <?php endif; ?>
    </div>

</body>
</html>