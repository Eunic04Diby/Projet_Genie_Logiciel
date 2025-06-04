// Configuration du graphique en secteurs
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Validés', 'En cours', 'Refusés'],
                datasets: [{
                    data: [96, 20, 12],
                    backgroundColor: [
                        '#4CAF50',
                        '#f39c12',
                        '#e74c3c'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        // Configuration du graphique en barres
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Soutenances',
                    data: [8, 12, 15, 18, 10, 7],
                    backgroundColor: '#4CAF50',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Animation des cartes statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        stat.textContent = finalValue;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(currentValue);
                    }
                }, 30);
            });
        });

        // Gestion du menu latéral
        document.querySelectorAll('.sidebar li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Fonctionnalité d'export
        document.querySelector('.export-btn').addEventListener('click', function() {
            alert('Fonctionnalité d\'export en cours de développement');
        });

        // Fonctionnalité d'édition
        document.querySelector('.edit-btn').addEventListener('click', function() {
            alert('Mode édition activé');
        });