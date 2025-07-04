<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Téléchargement de Documents</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link rel="stylesheet" href="css/Telechargement_dossier.css" />

</head>
<body>

    <div class="card">
      <h2>TÉLÉCHARGER DES DOCUMENTS</h2>
      <div class="form-section">
        <div class="form-group">
          <label for="annee">ANNÉE ACADÉMIQUE :</label>
          <select id="annee" name="annee">
            <option value="">-- Choisir --</option>
            <option value="2023-2024">2023-2024</option>
            <option value="2024-2025">2024-2025</option>
          </select>
        </div>
        <div class="form-group">
          <label for="type">TYPE DE DOCUMENT :</label>
          <select id="type" name="type">
            <option value="">-- Choisir --</option>
            <option value="rapport">Rapport</option>
            <option value="attestation">Attestation</option>
          </select>
        </div>
        <div class="form-group">
          <label for="etudiant">ÉTUDIANT CONCERNÉ :</label>
          <input type="text" id="etudiant" name="etudiant" />
        </div>
      </div>

      <div class="buttons">
        <button type="reset">RÉINITIALISER</button>
        <button type="submit">RECHERCHER</button>
      </div>

      <div class="table-title">Documents disponibles</div>
      <table>
        <thead>
          <tr>
            <th>TYPE DE DOCUMENT</th>
            <th>NOM ÉTUDIANT CONCERNÉ</th>
            <th>ANNÉE UNIVERSITAIRE</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Rapport</td>
            <td>Jean Dupont</td>
            <td>2023-2024</td>
            <td><button>Télécharger</button></td>
          </tr>
          <tr>
            <td>Attestation</td>
            <td>Marie Durand</td>
            <td>2024-2025</td>
            <td><button>Télécharger</button></td>
          </tr>
        </tbody>
      </table>
    </div>


</body>
</html>
