
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des Rapports</title>
    <link rel="stylesheet" href="css/saisir_validations_rapports.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
        <div class="header-section">
            <!--<h1>SAISIR ET VALIDATION DES RAPPORTS</h1>-->
            <h2>VALIDATION DES RAPPORTS</h2>
        </div>

        <form id="reportForm" class="validation-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>ÉTUDIANT :</label>
                    <input type="text" placeholder="S(X)" required>
                </div>
                <div class="form-group">
                    <label>THÈME DE MÉMOIRE :</label>
                    <input type="text" placeholder="S(X)" required>
                </div>
                <div class="form-group">
                    <label>DATE DE DÉPÔT :</label>
                    <input type="date" required>
                </div>
                <div class="form-group">
                    <label>ÉVALUATION :</label>
                    <select required>
                        <option value="">Choisir statut</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Validé</option>
                        <option value="rejected">Rejeté</option>
                    </select>
                </div>
            </div>


            <div class="form-actions">
                <button type="submit">
                    <i class="fas fa-check-circle"></i>
                    Valider le rapport
                </button>
            </div>
        </form>

    <script src="js/saisir_validation_rapport.js"></script>
</body>
</html>