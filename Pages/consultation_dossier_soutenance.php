<?php 
    include("../config/connexion_BD.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation des Dossiers</title>
     <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
        include ("../includes/header.html");
    
        include ("../includes/sidebar.html");
    ?>

    <main>
        <div class="header-info">
            <div class="code-designation">
                <h1>CONSULTATION DES DOSSIERS DE SOUTENANCE</h1>
            </div>
        </div>

        <div class="dossier-container">
            <section class="student-info">
                <h2><i class="fas fa-user-graduate"></i> INFORMATIONS DE L'ÉTUDIANT</h2>
                <div class="info-group">
                    <label>NOM :</label>
                    <p class="info-value">S(X)</p>
                </div>
                <div class="info-group">
                    <label>PRÉNOM :</label>
                    <p class="info-value">S(X)</p>
                </div>
                <div class="info-group">
                    <label>EMAIL :</label>
                    <p class="info-value">S(X)</p>
                </div>
            </section>

            <section class="memoire-info">
                <h2><i class="fas fa-book-open"></i> INFORMATIONS SUR LE MÉMOIRE</h2>
                <div class="info-group">
                    <label>THÈME :</label>
                    <p class="info-value">S(X)</p>
                </div>
                <div class="info-group">
                    <label>ENTREPRISE DE STAGE :</label>
                    <p class="info-value">S(X)</p>
                </div>
                <div class="info-group">
                    <label>RAPPORT DE STAGE :</label>
                    <a href="#" class="report-link">
                        <i class="fas fa-file-pdf"></i> Voir le rapport
                    </a>
                </div>
            </section>
        </div>

        <div class="action-buttons">
            <button class="btn-validate">
                <i class="fas fa-check"></i> Valider
            </button>
            <button class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </button>
        </div>
    </main>

    <script src="../js/consultation_dossiers_soutenance.js"></script>
</body>
</html>