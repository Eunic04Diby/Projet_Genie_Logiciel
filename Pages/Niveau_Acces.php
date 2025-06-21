<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$entreprise_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'ajouter':
            $id_tu = $_POST['identifiant'] ?? '';
            $lib_tu = $_POST['libelle'] ?? '';

            if (!empty($id_tu) && !empty($lib_tu)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO type_utilisateur (id_tu, lib_tu) VALUES (?, ?)");
                    $stmt->execute([$id_tu, $lib_tu]);
                    $success_message = "Type utilisateur ajouté avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'modifier':
            $id_original = $_POST['id_original'] ?? '';
            $nouveau_id = $_POST['identifiant'] ?? '';
            $nouveau_libelle = $_POST['libelle'] ?? '';

            if (!empty($id_original) && !empty($nouveau_id) && !empty($nouveau_libelle)) {
                try {
                    $stmt = $pdo->prepare("UPDATE type_utilisateur SET id_tu = ?, lib_tu = ? WHERE id_tu = ?");
                    $stmt->execute([$nouveau_id, $nouveau_libelle, $id_original]);
                    $success_message = "Type utilisateur modifié avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'supprimer':
            $id_tu = $_POST['id_tu'] ?? '';
            
            if (!empty($id_tu)) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM type_utilisateur WHERE id_tu = ?");
                    $stmt->execute([$id_tu]);
                    $success_message = "Traitement supprimé avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
            }
            break;
    }
}

// Gestion du mode édition
if (isset($_GET['modifier'])) {
    $id_a_modifier = $_GET['modifier'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM type_utilisateur WHERE id_tu = ?");
        $stmt->execute([$id_a_modifier]);
        $type_utilisateur_a_modifier = $stmt->fetch();
        if ($type_utilisateur_a_modifier) {
            $mode_edition = true;
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour du niveau d'accès</title>
        <link rel="stylesheet" href="../css/niveau_approbation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <header>
        <div class="logo">LOGO</div>
        <div class="search-bar">
            <input type="text" placeholder="Rechercher...">
        </div>
        <div class="user-profile">NOM UTILISATEUR</div>
    </header>

    <aside class="sidebar">
        <ul>
            <li class="active">
                <span>📋</span>
                <span>Fonctions</span>
            </li>
            <li>
                <span>👥</span>
                <span>Utilisateurs</span>
            </li>
            <li>
                <span>⚙️</span>
                <span>Paramètres</span>
            </li>
            <li>
                <span>📊</span>
                <span>Rapports</span>
            </li>
        </ul>
    </aside>

    <main>
        <h1 class="page-title">Mise à jour du niveau d'accès</h1>

        <div class="success-message" id="successMessage">
            Enregistrement ajouté avec succès !
        </div>

        <div class="form-container">
            <div class="form-row">
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" placeholder="Saisir l'identifiant">
                </div>
                <div class="form-group">
                    <label for="libelle">Libellé</label>
                    <input type="text" id="libelle" placeholder="Saisir le libellé">
                </div>
                <button class="btn-add" onclick="ajouterEnregistrement()">Ajouter</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                Liste des enregistrements
            </div>
            
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th class="numero-col">N°</th>
                        <th>Identifiant</th>
                        <th>Libellé</th>
                        <th class="action-col">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                  
                </tbody>
            </table>
        </div>
    </main>

<script src="../js/niveau_approbation.js"></script>
</body>
</html>