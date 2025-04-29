jQuery(document).ready(function () {
    jQuery.ajax({
        url: ajaxurl,
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'alphaform_get_form_widget_count',
            result: 'form',
            nonce: alpha_form_nonce.nonce,
        },
        success: function (res) {
            if (res.success) {
                const data = res.data;

                jQuery('#alpha-form-count').text(data.total_forms);
                jQuery('#alpha-response-count').text(data.total_responses);
                jQuery('#alpha-integrations-count').text(data.total_integrations);
                jQuery('#alpha-last-submit').text(data.last_submit || 'N/A');

                jQuery('.alpha-skeleton').removeClass('alpha-skeleton');
            }
        }
    });

    const promoCard = jQuery('#alpha-promo-card');
    const promoUrl = alpha_form_nonce.plugin_url + 'assets/data/promo.json';
    console.log(promoUrl)

    fetch(promoUrl)
        .then(response => response.json())
        .then(data => {
            let selectedDate = null;



            if (!selectedDate) return;

            const promo = data[selectedDate];
            promoCard.find('.alpha-promo-title').text(promo.title);
            promoCard.find('.alpha-promo-text').text(promo.text);
            promoCard.find('.alpha-promo-cta').text(promo.cta_text);
            promoCard.attr('href', promo.cta_url);

            if (promo.bg_image) {
                promoCard.css('background-image', `url(${promo.bg_image})`);
            }

            promoCard.show(); // exibe o card
        })
        .catch(error => {
            console.warn('[AlphaForm] Falha ao carregar promoções:', error);
        });
});


