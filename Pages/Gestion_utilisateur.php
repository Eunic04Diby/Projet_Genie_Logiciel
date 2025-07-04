<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link rel="stylesheet" href="../css/gestion_utilisateur.css" />

</head>
<body>

        <div class="card">
            <h2>GESTION DES UTILISATEURS</h2>
            
            <div class="form-section">
                <div class="form-group">
                    <label>IDENTIFIANT</label>
                    <input type="text" placeholder="E(X)">
                </div>
                
                <div class="form-group">
                    <label>NOM & PRENOMS</label>
                    <input type="text" placeholder="E(X)">
                </div>
                
                <div class="form-group">
                    <label>ADRESSE EMAIL</label>
                    <input type="email" placeholder="E(X)">
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-group">
                    <label>LOGIN</label>
                    <input type="text" placeholder="E(X)" style="width: 350px;">
                </div>
                
                <div class="form-group">
                    <label>MOT DE PASSE</label>
                    <input type="password" placeholder="E(X)" style="width: 350px;">
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-group">
                    <label>TYPE UTILISATEUR</label>
                    <input type="text" placeholder="E(X)">
                </div>
                
                <div class="form-group">
                    <label>GROUPE UTILISATEUR</label>
                    <input type="text" placeholder="E(X)">
                </div>
                
                <div class="form-group">
                    <label>STATUT</label>
                    <select>
                        <option>E(X)</option>
                        <option>Actif</option>
                        <option>Inactif</option>
                    </select>
                </div>
                
                <div class="buttons">
                    <button>CRÉER UN COMPTE</button>
                </div>
            </div>
            
            <div class="table-title">Liste des utilisateurs existants</div>
            
            <table>
                <thead>
                    <tr>
                        <th>IDENTIFIANT</th>
                        <th>NOM & PRENOM</th>
                        <th>LOGIN</th>
                        <th>TYPE UTILISATEUR</th>
                        <th>STATUT</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
</body>
</html>