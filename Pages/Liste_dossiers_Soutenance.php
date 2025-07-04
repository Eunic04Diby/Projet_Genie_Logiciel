
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossiers de Soutenance</title>
    <link rel="stylesheet" href="../css/liste_dossiers.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

        <div class="header-info">
          
            <h1>LISTE DES DOSSIERS DE SOUTENANCE</h1>
        </div>

        <section class="table-container">
            <div class="table-header">
                <span>MATRICULE</span>
                <span>NOM</span>
                <span>PRENOM</span>
                <span>THÈME DE MÉMOIRE</span>
                <span>ACTIONS</span>
            </div>
            
            <div class="table-body" id="tableBody">
                <!-- Lignes dynamiques -->
                <div class="table-row">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <div class="action-buttons">
                        <i class="fas fa-edit edit-btn"></i>
                        <i class="fas fa-trash delete-btn"></i>
                    </div>
                </div>
            </div>
        </section>

    <script src="js/liste_dossiers_soutenance.js"></script>
</body>
</html>