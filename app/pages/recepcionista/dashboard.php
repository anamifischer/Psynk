<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/globals.css">
    <link rel="stylesheet" type="text/css" href="/app/css/dashboard.css">
  
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>
    <?php $papel = 'recepcionista'; ?>


</head>
<body>
 
    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>
        <h1 class="cumprimento"> Olá Frederico </h1>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('listaConsultas')">
                <div class="cartaoTitulo">Consultas Hoje</div>
                <div class="cartaoValor">24</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAguardando')">
                <div class="cartaoTitulo">Aguardando Confirmação</div>
                <div class="cartaoValor">2</div>
            </div>

            <div class="cartao" onclick="toggleList('listaConsultasAmanha')">
                <div class="cartaoTitulo">Aguardando Confirmação</div>
                <div class="cartaoValor">5</div>
                <div class="cartaoLegenda">de amanhã</div>
            </div>
    </div>


        <div id="listaConsultas" class="collapse-list open">
            <div class="section-header">
                <span class="section-title">Consultas de hoje</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Horário</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="link-paciente" onclick="abrirDrawer('Ana Lima')">Ana Lima</span></td>
                        <td>Dr. Carlos Souza</td>
                        <td>08h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td><span class="link-paciente" onclick="abrirDrawer('Bruno Martins')">Bruno Martins</span></td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>09h30</td>
                        <td><span class="badge badge-orange">Pendente</span></td>
                    </tr>
                    <tr>
                        <td><span class="link-paciente" onclick="abrirDrawer('Carla Dias')">Carla Dias</span></td>
                        <td>Dr. Carlos Souza</td>
                        <td>11h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td><span class="link-paciente" onclick="abrirDrawer('Diego Fernandes')">Diego Fernandes</span></td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>14h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td><span class="link-paciente" onclick="abrirDrawer('Elisa Costa')">Elisa Costa</span></td>
                        <td>Dr. Carlos Souza</td>
                        <td>15h30</td>
                        <td><span class="badge badge-orange">Pendente</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="confirmacoes">
            <div id="listaAguardaConfirmacaoo" class="collapse-list open">
                <div class="section-header">
                    <span class="section-title">Aguardando confirmação</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Psicólogo(a)</th>
                            <th>Horário</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer('Bruno Martins')">Bruno Martins</span></td>
                            <td>Dra. Fernanda Rocha</td>
                            <td>09h30</td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                        <tr>
                            <td><span class="link-paciente" onclick="abrirDrawer('Elisa Costa')">Elisa Costa</span></td>
                            <td>Dr. Carlos Souza</td>
                            <td>15h30</td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="listaConsultasAmanha" class="collapse-list open">
                <div class="section-header">
                    <span class="section-title">Consultas de amanhã — aguardando confirmação</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Psicólogo(a)</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ana Lima</td>
                            <td>Dr. Carlos Souza</td>
                            <td>08h00</td>
                            <td><span class="badge badge-orange">Pendente</span></td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                        <tr>
                            <td>Bruno Martins</td>
                            <td>Dra. Fernanda Rocha</td>
                            <td>09h30</td>
                            <td><span class="badge badge-orange">Pendente</span></td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                        <tr>
                            <td>Carla Dias</td>
                            <td>Dr. Carlos Souza</td>
                            <td>11h00</td>
                            <td><span class="badge badge-orange">Pendente</span></td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                        <tr>
                            <td>Diego Fernandes</td>
                            <td>Dra. Fernanda Rocha</td>
                            <td>14h00</td>
                            <td><span class="badge badge-orange">Pendente</span></td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                        <tr>
                            <td>Elisa Costa</td>
                            <td>Dr. Carlos Souza</td>
                            <td>15h30</td>
                            <td><span class="badge badge-orange">Pendente</span></td>
                            <td><button class="btn btn-sm">Confirmar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
            
        <script src="/app/js/utils.js"></script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>

</body>
</html>