<?php
    require_once __DIR__ . "/../../../config/sessao.php";
    verificarPermissao(["PSICOLOGO"]);

    require_once __DIR__ . "/../../../config/conexao.php";
    require_once __DIR__ . "/../../includes/consultas.php";

    $valorConsulta = buscarValorConsulta($conexao, (int) $_SESSION['id']);

    $usuarios = buscarUsuariosDaClinica($conexao, (int) $_SESSION['id_clinica']);
    $nomesPapeis = [
        'ADMIN'         => 'Administrador(a)',
        'PSICOLOGO'     => 'Psicólogo(a)',
        'RECEPCIONISTA' => 'Recepcionista',
    ];

    $perfil = buscarUsuarioPorId($conexao, (int) $_SESSION['id']);

    // Iniciais para o avatar (primeiras letras dos dois primeiros nomes)
    $iniciais = '';
    foreach (array_slice(preg_split('/\s+/', trim($perfil['nome'])), 0, 2) as $parte) {
        $iniciais .= mb_strtoupper(mb_substr($parte, 0, 1));
    }

    $clinica = buscarClinica($conexao, (int) $_SESSION['id_clinica']);
    $horarios = buscarHorariosAtendimento($conexao, (int) $_SESSION['id']);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsySink — Configurações</title>

    <?php $papelUsuario = "psicologo"; ?>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" href="/app/css/configuracoes.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuPsicologo.php"); ?>

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
            <button class="config-nav-item" data-secao="horarios">Horários de atendimento</button>
            <button class="config-nav-item" data-secao="valor-consulta">Valor da consulta</button>
            <?php if ($papelUsuario === 'admin'): ?>
                <hr class="config-nav-divisor">
                <button class="config-nav-item" data-secao="dados-clinica">Dados da clínica</button>
                <button class="config-nav-item" data-secao="usuarios">Gerenciar usuários</button>
            <?php endif; ?>
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

            <section class="config-secao" id="horarios">
                <h2 class="config-titulo">Horários de atendimento</h2>
                <p class="config-subtitulo">Defina os dias e horários em que você está disponível para consultas. Agendamentos fora dessa janela não serão permitidos.</p>

                <div class="config-form">

                    <div class="horarios-grid" id="horarios-grid">
                        <?php
                            $dias = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado', 7 => 'Domingo'];
                            foreach ($dias as $numero => $dia):
                                $ativo  = isset($horarios[$numero]);
                                $inicio = $horarios[$numero]['inicio'] ?? '';
                                $fim    = $horarios[$numero]['fim'] ?? '';
                            ?>
                            <div class="horario-linha <?= $ativo ? 'ativo' : '' ?>">
                                <label class="horario-toggle">
                                    <input type="checkbox" <?= $ativo ? 'checked' : '' ?>
                                        onchange="toggleDia(this)">
                                    <span class="horario-dia"><?= $dia ?></span>
                                </label>
                                <div class="horario-campos <?= $ativo ? '' : 'desabilitado' ?>">
                                    <input type="time" value="<?= $inicio ?>" <?= $ativo ? '' : 'disabled' ?>>
                                    <span class="horario-ate">até</span>
                                    <input type="time" value="<?= $fim ?>" <?= $ativo ? '' : 'disabled' ?>>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="config-campo" style="margin-top:1.5rem">
                        <label>Duração padrão da consulta</label>
                        <select id="horario-duracao">
                            <?php foreach ([30, 45, 50, 60] as $minutos): ?>
                                <option value="<?= $minutos ?>" <?= (int) $perfil['duracao_consulta'] === $minutos ? 'selected' : '' ?>>
                                    <?= $minutos ?> minutos
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="config-campo">
                        <label>Intervalo entre consultas</label>
                        <select id="horario-intervalo">
                            <?php foreach ([0, 10, 15, 30] as $minutos): ?>
                                <option value="<?= $minutos ?>" <?= (int) $perfil['intervalo_consulta'] === $minutos ? 'selected' : '' ?>>
                                    <?= $minutos === 0 ? 'Sem intervalo' : $minutos . ' minutos' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="config-acoes">
                        <button class="btn btn-primary" id="btn-salvar-horarios">Salvar horários</button>
                    </div>
                </div>
            </section>

            <section class="config-secao" id="valor-consulta">
                <h2 class="config-titulo">Valor da consulta</h2>
                <p class="config-subtitulo">Este valor será usado como padrão ao registrar pagamentos. Pode ser alterado individualmente por paciente.</p>

                <div class="config-form">
                    <div class="config-campo config-campo-sm">
                        <label>Valor padrão</label>
                        <div class="input-prefixo">
                            <span>R$</span>
                            <input type="number" id="valor-consulta-input"
                                value="<?= number_format($valorConsulta, 2, '.', '') ?>"
                                min="0" step="0.01" placeholder="0,00">
                        </div>
                    </div>

                    <?php if ($papelUsuario === 'admin'): ?>
                    <div class="config-campo">
                        <label>Percentual de repasse aos psicólogos</label>
                        <div class="input-sufixo">
                            <input type="number" id="percentual-repasse-input" value="<?= rtrim(rtrim(number_format($clinica['percentual_repasse'], 2, '.', ''), '0'), '.') ?>" min="0" max="100" style="max-width:100px">
                            <span>%</span>
                        </div>
                        <p class="config-dica">Percentual padrão aplicado ao faturamento de cada psicólogo.</p>
                    </div>
                    <?php endif; ?>

                    <div class="config-acoes">
                        <button class="btn btn-primary" id="btn-salvar-valor">Salvar</button>
                    </div>
                </div>
            </section>

            <?php if ($papelUsuario === 'admin'): ?>

            <section class="config-secao" id="dados-clinica">
                <h2 class="config-titulo">Dados da clínica</h2>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Nome da clínica</label>
                        <input type="text" id="clinica-nome" maxlength="45" value="<?= htmlspecialchars($clinica['nome']) ?>" placeholder="Nome da clínica">
                    </div>
                    <div class="config-campo">
                        <label>CNPJ</label>
                        <input type="text" id="clinica-cnpj" maxlength="18" value="<?= htmlspecialchars($clinica['CNPJ'] ?? '') ?>" placeholder="00.000.000/0001-00">
                    </div>
                    <div class="config-campo">
                        <label>Endereço</label>
                        <input type="text" id="clinica-endereco" maxlength="60" value="<?= htmlspecialchars($clinica['Endereco'] ?? '') ?>" placeholder="Rua, número — Cidade, UF">
                    </div>
                    <div class="config-campo">
                        <label>Telefone</label>
                        <input type="tel" id="clinica-telefone" maxlength="45" value="<?= htmlspecialchars($clinica['telefone'] ?? '') ?>" placeholder="(00) 00000-0000">
                    </div>
                    <div class="config-acoes">
                        <button class="btn btn-primary" id="btn-salvar-clinica">Salvar dados da clínica</button>
                    </div>
                </div>
            </section>

            <section class="config-secao" id="usuarios">
                <h2 class="config-titulo">Gerenciar usuários</h2>

                <div class="config-usuarios-topo">
                    <button class="btn btn-primary" onclick="abrirFormUsuario()">+ Novo usuário</button>
                </div>

                <div class="config-form-usuario" id="form-novo-usuario" style="display:none">
                    <h3 class="config-subtitulo" id="usuario-form-titulo" style="font-weight:600;margin-bottom:1rem">Novo usuário</h3>
                    <div class="config-form">
                        <input type="hidden" id="usuario-id" value="0">
                        <div class="config-campo">
                            <label>Nome completo</label>
                            <input type="text" id="usuario-nome" maxlength="45" placeholder="Nome do usuário">
                        </div>
                        <div class="config-campo">
                            <label>E-mail (login)</label>
                            <input type="email" id="usuario-email" maxlength="45" placeholder="email@exemplo.com">
                        </div>
                        <div class="config-campo">
                            <label>Telefone</label>
                            <input type="tel" id="usuario-telefone" maxlength="45" placeholder="(00) 00000-0000">
                        </div>
                        <div class="config-campo">
                            <label>Papel</label>
                            <select id="usuario-permissao" onchange="atualizarCampoCrp()">
                                <option value="">Selecione...</option>
                                <option value="PSICOLOGO">Psicólogo(a)</option>
                                <option value="RECEPCIONISTA">Recepcionista</option>
                                <option value="ADMIN">Administrador(a)</option>
                            </select>
                        </div>
                        <div class="config-campo" id="usuario-campo-crp">
                            <label>CRP</label>
                            <input type="text" id="usuario-crp" maxlength="20" placeholder="00/00000">
                        </div>
                        <div class="config-campo">
                            <label id="usuario-rotulo-senha">Senha provisória</label>
                            <input type="password" id="usuario-senha" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                            <p class="config-dica" id="usuario-dica-senha"></p>
                        </div>
                        <div class="config-acoes">
                            <button class="btn btn-secondary" onclick="fecharFormUsuario()">Cancelar</button>
                            <button class="btn btn-primary" id="btn-salvar-usuario" onclick="salvarUsuario()">Criar usuário</button>
                        </div>
                    </div>
                </div>

                <table class="data-table" style="margin-top:1rem">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Papel</th>
                            <th>Situação</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario):
                            $ehVoce = (int) $usuario['idUsuario'] === (int) $_SESSION['id'];
                            $dadosJs = [
                                'id' => (int) $usuario['idUsuario'], 'nome' => $usuario['nome'], 'email' => $usuario['email'],
                                'telefone' => $usuario['telefone'] ?? '', 'crp' => $usuario['crp'] ?? '',
                                'permissao' => $usuario['permissao'], 'voce' => $ehVoce,
                            ];
                        ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($usuario['nome']) ?>
                                    <?php if ($ehVoce): ?>
                                        <span style="color:var(--gray-400)">(você)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= $nomesPapeis[$usuario['permissao']] ?? $usuario['permissao'] ?></td>
                                <td>
                                    <?php if ((int) $usuario['ativo'] === 1): ?>
                                        <span class="badge badge-verde">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-cinza">Desativado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm"
                                            onclick="editarUsuario(<?= htmlspecialchars(json_encode($dadosJs, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)">Editar</button>
                                    <?php if (!$ehVoce): ?>
                                        <?php if ((int) $usuario['ativo'] === 1): ?>
                                            <button class="btn btn-sm" style="color:var(--vermelho-500)"
                                                    onclick="alterarStatusUsuario(<?= (int) $usuario['idUsuario'] ?>, 'desativar')">Desativar</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm"
                                                    onclick="alterarStatusUsuario(<?= (int) $usuario['idUsuario'] ?>, 'ativar')">Ativar</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <?php endif; ?>

        </div>
    </div>
</div>

<script src="/app/js/config.js"></script>

</body>
</html>