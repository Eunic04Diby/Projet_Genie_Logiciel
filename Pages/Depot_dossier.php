
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépôt des Dossiers</title>
    <link rel="stylesheet" href="../css/depot_dossiers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
        <h1 class="page-title">DÉPÔT DES DOSSIERS</h1>

        <form class="depot-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>ANNÉE ACADÉMIQUE :</label>
                    <input type="text" placeholder="S(X)">
                </div>

                <div class="form-group">
                    <label>THÈME DE MÉMOIRE :</label>
                    <input type="text" placeholder="E(X)">
                </div>

                <div class="form-group">
                    <label>ENTREPRISE DE STAGE :</label>
                    <div class="entreprise-group">
                        <input type="text" placeholder="E(X)">
                        <button type="button" class="add-entreprise">
                            <i class="fas fa-plus-circle"></i> AJOUTER
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>DATE DEBUT STAGE :</label>
                    <input type="date">
                </div>

                <div class="form-group">
                    <label>DATE FIN STAGE :</label>
                    <input type="date">
                </div>
            </div>

            <div class="documents-section">
                <h2>DOCUMENTS ASSOCIÉS</h2>
                <div class="file-upload">
                    <label>RAPPORT DE STAGE :</label>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> SOUMETTRE
            </button>
        </form>

    <script src="js/depot_dossiers.js"></script>
</body>
</html>