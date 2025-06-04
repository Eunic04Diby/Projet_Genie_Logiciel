        // Animation des compteurs
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.metric-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                let current = 0;
                const increment = target / 60;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 40);
            });
        });

        // Configuration du graphique en barres - Évolution des dépôts
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai'],
                datasets: [{
                    label: 'Rapports déposés',
                    data: [12, 19, 15, 22, 21],
                    backgroundColor: [
                        '#4CAF50',
                        '#3498db',
                        '#f39c12', 
                        '#9b59b6',
                        '#e74c3c'
                    ],
                    borderRadius: 10,
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
                        },
                        ticks: {
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        // Configuration du graphique linéaire - Répartition par spécialité
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'doughnut',
            data: {
                labels: ['Informatique', 'Finance', 'Marketing', 'RH', 'Ingénierie'],
                datasets: [{
                    label: 'Étudiants par spécialité',
                    data: [35, 20, 25, 12, 24],
                    backgroundColor: [
                        '#4CAF50',
                        '#3498db',
                        '#f39c12',
                        '#9b59b6',
                        '#e74c3c'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
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
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        // Fonctionnalité de téléchargement
        document.querySelector('.download-btn').addEventListener('click', function() {
            // Simulation du téléchargement
            this.style.background = '#4CAF50';
            this.innerHTML = '✓';
            
            setTimeout(() => {
                this.style.background = '#2c3e50';
                this.innerHTML = '⬇';
            }, 2000);
            
            // Ici vous pourriez ajouter la logique de téléchargement réelle
            console.log('Téléchargement du rapport d\'activités...');
        });

        // Animation au scroll
        window.addEventListener('scroll', function() {
            const cards = document.querySelectorAll('.metric-card');
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        });

        // Effet de survol interactif pour les cartes
        document.querySelectorAll('.metric-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });