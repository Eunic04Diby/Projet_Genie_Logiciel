document.addEventListener('DOMContentLoaded', () => {
    chargerEtudiantContent();
});

function chargerEtudiantContent() {
    fetch('Etudiant_content.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('main-content').innerHTML = html;
            attacherEvenementsSuppression();
        })
        .catch(err => {
            console.error("Erreur de chargement :", err);
        });
}

function attacherEvenementsSuppression() {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nom = this.dataset.nom;

            if (confirm(`Voulez-vous vraiment supprimer ${nom} ?`)) {
                const formData = new FormData();
                formData.append('action', 'supprimer');
                formData.append('num_etud', id);

                fetch('Etudiant_content.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('main-content').innerHTML = html;
                    attacherEvenementsSuppression(); // réattache les événements sur les nouveaux boutons
                })
                .catch(err => {
                    alert("Erreur lors de la suppression");
                    console.error(err);
                });
            }
        });
    });
}
