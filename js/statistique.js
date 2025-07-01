document.addEventListener("DOMContentLoaded", function () {
    // Données fictives – à remplacer éventuellement par des données dynamiques JSON/PHP
    const rapports = {
        validés: 96,
        déposés: 128,
        refusés: 12
    };

    const soutenancesParMois = {
        labels: ["Jan", "Fév", "Mars", "Avr", "Mai", "Juin"],
        data: [5, 12, 25, 18, 10, 0]
    };

    // 🥧 Camembert - Statut des rapports
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Déposés', 'Validés', 'Refusés'],
            datasets: [{
                label: 'Rapports',
                data: [rapports.déposés, rapports.validés, rapports.refusés],
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 📊 Barres - Soutenances par mois
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: soutenancesParMois.labels,
            datasets: [{
                label: 'Soutenances',
                data: soutenancesParMois.data,
                backgroundColor: '#1abc9c'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
