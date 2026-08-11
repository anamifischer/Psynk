<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro</title>

        <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">

    <link rel="stylesheet" type="text/css" href="/app/css/variables.css">
    <link rel="stylesheet" type="text/css" href="/app/css/financeiro.css">
  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php 
        include($_SERVER['DOCUMENT_ROOT'] . "/app/includes/menuAdmin.php");
    ?>
</head>
<body>

    <div class="main">
        <header class="barraSuperior">
            <h1>Visão Geral</h1>
            <p>07 de Agosto de 2026</p>
            </header>

        <div class="cartoes">
            <div class="cartao">
                <div class="cartaoTitulo">Receita</div>
                <div class="cartaoValor">R$ 12150,76</div>
                <div class="cartaoLegenda">total mês</div>
            </div>

            <div class="cartao" onclick="toggleList('listaPendentes')">
                <div class="cartaoTitulo">Pendente</div>
                <div class="cartaoValor">R$ 630,00</div>
                <div class="cartaoLegenda">pagamentos pendentes</div>
            </div>

            <div class="cartao" onclick="toggleList('listaAtrasados')">
                <div class="cartaoValor">4</div>
                <div class="cartaoLegenda">pagamentos atrasados</div>
            </div>

            <div class="cartao" onclick="toggleList('listaVencendo')">
                <div class="cartaoValor">3</div>
                <div class="cartaoLegenda">pagamentos vencendo hoje</div>
            </div>
        </div>

        <div class="financeiro-header">
            <div class="ano-dropdown-wrapper">
                <button class="btn btn-secondary" id="btn-ano" onclick="toggleAnoDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="btn-ano-label">2026</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <div class="ano-dropdown" id="ano-dropdown">
                    <button class="ano-opcao" data-ano="2024">2024</button>
                    <button class="ano-opcao" data-ano="2025">2025</button>
                    <button class="ano-opcao active" data-ano="2026">2026</button>
                </div>
            </div>
        </div>

        <div class="grafico-container">
            <canvas id="graficoReceita"></canvas>
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
    
</body>
</html>