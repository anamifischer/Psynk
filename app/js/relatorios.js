//Gráfico Financeiro
const dadosReceita = {
    2024: {
        receita:  [2800, 3100, 3400, 3200, 3900, 4100, 3700, 4200, 3800, 4400, 4100, 4800],
        pendente: [300,  200,  400,  350,  250,  300,  200,  400,  300,  250,  350,  500],
    },
    2025: {
        receita:  [3200, 2900, 3800, 4100, 4300, 3900, 4600, 4800, 4200, 5000, 4700, 5300],
        pendente: [400,  350,  300,  500,  200,  450,  350,  300,  400,  200,  300,  600],
    },
    2026: {
        receita:  [3500, 3800, 4200, 3900, 4500, 4100, 4800, 3100, 0, 0, 0, 0],
        pendente: [300,  250,  350,  400,  200,  300,  280,  630, 0, 0, 0, 0],
    },
};

const mesesLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
let graficoReceita = null;
let anoAtivo = new Date().getFullYear();

function atualizarGrafico() {
    const d = dadosReceita[anoAtivo];
    if (!d) return;

    const datasets = [
        {
            label: 'Receita recebida',
            data: d.receita,
            borderColor: '#316372',
            backgroundColor: 'rgba(76, 175, 80, 0.06)',
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
            backgroundColor: 'rgba(242, 140, 92, 0.06)',
            borderWidth: 2,
            pointBackgroundColor: '#d97c56',
            pointRadius: 2,
            tension: 0.1,
            fill: true,
            borderDash: [4, 4],
        }
    ];

    if (graficoReceita) {
        graficoReceita.data.datasets[0].data = d.receita;
        graficoReceita.data.datasets[1].data = d.pendente;
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
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 12 },
                        color: '#9E9E9E',
                        boxWidth: 12,
                        usePointStyle: true,
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

            dropdown.classList.remove('open');
            atualizarGrafico();
        });
    });

    // fecha ao clicar fora
    document.addEventListener('click', (e) => {
        const wrapper = document.querySelector('.ano-dropdown-wrapper');
        if (!wrapper) return;
        if (!wrapper.contains(e.target)) {
            document.getElementById('ano-dropdown')?.classList.remove('open');
        }
    });

    atualizarGrafico();
}