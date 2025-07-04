<!DOCTYPE html>
<html lang="fr">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisir des Compte-Rendus</title>
    <link rel="stylesheet" href="css/saisir_CR.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>


        <div class="content">
            <h2>SAISIR DES COMPTE-RENDUS</h2>

            <div class="form-section">
                <div class="form-group">
                    <label for="date">DATE DE LA SEANCE :</label>
                    <input type="date" id="date" name="date">
                </div>
                <div class="form-group">
                    <label for="encadreur">ENCADREUR :</label>
                    <input type="text" id="encadreur" name="encadreur">
                </div>
                <div class="form-group">
                    <label for="directeur">DIRECTEUR DE MEMOIRE :</label>
                    <input type="text" id="directeur" name="directeur">
                </div>
            </div>

            <hr>
            <strong>Etudiants validé</strong>

            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>NOM ET PRENOM</th>
                        <th>SUJET DE MEMOIRE</th>
                        <th>APPROBATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><input type="text" name="nom1"></td>
                        <td><input type="text" name="sujet1"></td>
                        <td><input type="text" name="approbation1"></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><input type="text" name="nom2"></td>
                        <td><input type="text" name="sujet2"></td>
                        <td><input type="text" name="approbation2"></td>
                    </tr>
                </tbody>
            </table>

            <div class="add-row">+ AJOUTER UNE NOUVELLE LIGNE</div>

            <div class="validate-btn">
                <button>VALIDER</button>
            </div>
        </div>

</body>
</html>
