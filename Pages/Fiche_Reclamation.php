<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Réclamation</title>
     <link rel="stylesheet" href="../css/fiche_reclamation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
        <h1 class="page-title">Fiche de Réclamation</h1>

        <div class="success-message" id="successMessage">
            Réclamation enregistrée avec succès !
        </div>

        <form class="form-container" id="reclamationForm">
            <!-- Information de l'étudiant -->
            <div class="section-title">Information de l'étudiant</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="numeroEtudiant">N° Étudiant :</label>
                    <input type="text" id="numeroEtudiant" name="numeroEtudiant" placeholder="Entrez le numéro étudiant" required>
                </div>
                <div class="form-group">
                    <label for="nomEtudiant">Nom :</label>
                    <input type="text" id="nomEtudiant" name="nomEtudiant" placeholder="Entrez le nom complet" required>
                </div>
            </div>

            <!-- Information sur la réclamation -->
            <div class="section-title">Information sur la réclamation</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="dateReclamation">Date :</label>
                    <input type="date" id="dateReclamation" name="dateReclamation" required>
                </div>
                <div class="form-group">
                    <label for="objetReclamation">Objet :</label>
                    <select id="objetReclamation" name="objetReclamation" required>
                        <option value="">Sélectionnez un objet</option>
                        <option value="notes">Réclamation de notes</option>
                        <option value="inscription">Problème d'inscription</option>
                        <option value="emploi-temps">Emploi du temps</option>
                        <option value="services">Services administratifs</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="referenceReclamation">Référence :</label>
                    <input type="text" id="referenceReclamation" name="referenceReclamation" placeholder="Référence automatique" readonly>
                </div>
            </div>

            <div class="form-row single">
                <div class="form-group">
                    <label for="descriptionReclamation">Description :</label>
                    <textarea id="descriptionReclamation" name="descriptionReclamation" placeholder="Décrivez votre réclamation en détail..." required></textarea>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" class="submit-btn">Envoyer</button>
            </div>
        </form>

    <script src="../js/fiche_reclamation.js"></script>
</body>
</html>