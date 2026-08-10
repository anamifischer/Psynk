console.log("index.js carregado!");

function mostrarPagina(pagina) {

    const conteudo = document.getElementById("conteudo");

    if (pagina === "inicio") {

        conteudo.innerHTML = `
            <h1>Mais tempo para atender. Menos tempo para organizar.</h1>

            <p>
                O PsySink reúne agenda, pacientes, prontuários e gestão
                financeira em um único lugar, feito especialmente para
                profissionais da psicologia.
            </p>

            <p>
                Gestão simples, segura e pensada para a rotina do psicólogo.
            </p>
        `;

    }

    else if (pagina === "sobre") {

        conteudo.innerHTML = `
            <h1>Feito para quem cuida</h1>

            <p>
                Sabemos que a rotina de um psicólogo vai muito além dos
                atendimentos. Entre sessões, horários, registros, pagamentos
                e organização da agenda, a gestão do consultório pode acabar
                ocupando um espaço que deveria ser dedicado ao que realmente
                importa: o cuidado com o paciente.
            </p>

            <p>
                O PsySink nasceu para simplificar essa rotina.
            </p>

            <p>
                Criamos uma plataforma pensada especificamente para
                profissionais da psicologia, reunindo as principais
                ferramentas de gestão em um único ambiente, de forma
                intuitiva, organizada e segura.
            </p>
        `;

    }

    else if (pagina === "funcionalidades") {

        conteudo.innerHTML = `
            <h1>Um sistema completo pensado para você</h1>

            <div class="cards-funcionalidades">

                <div class="card">
                    <div class="icone">
                        <img src="imgs/icons/agenda.png">
                    </div>

                    <h3>Agenda</h3>

                    <p>
                        Organize seus atendimentos, horários e compromissos
                        de forma simples e intuitiva.
                    </p>
                </div>


                <div class="card">
                    <div class="icone">
                        <img src="imgs/icons/pacientes.png">
                    </div>

                    <h3>Pacientes</h3>

                    <p>
                        Tenha as informações dos seus pacientes organizadas
                        e sempre acessíveis.
                    </p>
                </div>


                <div class="card">
                    <div class="icone">
                        <img src="imgs/icons/prontuario.png">
                    </div>

                    <h3>Prontuários</h3>

                    <p>
                        Mantenha registros e informações dos acompanhamentos
                        organizados.
                    </p>
                </div>


                <div class="card">
                    <div class="icone">
                        <img src="imgs/icons/financeiro.png">
                    </div>

                    <h3>Financeiro</h3>

                    <p>
                        Acompanhe seus recebimentos e tenha mais controle
                        sobre sua prática.
                    </p>
                </div>


                <div class="card">
                    <div class="icone">
                        <img src="imgs/icons/relatorio.png">
                    </div>

                    <h3>Relatórios</h3>

                    <p>
                        Visualize informações importantes e acompanhe seus
                        principais indicadores.
                    </p>
                </div>

            </div>
        `;

    }

    else if (pagina === "planos") {

        conteudo.innerHTML = `
            <div class="secao-titulo">

                <h1>Escolha o plano ideal para sua prática.</h1>

                <p>
                    Tenha as ferramentas necessárias para organizar
                    sua rotina profissional.
                </p>

            </div>


            <div class="card-plano">

                <h3>Profissional</h3>

                <p class="descricao-plano">
                    Para psicólogos que querem uma gestão completa
                    da sua prática.
                </p>

                <div class="preco">
                    <span>R$</span>
                    49,90
                    <small>/mês</small>
                </div>

                <button class="btn-plano">
                    Começar agora
                </button>

            </div>
        `;
    }

    
}


