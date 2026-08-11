<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/dashboard.css">
  
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>



</head>
<body>
 
    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>
        <h1 class="cumprimento"> Olá Frederico </h1>

        <div class="cartoes">
            <div class="cartao" onclick="toggleList('listaConsultas')">
                <div class="cartaoTitulo">Consultas Hoje</div>
                <div class="cartaoValor">8</div>
                <div class="cartaoLegenda">confirmadas</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAguardaConfirmacaoo')">
                <div class="cartaoTitulo">Aguardando Confirmação</div>
                <div class="cartaoValor">2</div>
                <div class="cartaoLegenda">pendentes</div>
            </div>

            <div class="cartao-aviso">
                <div class="cartaoTitulo">Avisos</div>
                <div class="cartaoLegenda">Paciente Ana Lima já está aqui | Atendimento 08:00h - 09:30h</div>
                <div class="link"><a href="agenda.php">Acessar evento 🡪 </a></div>
            </div> 

            <div class="calendario">
                <div class="calendario-header">
                    <button id="mes-anterior">‹</button>
                    <h3 id="mes-atual"></h3>
                    <button id="mes-proximo">›</button>
                </div>

                <div class="dias-semana">
                    <span>DOM</span>
                    <span>SEG</span>
                    <span>TER</span>
                    <span>QUA</span>
                    <span>QUI</span>
                    <span>SEX</span>
                    <span>SÁB</span>
                </div>

                <div id="dias" class="dias"></div>
            </div>

        </div>

        <div id="listaConsultas" class="collapse-list">
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
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td>08h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td>Bruno Martins</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>09h30</td>
                        <td><span class="badge badge-orange">Pendente</span></td>
                    </tr>
                    <tr>
                        <td>Carla Dias</td>
                        <td>Dr. Carlos Souza</td>
                        <td>11h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td>Diego Fernandes</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>14h00</td>
                        <td><span class="badge badge-green">Confirmado</span></td>
                    </tr>
                    <tr>
                        <td>Elisa Costa</td>
                        <td>Dr. Carlos Souza</td>
                        <td>15h30</td>
                        <td><span class="badge badge-orange">Pendente</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="listaAguardaConfirmacaoo" class="collapse-list">
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
                        <td>Bruno Martins</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>09h30</td>
                        <td><button class="btn btn-sm">Confirmar</button></td>
                    </tr>
                    <tr>
                        <td>Elisa Costa</td>
                        <td>Dr. Carlos Souza</td>
                        <td>15h30</td>
                        <td><button class="btn btn-sm">Confirmar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

    
            
        <script src="/app/js/utils.js"></script>

</body>
</html>