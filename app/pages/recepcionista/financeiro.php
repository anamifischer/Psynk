<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro</title>

        <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/financeiro.css">
  
    <?php $papel = 'recepcionista'; ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuRecepcionista.php"); ?>
</head>
<body>

    <div class="main">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/header.php"); ?>

        <div class="filtro-secao">
            <div class="filtro-dropdown-wrapper">
                <button class="btn btn-secondary" id="btn-filtrar" onclick="toggleFiltroDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    Profissional
                </button>

                <div class="filtro-dropdown" id="filtro-dropdown">
                    <div class="filtro-grupo">
                        <p class="filtro-titulo">Psicólogo</p>
                        <div class="lista-chips" id="filtro-profissional">
                            <button class="chip" data-valor="todos">Todos</button>
                            <button class="chip active" data-valor="Dr. Carlos Souza">Dr. Carlos Souza</button>
                            <button class="chip" data-valor="Dra. Fernanda Rocha">Dra. Fernanda Rocha</button>
                            <button class="chip" data-valor="Dr. Pedro Alves">Dr. Pedro Alves</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cartoes">

            <div class="cartao" onclick="toggleList('listaPendentes')">
                <div class="cartaoTitulo">Pendente</div>
                <div class="cartaoValor" id="valor-pendente">R$ 630,00</div>
                <div class="cartaoLegenda">pagamentos pendentes</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAtrasados')">
                <div class="cartaoValor" id="valor-atrasados">4</div>
                <div class="cartaoLegenda">pagamentos atrasados</div>
            </div>

            <div class="cartao" onclick="toggleList('listaVencendo')">
                <div class="cartaoValor" id="valor-vencendo">3</div>
                <div class="cartaoLegenda">pagamentos vencendo hoje</div>
            </div>
        </div>

        <div id="listaPendentes" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Pendentes</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td>10/08/2026</td>
                        <td>R$ 180,00</td>
                        <td><span class="badge badge-laranja">Pendente</span></td>
                    </tr>
                    <tr>
                        <td>Gabriela Moreira</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>12/08/2026</td>
                        <td>R$ 200,00</td>
                        <td><span class="badge badge-laranja">Pendente</span></td>
                    </tr>
                    <tr>
                        <td>José Gonçalves</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>14/08/2026</td>
                        <td>R$ 250,00</td>
                        <td><span class="badge badge-laranja">Pendente</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="listaAtrasados" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Atrasados</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gabriel Mussoi</td>
                        <td>Dr. Pedro Alves</td>
                        <td>01/08/2026</td>
                        <td>R$ 180,00</td>
                        <td><span class="badge badge-vermelho">Atrasado</span></td>
                    </tr>
                    <tr>
                        <td>Maria Laura Silva</td>
                        <td>Dr. Carlos Souza</td>
                        <td>03/08/2026</td>
                        <td>R$ 200,00</td>
                        <td><span class="badge badge-vermelho">Atrasado</span></td>
                    </tr>
                    <tr>
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td>05/08/2026</td>
                        <td>R$ 150,00</td>
                        <td><span class="badge badge-vermelho">Atrasado</span></td>
                    </tr>
                    <tr>
                        <td>Gabriela Moreira</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>06/08/2026</td>
                        <td>R$ 200,00</td>
                        <td><span class="badge badge-vermelho">Atrasado</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="listaVencendo" class="collapse-list">
            <div class="section-header">
                <span class="section-title">Pagamentos Vencendo Hoje</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Psicólogo(a)</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>José Gonçalves</td>
                        <td>Dra. Fernanda Rocha</td>
                        <td>10/08/2026</td>
                        <td>R$ 250,00</td>
                        <td><span class="badge badge-laranja">Vence hoje</span></td>
                    </tr>
                    <tr>
                        <td>Gabriel Mussoi</td>
                        <td>Dr. Pedro Alves</td>
                        <td>10/08/2026</td>
                        <td>R$ 180,00</td>
                        <td><span class="badge badge-laranja">Vence hoje</span></td>
                    </tr>
                    <tr>
                        <td>Ana Lima</td>
                        <td>Dr. Carlos Souza</td>
                        <td>10/08/2026</td>
                        <td>R$ 180,00</td>
                        <td><span class="badge badge-laranja">Vence hoje</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script src="/app/js/utils.js"></script>
    <script src="/app/js/financeiro.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/drawer.php"); ?>
    
</body>
</html>