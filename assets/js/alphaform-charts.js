// assets/js/alphaform-charts.js

document.addEventListener('DOMContentLoaded', function () {
    const chartContainer = document.getElementById('alphaform-chart');
    if (!chartContainer) return;
  
    fetch(`${alpha_form_nonce.ajaxurl}?action=alphaform_get_dashboard_stats&nonce=${alpha_form_nonce.nonce}`)
      .then(res => res.json())
      .then(json => {
        if (!json.success) return console.warn('Erro ao carregar estatísticas');
  
        const labels = json.data.submissions_per_day.map(item => item.data);
        const values = json.data.submissions_per_day.map(item => item.total);
  
        const ctx = chartContainer.getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: 'Submissões por dia',
              data: values,
              borderWidth: 2,
              borderColor: '#0073aa',
              backgroundColor: 'rgba(0, 115, 170, 0.1)',
              fill: true,
              tension: 0.4,
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: context => `${context.parsed.y} submissões`
                }
              }
            },
            scales: {
              x: { grid: { display: false } },
              y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: '#eee' }
              }
            }
          }
        });
      })
      .catch(err => console.error('Erro ao buscar estatísticas:', err));
  });
  