document.addEventListener('DOMContentLoaded', () => {
    // Gestion du menu actif
    document.querySelectorAll('.sidebar li').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelector('.sidebar .active').classList.remove('active');
            this.classList.add('active');
        });
    });

    // Animation au survol des éléments
    document.querySelectorAll('.sidebar li').forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateX(10px)';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateX(0)';
        });
    });

    // Gestion des boutons
    document.querySelector('.btn-validate').addEventListener('click', () => {
        // Logique de validation
        alert('Dossier validé avec succès !');
    });

    document.querySelector('.btn-cancel').addEventListener('click', () => {
        if(confirm('Êtes-vous sûr de vouloir annuler ?')) {
            window.location.reload();
        }
    });
});