<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações — Psynk</title>

    <?php $papelUsuario = "admin";?>

    <link rel="stylesheet" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" href="/app/css/variables.css">
    <link rel="stylesheet" href="/app/css/globals.css">
    <link rel="stylesheet" href="/app/css/configuracoes.css">

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php"); ?>

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
                    <div class="config-avatar" id="config-avatar-preview">MF</div>
                    <div>
                        <button class="btn btn-secondary btn-sm">Alterar foto</button>
                        <p class="config-dica">JPG ou PNG, máximo 2MB</p>
                    </div>
                </div>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Nome completo</label>
                        <input type="text" value="Marcos Ferreira" placeholder="Seu nome completo">
                    </div>
                    <div class="config-campo">
                        <label>E-mail</label>
                        <input type="email" value="marcos@psynk.com" placeholder="seu@email.com">
                    </div>
                    <div class="config-campo">
                        <label>Telefone</label>
                        <input type="tel" value="(51) 99999-0000" placeholder="(00) 00000-0000">
                    </div>
                    <?php if ($papelUsuario === 'psicologo' || $papelUsuario === 'admin'): ?>
                    <div class="config-campo">
                        <label>CRP</label>
                        <input type="text" value="07/12345" placeholder="00/00000">
                    </div>
                    <?php endif; ?>
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

            <section class="config-secao" id="horarios">
                <h2 class="config-titulo">Horários de atendimento</h2>
                <p class="config-subtitulo">Defina os dias e horários em que você está disponível para consultas. Agendamentos fora dessa janela não serão permitidos.</p>

                <div class="config-form">

                    <div class="horarios-grid">
                        <?php
                        $dias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                        $ativos = [true, true, true, true, true, false, false];
                        foreach ($dias as $i => $dia):
                        ?>
                        <div class="horario-linha <?= $ativos[$i] ? 'ativo' : '' ?>">
                            <label class="horario-toggle">
                                <input type="checkbox" <?= $ativos[$i] ? 'checked' : '' ?>
                                    onchange="toggleDia(this)">
                                <span class="horario-dia"><?= $dia ?></span>
                            </label>
                            <div class="horario-campos <?= $ativos[$i] ? '' : 'desabilitado' ?>">
                                <input type="time" value="<?= $ativos[$i] ? '08:00' : '' ?>" <?= $ativos[$i] ? '' : 'disabled' ?>>
                                <span class="horario-ate">até</span>
                                <input type="time" value="<?= $ativos[$i] ? '18:00' : '' ?>" <?= $ativos[$i] ? '' : 'disabled' ?>>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="config-campo" style="margin-top:1.5rem">
                        <label>Duração padrão da consulta</label>
                        <select>
                            <option>30 minutos</option>
                            <option selected>45 minutos</option>
                            <option>50 minutos</option>
                            <option>60 minutos</option>
                        </select>
                    </div>

                    <div class="config-campo">
                        <label>Intervalo entre consultas</label>
                        <select>
                            <option>Sem intervalo</option>
                            <option selected>10 minutos</option>
                            <option>15 minutos</option>
                            <option>30 minutos</option>
                        </select>
                    </div>

                    <div class="config-acoes">
                        <button class="btn btn-primary">Salvar horários</button>
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
                            <input type="number" value="180" min="0" step="0.01" placeholder="0,00">
                        </div>
                    </div>

                    <?php if ($papelUsuario === 'admin'): ?>
                    <div class="config-campo">
                        <label>Percentual de repasse aos psicólogos</label>
                        <div class="input-sufixo">
                            <input type="number" value="30" min="0" max="100" style="max-width:100px">
                            <span>%</span>
                        </div>
                        <p class="config-dica">Percentual padrão aplicado ao faturamento de cada psicólogo.</p>
                    </div>
                    <?php endif; ?>

                    <div class="config-acoes">
                        <button class="btn btn-primary">Salvar valor</button>
                    </div>
                </div>
            </section>

            <?php if ($papelUsuario === 'admin'): ?>

            <section class="config-secao" id="dados-clinica">
                <h2 class="config-titulo">Dados da clínica</h2>

                <div class="config-form">
                    <div class="config-campo">
                        <label>Nome da clínica</label>
                        <input type="text" value="Clínica Psynk" placeholder="Nome da clínica">
                    </div>
                    <div class="config-campo">
                        <label>CNPJ</label>
                        <input type="text" value="00.000.000/0001-00" placeholder="00.000.000/0001-00">
                    </div>
                    <div class="config-campo">
                        <label>Endereço</label>
                        <input type="text" value="Rua Exemplo, 123 — Porto Alegre, RS" placeholder="Rua, número — Cidade, UF">
                    </div>
                    <div class="config-campo">
                        <label>Telefone</label>
                        <input type="tel" value="(51) 3000-0000" placeholder="(00) 00000-0000">
                    </div>
                    <div class="config-acoes">
                        <button class="btn btn-primary">Salvar dados da clínica</button>
                    </div>
                </div>
            </section>

            <section class="config-secao" id="usuarios">
                <h2 class="config-titulo">Gerenciar usuários</h2>

                <div class="config-usuarios-topo">
                    <button class="btn btn-primary" onclick="abrirFormUsuario()">+ Novo usuário</button>
                </div>

                <div class="config-form-usuario" id="form-novo-usuario" style="display:none">
                    <h3 class="config-subtitulo" style="font-weight:600;margin-bottom:1rem">Novo usuário</h3>
                    <div class="config-form">
                        <div class="config-campo">
                            <label>Nome completo</label>
                            <input type="text" placeholder="Nome do usuário">
                        </div>
                        <div class="config-campo">
                            <label>E-mail</label>
                            <input type="email" placeholder="email@exemplo.com">
                        </div>
                        <div class="config-campo">
                            <label>Papel</label>
                            <select>
                                <option value="">Selecione...</option>
                                <option value="psicologo">Psicólogo(a)</option>
                                <option value="recepcionista">Recepcionista</option>
                            </select>
                        </div>
                        <div class="config-campo">
                            <label>Senha provisória</label>
                            <input type="password" placeholder="••••••••">
                        </div>
                        <div class="config-acoes">
                            <button class="btn btn-secondary" onclick="fecharFormUsuario()">Cancelar</button>
                            <button class="btn btn-primary">Criar usuário</button>
                        </div>
                    </div>
                </div>

                <table class="data-table" style="margin-top:1rem">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Papel</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ana Souza</td>
                            <td>ana@psynk.com</td>
                            <td>Psicóloga</td>
                            <td><span class="badge badge-verde">Ativo</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm">Editar</button>
                                <button class="btn btn-sm" style="color:var(--vermelho-500)">Desativar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Carlos Lima</td>
                            <td>carlos@psynk.com</td>
                            <td>Psicólogo</td>
                            <td><span class="badge badge-verde">Ativo</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm">Editar</button>
                                <button class="btn btn-sm" style="color:var(--vermelho-500)">Desativar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Carol Andrade</td>
                            <td>carol@psynk.com</td>
                            <td>Recepcionista</td>
                            <td><span class="badge badge-verde">Ativo</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm">Editar</button>
                                <button class="btn btn-sm" style="color:var(--vermelho-500)">Desativar</button>
                            </td>
                        </tr>
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