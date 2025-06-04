        // Génération automatique de la référence
        function generateReference() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `REC-${year}${month}${day}-${random}`;
        }

        // Initialisation de la date et référence
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateReclamation').value = today;
            document.getElementById('referenceReclamation').value = generateReference();
        });

        // Gestion du formulaire
        document.getElementById('reclamationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulation de l'envoi
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            
            // Scroll vers le message de succès
            successMessage.scrollIntoView({ behavior: 'smooth' });
            
            // Réinitialiser le formulaire après 2 secondes
            setTimeout(() => {
                this.reset();
                document.getElementById('dateReclamation').value = new Date().toISOString().split('T')[0];
                document.getElementById('referenceReclamation').value = generateReference();
                successMessage.style.display = 'none';
            }, 3000);
        });

        // Animation des éléments du sidebar
        document.querySelectorAll('.sidebar li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
                this.classList.add('active');
            });
        });