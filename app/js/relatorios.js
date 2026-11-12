const mesesLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
let graficoReceita = null;
let anoAtivo = new Date().getFullYear();

async function atualizarGrafico() {
    let d;
    try {
        const psicologo = document.getElementById('graficoReceita')?.dataset.psicologo || '0';
        const resp = await fetch('/app/api/relatorio_receita.php?ano=' + anoAtivo + '&psicologo=' + psicologo);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        d = await resp.json();
    } catch (erro) {
        console.error('Erro ao carregar o gráfico:', erro);
        return;
    }

    const datasets = [
        {
            label: 'Receita recebida',
            data: d.receita,
            borderColor: '#316372',
            backgroundColor: 'rgba(35,76,88, 0.02)',
            borderWidth: 2,
            pointBackgroundColor: '#316372',
            pointRadius: 2,
            tension: 0.2,
            fill: true,
        },
        {
            label: 'Pendente',
            data: d.pendente,
            borderColor: '#d97c56',
            backgroundColor: 'rgba(35,76,88, 0.02)',
            borderWidth: 2,
            pointBackgroundColor: '#d97c56',
            pointRadius: 2,
            tension: 0.1,
            fill: true,
            borderDash: [4, 4],
        },
        {
            label: 'Atrasado',
            data: d.atrasado,
            borderColor: '#f25c5c',
            backgroundColor: 'rgba(35,76,88, 0.02)',
            borderWidth: 2,
            pointBackgroundColor: '#f25c5c',
            pointRadius: 2,
            tension: 0.1,
            fill: true,
            borderDash: [4, 4],
        }
    ];

    if (graficoReceita) {
        graficoReceita.data.datasets[0].data = d.receita;
        graficoReceita.data.datasets[1].data = d.pendente;
        graficoReceita.data.datasets[2].data = d.atrasado;
        graficoReceita.update();
        return;
    }

    const ctx = document.getElementById('graficoReceita');
    if (!ctx) return;
    ctx.style.width = '100%';

    graficoReceita = new Chart(ctx, {
        type: 'line',
        data: { labels: mesesLabels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 12 },
                        color: 'var(--sage)',
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        pointStyleWidth: 12,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` R$ ${ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#9E9E9E', font: { size: 12 } }
                },
                y: {
                    grid: { color: '#f0f0f0' },
                    ticks: {
                        color: '#9E9E9E',
                        font: { size: 12 },
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                    }
                }
            }
        }
    });
}

function toggleAnoDropdown() {
    const dropdown = document.getElementById('ano-dropdown');
    if (!dropdown) return;
    dropdown.classList.toggle('open');
}

function iniciarFiltroAnos() {
    const container = document.getElementById('ano-dropdown');
    if (!container) return;

    container.querySelectorAll('.ano-opcao').forEach(opcao => {
        opcao.addEventListener('click', () => {
            container.querySelectorAll('.ano-opcao').forEach(o => o.classList.remove('active'));
            opcao.classList.add('active');

            anoAtivo = parseInt(opcao.dataset.ano);
            document.getElementById('btn-ano-label').textContent = anoAtivo;

            container.classList.remove('open');
            atualizarGrafico();
        });
    });

    document.addEventListener('click', (e) => {
        const wrapper = document.querySelector('.ano-dropdown-wrapper');
        if (!wrapper) return;
        if (!wrapper.contains(e.target)) {
            document.getElementById('ano-dropdown')?.classList.remove('open');
        }
    });

    atualizarGrafico();
}
