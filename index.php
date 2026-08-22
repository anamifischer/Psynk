<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página inicial</title>

    <link rel="stylesheet" type="text/css" href="https://meyerweb.com/eric/tools/css/reset/reset.css">
    <link rel="stylesheet" type="text/css" href="public/index.css">
    <link rel="stylesheet" type="text/css" href="app/css/variables.css">

</head>
<body onload="mostrarPagina('inicio')">

    <div class="main">

        <div class="conteudo">
            <nav>
                <img src="imgs/logotipo-escuro.png" alt="logotipo PsySink">
                <a href="#inicio" class="btn">Início</a>
                <a href="#sobreNos" class="btn">Sobre Nós</a>
                <a href="#funcionalidades" class="btn">Funcionalidades</a>
                <a href="#planos" class="btn">Planos</a>
                <button id="btn-login" onclick="window.location.href='/public/login.php'">Login</button>
                <button id="btn-login" onclick="window.location.href='/public/cadastrar.php'">Cadastrar</button>
            </nav>

            <main id="conteudo">
                
                <section id="inicio"><h1>PsySink</h1>
                    <h2>Mais tempo para atender. Menos tempo para organizar.</h2>
                    <p>O PsySink reúne agenda, pacientes, prontuários e gestão financeira em um único lugar, feito especialmente para profissionais da psicologia.</p>
                    <p>Gestão simples, segura e pensada para a rotina do psicólogo.</p>
                </section>      

                <section id="sobreNos"><h1>Sobre Nós</h1>
                    <h2>Feito para quem cuida</h2>
                    <p>Sabemos que a rotina de um psicólogo vai muito além dos atendimentos. Entre sessões, horários, registros, pagamentos e organização da agenda, a gestão do consultório pode acabar ocupando um espaço que deveria ser dedicado ao que realmente importa: o cuidado com o paciente.</p>
                    <p>O PsySink nasceu para simplificar essa rotina.</p>

                    <p>Criamos uma plataforma pensada especificamente para profissionais da psicologia, reunindo as principais ferramentas de gestão em um único ambiente, de forma intuitiva, organizada e segura.</p>
                </section>

                <section id="funcionalidades"><h1>Funcionalidades</h1>
                    <h2>Um sistema completo pensado para você</h2>
                    <div class="cards-funcionalidades">
                        <div class="card">
                            <div class="icone">
                                <img src="imgs/icons/agenda.png">
                            </div>
                            <h3>Agenda</h3>
                            <p>Organize seus atendimentos, horários e compromissos de forma simples e intuitiva.</p>
                        </div>
                        <div class="card">
                            <div class="icone">
                                <img src="imgs/icons/pacientes.png">
                            </div>
                            <h3>Pacientes</h3>
                            <p>Tenha as informações dos seus pacientes organizadas e sempre acessíveis.</p>
                        </div>
                        <div class="card">
                            <div class="icone">
                                <img src="imgs/icons/menu.png">
                            </div>
                            <h3>Prontuários</h3>
                            <p>Mantenha registros e informações dos acompanhamentos organizados.</p>
                        </div>
                        <div class="card">
                            <div class="icone">
                                <img src="imgs/icons/financeiro.png">
                            </div>
                            <h3>Financeiro</h3>
                            <p>Acompanhe seus recebimentos e tenha mais controle sobre sua prática.</p>
                        </div>
                        <div class="card">
                            <div class="icone">
                                <img src="imgs/icons/relatorio.png">
                            </div>
                            <h3>Relatórios</h3>
                            <p>Visualize informações importantes e acompanhe seus principais indicadores.</p>
                        </div>
                    </div>
                </section>

                <section id="planos"><h1>Planos</h1>
                    <div class="secao-titulo">
                        <h2>Escolha o plano ideal para sua prática.</h2>
                        <p>Tenha as ferramentas necessárias para organizar sua rotina profissional.</p>
                    </div>
                    <div class="card-plano">
                        <h3>Profissional</h3>
                        <p class="descricao-plano">Para psicólogos que querem uma gestão completa da sua prática.</p>
                    <div class="preco">
                        <span>R$</span>49,90<small>/mês</small>
                    </div>
                    <button class="btn-plano">
                        Começar agora
                    </button>
                </section>  
            </main>
        </div>
    </body>
    
</html>