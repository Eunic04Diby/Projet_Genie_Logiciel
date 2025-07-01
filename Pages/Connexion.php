<?php
session_start();
$erreur = $_SESSION['erreur_connexion'] ?? '';
unset($_SESSION['erreur_connexion']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Utilisateur</title>
        <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">CONNEXION</h2>

        <?php if ($erreur): ?>
      <p class="error-message"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

        <form id="loginForm" action="login_traitement.php" method="POST" class="login-form">
            <div class="input-group">
                <input type="text" name="username" class="input-field" placeholder="Nom d'utilisateur" required>
                <div class="input-icon user-icon"></div>
            </div>
            
            <div class="input-group">
                <input type="password" name = "password" class="input-field" placeholder="Mot de passe" required>
                <div class="input-icon lock-icon" onclick="togglePassword(this)"></div>
            </div>
            
            <button type="submit" class="login-button">SE CONNECTER</button>

            <div class="forgot-password">
            <a href="#" onclick="forgotPassword()">Mot de passe oublié ?</a>
        </div>
        </form>  
    </div>

    <script>
         function togglePassword(icon) {
      const field = icon.previousElementSibling;
      field.type = field.type === 'password' ? 'text' : 'password';
    }

        function forgotPassword() {
            alert('Fonctionnalité de récupération de mot de passe à implémenter');
        }


        // Animation d'apparition des champs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.input-group');
            inputs.forEach((input, index) => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    input.style.transition = 'all 0.5s ease';
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 300 + (index * 150));
            });
        });
    </script>
</body>
</html>