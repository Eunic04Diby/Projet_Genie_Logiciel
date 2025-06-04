        let numeroSequence = 4;
        let modeEdition = false;
        let ligneEnEdition = null;

        function ajouterEnregistrement() {
            const identifiant = document.getElementById('identifiant').value.trim();
            const libelle = document.getElementById('libelle').value.trim();

            if (!identifiant || !libelle) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            // Vérifier les doublons
            const existingRows = document.querySelectorAll('#tableBody tr');
            for (let row of existingRows) {
                if (row.cells[1].textContent === identifiant) {
                    alert('Cet identifiant existe déjà');
                    return;
                }
            }

            const tableBody = document.getElementById('tableBody');
            
            if (modeEdition && ligneEnEdition) {
                // Mode modification
                ligneEnEdition.cells[1].textContent = identifiant;
                ligneEnEdition.cells[2].textContent = libelle;
                modeEdition = false;
                ligneEnEdition = null;
                document.querySelector('.btn-add').textContent = 'Ajouter';
                document.querySelector('.btn-add').style.background = 'var(--primary-green)';
            } else {
                // Mode ajout
                const newRow = tableBody.insertRow();
                newRow.innerHTML = `
                    <td class="numero-col">${numeroSequence}</td>
                    <td>${identifiant}</td>
                    <td>${libelle}</td>
                    <td class="action-col">
                        <div class="action-buttons">
                            <button class="btn-edit" onclick="modifierLigne(this)">Modifier</button>
                            <button class="btn-delete" onclick="supprimerLigne(this)">Supprimer</button>
                        </div>
                    </td>
                `;
                numeroSequence++;
                
                // Afficher message de succès
                showSuccessMessage();
            }

            // Réinitialiser le formulaire
            document.getElementById('identifiant').value = '';
            document.getElementById('libelle').value = '';
        }

        function supprimerLigne(button) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ?')) {
                const row = button.closest('tr');
                row.style.transform = 'translateX(-100%)';
                row.style.opacity = '0';
                
                setTimeout(() => {
                    row.remove();
                    reorganiserNumeros();
                }, 300);
            }
        }

        function modifierLigne(button) {
            const row = button.closest('tr');
            const identifiant = row.cells[1].textContent;
            const libelle = row.cells[2].textContent;
            
            // Remplir le formulaire
            document.getElementById('identifiant').value = identifiant;
            document.getElementById('libelle').value = libelle;
            
            // Passer en mode édition
            modeEdition = true;
            ligneEnEdition = row;
            
            // Changer le bouton
            const btnAdd = document.querySelector('.btn-add');
            btnAdd.textContent = 'Modifier';
            btnAdd.style.background = '#3498db';
            
            // Scroll vers le formulaire
            document.querySelector('.form-container').scrollIntoView({ 
                behavior: 'smooth' 
            });
        }

        function reorganiserNumeros() {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
            numeroSequence = rows.length + 1;
        }

        function showSuccessMessage() {
            const message = document.getElementById('successMessage');
            message.style.display = 'block';
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }

        // Permettre l'ajout avec la touche Entrée
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && (e.target.id === 'identifiant' || e.target.id === 'libelle')) {
                ajouterEnregistrement();
            }
        });

        // Annuler le mode édition avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modeEdition) {
                modeEdition = false;
                ligneEnEdition = null;
                document.getElementById('identifiant').value = '';
                document.getElementById('libelle').value = '';
                document.querySelector('.btn-add').textContent = 'Ajouter';
                document.querySelector('.btn-add').style.background = 'var(--primary-green)';
            }
        });

        // Animation pour les nouvelles lignes
        function animateNewRow(row) {
            row.style.opacity = '0';
            row.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100);
        }