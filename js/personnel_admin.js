document.getElementById('personnelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simuler la soumission du formulaire
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            
            // Cacher le message après 3 secondes
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
            
            // Réinitialiser le formulaire
            this.reset();
        });

        // Gestion des clics sur les boutons d'action
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('delete')) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce personnel ?')) {
                        this.closest('tr').remove();
                    }
                } else {
                    alert('Fonction de modification à implémenter');
                }
            });
        });

        // Gestion des éléments de la sidebar
        document.querySelectorAll('.sidebar li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
                this.classList.add('active');
            });
        });