<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/stages.css">
</head>
<body>

        <section class="form-section">
            <h2>MISE À JOUR DES STAGES</h2>
            <form id="stageForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>MATRICULE :</label>
                        <input type="text" id="matricule" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label>NOM :</label>
                        <input type="text" id="nom" placeholder="S(X)">
                    </div>
                    <div class="form-group">
                        <label>PRENOMS :</label>
                        <input type="text" id="prenoms" placeholder="S(X)">
                    </div>
                    <div class="form-group">
                        <label>NIVEAU :</label>
                        <select id="niveau">
                            <option value="L1">L1</option>
                            <option value="L2">L2</option>
                            <option value="L3">L3</option>
                            <option value="M1">L3</option>
                            <option value="M2">L3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ENTREPRISE :</label>
                        <input type="text" id="entreprise" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label>THÈME DE STAGE :</label>
                        <input type="text" id="theme" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label>DATE DEBUT :</label>
                        <input type="date" id="debut">
                    </div>
                    <div class="form-group">
                        <label>DATE FIN :</label>
                        <input type="date" id="fin">
                    </div>
                </div>
                <button type="submit" id="submitBtn">AJOUTER</button>
            </form>
        </section>

        <section class="table-section">
            <div class="table-header">
                <span>MATRICULE</span>
                <span>NOM & PRENOMS</span>
                <span>ENTREPRISE</span>
                <span>DATE DEBUT</span>
                <span>DATE FIN</span>
                <span>ACTION</span>
            </div>
            <div class="table-body" id="tableBody">
                <!-- Données dynamiques -->
            </div>
        </section>

    <script src="stages.js"></script>
</body>
</html>