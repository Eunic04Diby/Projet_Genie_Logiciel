<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à Jour du Personnel Administratif</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="../css/personnel_admin.css">
</head>
<body>
    <header>
        <div class="logo">LOGO</div>
        <div class="search-bar">
            <input type="text" placeholder="Rechercher...">
            <button class="search-btn">RECHERCHER</button>
        </div>
        <div class="user-profile">NOM UTILISATEUR</div>
    </header>

    <nav class="sidebar">
        <ul>
            <li class="active">
                <span>📋</span>
                <span>MENU</span>
            </li>
            <li>
                <span>👥</span>
                <span>Personnel</span>
            </li>
            <li>
                <span>📊</span>
                <span>Rapports</span>
            </li>
            <li>
                <span>⚙️</span>
                <span>Paramètres</span>
            </li>
        </ul>
    </nav>

    <main>
        <h1 class="page-title">Mise à Jour du Personnel Administratif</h1>

        <div class="success-message" id="successMessage">
            Personnel mis à jour avec succès !
        </div>

        <div class="form-container">
            <form id="personnelForm">
                <!-- Section Information générale/admin -->
                <div class="section-title">Information générale/admin</div>
                
                <div class="form-row four-columns">
                    <div class="form-group">
                        <label for="matricule1">Matricule :</label>
                        <input type="text" id="matricule1" name="matricule1" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label for="matricule2">Nom :</label>
                        <input type="text" id="matricule2" name="matricule2" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label for="matricule3">Prenoms :</label>
                        <input type="text" id="matricule3" name="matricule3" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label for="matricule4">Date de naissance:</label>
                        <input type="text" id="matricule4" name="matricule4" placeholder="E(X)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Genre :</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="masculin" name="genre" value="M">
                            <label for="masculin">M</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="feminin" name="genre" value="F">
                            <label for="feminin">F</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="neutre" name="genre" value="N">
                            <label for="neutre">N</label>
                        </div>
                    </div>
                </div>

                <!-- Section Carrière -->
                <div class="section-title" style="margin-top: 3rem;">Carrière</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="poste">Poste Occupé :</label>
                        <input type="text" id="poste" name="poste" placeholder="E(X)">
                    </div>
                    <div class="form-group">
                        <label for="date">Date :</label>
                        <input type="date" id="date" name="date">
                    </div>
                </div>

                <div class="form-row single">
                    <div class="form-group">
                        <label for="dateEmbauche">Date d'Embauche :</label>
                        <input type="date" id="dateEmbauche" name="dateEmbauche">
                    </div>
                </div>

                <!-- Section Contact -->
                <div class="section-title" style="margin-top: 3rem;">Contact</div>
                
                <div class="form-row single">
                    <div class="form-group">
                        <label for="telephone">Numéro Téléphone :</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="E(X)">
                    </div>
                </div>

                <div class="button-container">
                    <button type="submit" class="submit-btn">Valider</button>
                </div>
            </form>
        </div>

        <!-- Section Liste du personnel -->
        <div class="table-container">
            <div class="table-header">Liste du personnel</div>
            <table class="personnel-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénoms</th>
                        <th>Poste Occupé</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>EMP001</td>
                        <td>DUPONT</td>
                        <td>Jean-Pierre</td>
                        <td>Administrateur</td>
                        <td>
                            <button class="action-btn">Modifier</button>
                            <button class="action-btn delete">Supprimer</button>
                        </td>
                    </tr>
                    <tr>
                        <td>EMP002</td>
                        <td>MARTIN</td>
                        <td>Marie-Claire</td>
                        <td>Secrétaire</td>
                        <td>
                            <button class="action-btn">Modifier</button>
                            <button class="action-btn delete">Supprimer</button>
                        </td>
                    </tr>
                    <tr>
                        <td>EMP003</td>
                        <td>BERNARD</td>
                        <td>Paul</td>
                        <td>Comptable</td>
                        <td>
                            <button class="action-btn">Modifier</button>
                            <button class="action-btn delete">Supprimer</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

<script src="../js/personnel_admin.js"></script>
</body>
</html>