document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('alphaFormChart');
    const devicesCanvas = document.getElementById('alphaFormDevices');
    const devicesDuration = document.getElementById('alphaFormDuration');
    const select = document.getElementById('alphaFormDateRange');
    const customFields = document.getElementById('customDateFields');
    const btnApply = document.getElementById('applyDateRange');
    const barStates = document.getElementById('barStates');
    let chartInstance = null;
    let chartInstanceDevice = null;
    let chartInstanceDuration = null;
    let chartInstanceStates = null;
    let selectedForms = [];

    async function updateDashboard(inicio, fim, widgetIds = []) {
        const params = new URLSearchParams({
            action: 'alphaform_get_dashboard_stats',
            nonce: alpha_form_nonce.nonce,
            inicio: inicio,
            fim: fim
        });

        widgetIds.forEach(id => params.append('widget_ids[]', id));

        const res = await fetch(`${alpha_form_nonce.ajaxurl}?${params.toString()}`);
        const json = await res.json();

        if (!json.success) {
            console.error('[AlphaForm] Erro ao buscar dados do dashboard');
            return;
        }

        const stats = json.data;
        console.log(json.data)
        const totalDispositivos = [
            stats.devices.desktop,
            stats.devices.tablet,
            stats.devices.mobile
        ].map(n => parseInt(n) || 0).reduce((acc, curr) => acc + curr, 0);

        const startForms = parseInt(stats.start_forms) || 0;
        const totalConcluido = parseInt(stats.totalconcluido) || 0;

        let taxaConversao = 0;

        if (startForms > 0) {
            taxaConversao = (totalConcluido / startForms) * 100;
        }

        taxaConversao = Math.round(taxaConversao * 10) / 10; // arredonda para 1 casa decimal


        // Atualiza cards (coloque os IDs corretos dos elementos HTML)
        document.getElementById('stat-today').textContent = stats.leads || 0;
        document.getElementById('stat-total').textContent = stats.totalgeral || 0;
        document.getElementById('stat-month').textContent = stats.month;
        document.getElementById('stat-week').textContent = stats.week;
        document.getElementById('stat-desktop').textContent = stats.devices.desktop;
        document.getElementById('stat-tablet').textContent = stats.devices.tablet;
        document.getElementById('stat-mobile').textContent = stats.devices.mobile;
        document.getElementById('tempo-min').textContent = stats.duration.min + "s";
        document.getElementById('tempo-max').textContent = stats.duration.max + "s";
        document.getElementById('tempo-avg').textContent = stats.duration.avg + "s";
        document.getElementById('stat-page_view').textContent = stats.page_views;
        document.getElementById('stat-form_iniciados').textContent = stats.start_forms;
        document.getElementById('stat-form_concluidos').textContent = stats.totalconcluido;
        document.getElementById('stat-conversao').textContent = taxaConversao + "%";

        // Atualiza o gráfico
        if (chartInstance) chartInstance.destroy();
        if (chartInstanceDevice) chartInstanceDevice.destroy();
        if (chartInstanceDuration) chartInstanceDuration.destroy();
        if (chartInstanceStates) chartInstanceStates.destroy();

        chartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: stats.labels,
                datasets: [
                    {
                        label: 'Page View',
                        data: stats.submissions_per_day,
                        borderWidth: 2,
                        borderColor: '#3874FF',
                        tension: 0,
                        pointRadius: 0,
                    },
                    {
                        label: 'Concluídos',
                        data: stats.submissions_per_day_concluido,
                        borderWidth: 2,
                        borderColor: '#10B981',
                        tension: 0,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Formulários iniciados',
                        data: stats.formularios_iniciados,
                        borderWidth: 2,
                        borderColor: '#F59E0B',
                        tension: 0,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: '#111',
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: { display: false },
                    x: {
                        ticks: {
                            callback: function (value) {
                                const label = this.getLabelForValue(value);
                                const date = new Date(label);
                                const dia = String(date.getDate()).padStart(2, '0');
                                const mes = date.toLocaleDateString('pt-BR', { month: 'short' }).toLowerCase();
                                return `${dia} ${mes}`;
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6,
                        hitRadius: 20
                    }
                }
            }
        });
        chartInstanceDevice = new Chart(devicesCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablet'],
                datasets: [{
                    data: [
                        stats.devices.desktop,
                        stats.devices.mobile,
                        stats.devices.tablet
                    ],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b'], // azul, verde, amarelo
                    borderWidth: 4,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: '#111',
                        padding: 10,
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            color: '#6b7280' // cinza leve
                        }
                    },
                    // Texto central no meio do gráfico
                    doughnutLabel: {
                        labels: [
                            {
                                text: stats.visits.toString(),
                                font: {
                                    size: 22,
                                    weight: 'bold'
                                },
                                color: '#111'
                            },
                            {
                                text: 'Total de visitas',
                                font: {
                                    size: 12
                                },
                                color: '#6b7280'
                            }
                        ]
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const { width, height, ctx } = chart;
                    const totalDispositivos = [
                        stats.devices.desktop,
                        stats.devices.tablet,
                        stats.devices.mobile
                    ].map(n => parseInt(n) || 0).reduce((acc, curr) => acc + curr, 0);

                    const text = totalDispositivos || 0;

                    ctx.save();
                    ctx.font = 'bold 24px sans-serif';
                    ctx.fillStyle = '#111';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const offsetY = -16;
                    ctx.fillText(text, width / 2, height / 2 + offsetY);
                    ctx.restore();
                }
            }]


        });



        chartInstanceDuration = new Chart(devicesDuration, {
            type: 'doughnut',
            data: {
                labels: ['Minimo', 'Máximo', 'Médio'],
                datasets: [{
                    data: [
                        stats.duration.min,
                        stats.duration.max,
                        stats.duration.avg
                    ],
                    backgroundColor: ['#FFCC85', '#60C6FF', '#0096E9'], // azul, verde, amarelo
                    borderWidth: 4,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: '#111',
                        padding: 10,
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            color: '#6b7280' // cinza leve
                        }
                    },
                    // Texto central no meio do gráfico
                    doughnutLabel: {
                        labels: [
                            {
                                text: stats.visits.toString(),
                                font: {
                                    size: 22,
                                    weight: 'bold'
                                },
                                color: '#111'
                            },
                            {
                                text: 'Total de visitas',
                                font: {
                                    size: 12
                                },
                                color: '#6b7280'
                            }
                        ]
                    }
                }
            },
            plugins: [{
                id: 'centerText2',
                beforeDraw(chart) {
                    const { width, height, ctx } = chart;
                    const text = stats.duration.avg + "s" || 0;

                    ctx.save();
                    ctx.font = 'bold 24px sans-serif';
                    ctx.fillStyle = '#111';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const offsetY = -16;
                    ctx.fillText(text, width / 2, height / 2 + offsetY);
                    ctx.restore();
                }
            }]
        });

        // Atualiza gráfico de Estados mais acessados (horizontal)
        chartInstanceStates = new Chart(barStates, {
            type: 'bar',
            data: {
                labels: stats.states.map(state => state.state || 'Indefinido'),
                datasets: [{
                    data: stats.states.map(state => state.total),
                    backgroundColor: '#85A9FF',
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // transforma em barras horizontais
                plugins: {
                    tooltip: {
                        backgroundColor: '#111',
                        padding: 10,
                        titleFont: { weight: 'bold' },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                }
            }
        });


    }


    // Data range (padrão ou customizado)
    if (select) {
        select.addEventListener('change', function () {
            const valor = this.value;
            customFields.style.display = valor === 'custom' ? 'block' : 'none';

            if (valor !== 'custom') {
                const dias = parseInt(valor, 10);
                const hoje = new Date();
                const inicio = new Date();
                inicio.setDate(hoje.getDate() - dias);

                updateDashboard(inicio.toISOString().slice(0, 10), hoje.toISOString().slice(0, 10), selectedForms);
            }
        });
    }
    else
        return
    btnApply.addEventListener('click', function () {
        const inicio = document.getElementById('dateStart').value;
        const fim = document.getElementById('dateEnd').value;
        if (inicio && fim) {
            updateDashboard(inicio, fim, selectedForms);
        }
    });

    // Chamada inicial (últimos 30 dias)
    const hoje = new Date();
    const inicioDefault = new Date();
    inicioDefault.setDate(hoje.getDate() - 15);
    updateDashboard(inicioDefault.toISOString().slice(0, 10), hoje.toISOString().slice(0, 10));

    // Select2 para filtros por formulário
    if (typeof jQuery === 'undefined') {
        console.error('[AlphaForm] jQuery não carregado!');
        return;
    }

    jQuery(function ($) {
        $('#alpha-form-filter').select2({
            placeholder: 'Selecione o(s) formulário(s)',
            allowClear: true,
            width: '100%',
            ajax: {
                url: alpha_form_nonce.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function () {
                    return {
                        action: 'alphaform_get_forms_list',
                        nonce: alpha_form_nonce.nonce
                    };
                },
                processResults: function (res) {
                    return {
                        results: res.success ? res.data : []
                    };
                }
            }
        });

        $('#alpha-form-filter').on('change', function () {
            selectedForms = $(this).val() || [];

            // Atualiza o gráfico com os novos formulários e datas atuais do seletor
            const valor = select.value;
            if (valor !== 'custom') {
                const dias = parseInt(valor, 10);
                const hoje = new Date();
                const inicio = new Date();
                inicio.setDate(hoje.getDate() - dias);
                updateDashboard(inicio.toISOString().slice(0, 10), hoje.toISOString().slice(0, 10), selectedForms);
            } else {
                const inicio = document.getElementById('dateStart').value;
                const fim = document.getElementById('dateEnd').value;
                if (inicio && fim) {
                    updateDashboard(inicio, fim, selectedForms);
                }
            }
        });
    });
});
