<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes Cadastrados</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/pacientes.css">
  
    <?php $papel = 'recepcionista'; ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>

</head>
<body>
        <div class="main">

            <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>

            <div class="cartoes">
                <div class="cartao" onclick="toggleList('listaCadastrados')">
                    <div class="cartaoValor">68</div>
                    <div class="cartaoTitulo">Pacientes Cadastrados</div>
                </div>

                <div class="cartao" onclick="toggleList('listaEspera')">
                    <div class="cartaoValor">2</div>
                    <div class="cartaoTitulo">Pacientes em fila de espera</div>
                </div>

                <div class="cartao" onclick="toggleList('listaAtivos')">
                    <div class="cartaoValor">40</div>
                    <div class="cartaoTitulo">Pacientes ativos</div>
                </div>
            </div>
        
            <div class="filtro-secao">
                <div class="filtro-dropdown-wrapper">
                    <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                        Filtrar
                    </button>

                    <div class="filtro-dropdown" id="filtro-dropdown">
                        <div class="filtro-grupo">
                            <p class="filtro-titulo">Psicólogo</p>
                            <div class="lista-chips" id="filtro-psicologo">
                                <button class="chip active" data-valor="todos">Todos</button>
                                <button class="chip" data-valor="Dr. Carlos Souza">Dr. Carlos Souza</button>
                                <button class="chip" data-valor="Dra. Fernanda Rocha">Dra. Fernanda Rocha</button>
                                <button class="chip" data-valor="Dr. Pedro Alves">Dr. Pedro Alves</button>
                            </div>
                        </div>
                        <div class="filtro-grupo">
                            <p class="filtro-titulo">Status</p>
                            <div class="lista-chips" id="filtro-status">
                                <button class="chip active" data-valor="todos">Todos</button>
                                <button class="chip" data-valor="Ativo">Ativo</button>
                                <button class="chip" data-valor="Em espera">Em espera</button>
                                <button class="chip" data-valor="Encerrado">Encerrado</button>
                                <button class="chip" data-valor="Pausado">Pausado</button>
                                <button class="chip" data-valor="Inativo">Inativo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    
        <div id="listaCadastrados" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pacientes Cadastrados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td><span class="badge badge-green">Ativo</span></td>
                    </tr>
                    <tr>
                        <td>Maria Laura Silva</td>
                        <td>Dr. Carlos Souza</td>
                        <td><span class="badge badge-green">Em espera</span></td>
                    </tr>
                    <tr>
                        <td>José Gonçalves</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td><span class="badge badge-green">Encerrado</span></td>
                    </tr>
                    <tr>
                        <td>Gabriela Moreira</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td><span class="badge badge-green">Pausado</span></td>
                    </tr>
                    <tr>
                        <td>Gabriel Mussoi</td>
                        <td>Dr. Pedro Alves</td>
                        <td><span class="badge badge-green">Inativo</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="listaEspera" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Lista de Espera</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Maria Laura Silva</td>
                        <td>Dr. Carlos Souza</td>
                        <td><span class="badge badge-green">Em espera</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="listaAtivos" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Lista de Ativos (Em acompanhamento Regular)</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a) responsável</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td><span class="badge badge-green">Ativo</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <script src="/app/js/utils.js"></script>
        <script src="/app/js/pacientes.js"></script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
</div>
    
</body>
</html>