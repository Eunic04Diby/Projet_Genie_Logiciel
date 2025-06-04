document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reportForm');
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        if(validateForm(data)) {
            showSuccessMessage();
            form.reset();
        }
    });

    function validateForm(data) {
        // Implémenter la validation avancée ici
        return Object.values(data).every(value => value.trim() !== '');
    }

    function showSuccessMessage() {
        const toast = document.createElement('div');
        toast.className = 'success-toast';
        toast.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Rapport validé avec succès !
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
            setTimeout(() => toast.remove(), 3000);
        }, 100);
    }
});
// Gestion du menu actif
document.querySelectorAll('.sidebar li').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelector('.sidebar .active').classList.remove('active');
        this.classList.add('active');
    });
});