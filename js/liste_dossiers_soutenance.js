document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');

    // Gestion de la recherche
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.table-row');
        
        rows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            row.style.display = textContent.includes(searchTerm) ? '' : 'none';
        });
    });

    // Gestion des actions
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('.table-row');
        
        if(e.target.classList.contains('delete-btn')) {
            row.style.animation = 'fadeIn 0.3s reverse';
            setTimeout(() => row.remove(), 300);
        }
        
        if(e.target.classList.contains('edit-btn')) {
            const cells = row.querySelectorAll('span');
            // Récupération des données pour édition
            const data = {
                matricule: cells[0].textContent,
                nom: cells[1].textContent,
                prenom: cells[2].textContent,
                theme: cells[3].textContent
            };
            // Ici vous pouvez implémenter la logique d'édition
            alert(`Mode édition pour : ${data.nom} ${data.prenom}`);
        }
    });

    // Ajout dynamique de lignes (exemple)
    function addNewRow(data) {
        const row = document.createElement('div');
        row.className = 'table-row';
        row.innerHTML = `
            <span>${data.matricule}</span>
            <span>${data.nom}</span>
            <span>${data.prenom}</span>
            <span>${data.theme}</span>
            <div class="action-buttons">
                <i class="fas fa-edit edit-btn"></i>
                <i class="fas fa-trash delete-btn"></i>
            </div>
        `;
        row.style.animation = 'fadeIn 0.5s';
        tableBody.appendChild(row);
    }
});