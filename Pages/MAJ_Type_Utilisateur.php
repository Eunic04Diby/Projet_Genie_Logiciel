<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$type_utilisateur_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_gu = $_POST['id_gu'] ?? null;

    switch ($action) {
        case 'ajouter':
            $id_tu = $_POST['identifiant'] ?? '';
            $lib_tu = $_POST['libelle'] ?? '';

            if (!empty($id_tu) && !empty($lib_tu) && !empty($id_gu)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO type_utilisateur (id_tu, lib_tu, id_gu) VALUES (?, ?, ?)");
                    $stmt->execute([$id_tu, $lib_tu, $id_gu]);
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

            if (!empty($id_original) && !empty($nouveau_id) && !empty($nouveau_libelle) && !empty($id_gu)) {
                try {
                    $stmt = $pdo->prepare("UPDATE type_utilisateur SET id_tu = ?, lib_tu = ?, id_gu = ? WHERE id_tu = ?");
                    $stmt->execute([$nouveau_id, $nouveau_libelle, $id_gu, $id_original]);
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
                    $success_message = "Type utilisateur supprimé avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
            }
            break;
    }
}

// Mode édition
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

// Récupération des groupes utilisateurs pour le menu déroulant
$groupes = [];
try {
    $stmt = $pdo->query("SELECT * FROM groupe_utilisateur");
    $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors du chargement des groupes : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour des types utilisateurs</title>
        <link rel="stylesheet" href="../css/niveau_approbation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
            .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        .modal-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-confirm {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
        
        .form-edit-mode {
            background-color: #e7f3ff;
            border: 2px solid #007bff;
        }
        
        .edit-mode-title {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        </style>
</head>
<body>
        <h1 class="page-title">Mise à jour des types utilisateurs</h1>

        <?php if (!empty($success_message)) : ?>
        <div class="success-message" style="display: block;">
            ✅ <?= htmlspecialchars($success_message) ?>
        </div>
    <?php elseif (!empty($error_message)) : ?>
        <div class="success-message" style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; display: block;">
            ❌ <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="form-container <?= $mode_edition ? 'form-edit-mode' : '' ?>">
        <?php if ($mode_edition) : ?>
            <div class="edit-mode-title">Mode modification - Type utilisateur: <?= htmlspecialchars($type_utilisateur_a_modifier['lib_tu']) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form-row" action="MAJ_Type_Utilisateur.php">
            <?php if ($mode_edition) : ?>
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id_original" value="<?= htmlspecialchars($type_utilisateur_a_modifier['id_tu']) ?>">
            <?php else : ?>
                <input type="hidden" name="action" value="ajouter">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="identifiant">Identifiant</label>
                <input type="text" 
                       id="identifiant" 
                       name="identifiant" 
                       placeholder="Saisir l'identifiant" 
                       value="<?= $mode_edition ? htmlspecialchars($type_utilisateur_a_modifier['id_tu']) : '' ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input type="text" 
                       id="libelle" 
                       name="libelle" 
                       placeholder="Saisir le libellé" 
                       value="<?= $mode_edition ? htmlspecialchars($type_utilisateur_a_modifier['lib_tu']) : '' ?>"
                       required>
            </div>

        <div class="form-group">
            <label for="id_gu">Groupe utilisateur</label>
            <select id="id_gu" name="id_gu" required>
                <option value="">-- Choisir un groupe --</option>
                <?php foreach ($groupes as $groupe): ?>
                    <option value="<?= $groupe['id_gu'] ?>"
                        <?= ($mode_edition && $type_utilisateur_a_modifier['id_gu'] == $groupe['id_gu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($groupe['lib_gu']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

            <button class="btn-add" type="submit">
                <?= $mode_edition ? 'Modifier' : 'Ajouter' ?>
            </button>
            <?php if ($mode_edition) : ?>
                <a href="MAJ_Type_Utilisateur.php" class="btn-cancel" style="margin-left: 10px; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                    Annuler
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header">Liste des status</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Libellé</th>
                    <th>Groupe utilisateur</th>
                    <th class="action-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT tu.*, gu.lib_gu 
                     FROM type_utilisateur tu 
                     JOIN groupe_utilisateur gu ON tu.id_gu = gu.id_gu");

                    $rows = $stmt->fetchAll();
                    if (count($rows) === 0) {
                        echo "<tr><td colspan='4' class='empty-state'>Aucun type utilisateur enregistré.</td></tr>";
                    } else {
                        $numero = 1;
                        foreach ($rows as $row) {
                            $id_encoded = urlencode($row['id_tu']);
                            $row_class = ($mode_edition && $row['id_tu'] === $type_utilisateur_a_modifier['id_tu']) ? 'style="background-color: #e7f3ff;"' : '';
                            
                            echo "<tr {$row_class}>
                                    
                                    <td>" . htmlspecialchars($row['id_tu']) . "</td>
                                    <td>" . htmlspecialchars($row['lib_tu']) . "</td>
                                    <td>" . htmlspecialchars($row['lib_gu']) . "</td>
                                    <td class='action-col'>
                                        <div class='action-buttons'>
                                            <a href='MAJ_Type_Utilisateur.php?modifier={$id_encoded}' class='btn-edit'>Modifier</a>
                                            <button type='button' class='btn-delete' onclick='confirmerSuppression(\"{$id_encoded}\", \"" . htmlspecialchars($row['lib_tu']) . "\")'>Supprimer</button>
                                        </div>
                                    </td>
                                  </tr>";
                            $numero++;
                        }
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='4'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div id="modalSuppression" class="modal">
    <div class="modal-content">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer le traitement "<span id="nomTypeUtilisateur"></span>" ?</p>
        <div class="modal-buttons">
            <button type="button" class="btn-confirm" onclick="supprimerTypeUtilisateur()">Oui, supprimer</button>
            <button type="button" class="btn-cancel" onclick="fermerModal()">Annuler</button>
        </div>
    </div>
</div>

<!-- Formulaire caché pour la suppression -->
<form id="formSuppression" method="POST" style="display: none;">
    <input type="hidden" name="action" value="supprimer">
    <input type="hidden" name="id_tu" id="idTypeUtilisateurSupprimer">
</form>

<script>
let idTypeUtilisateurASupprimer = '';

function confirmerSuppression(id, nom) {
    idTypeUtilisateurASupprimer = id;
    document.getElementById('nomTypeUtilisateur').textContent = nom;
    document.getElementById('modalSuppression').style.display = 'block';
}

function supprimerTypeUtilisateur() {
    document.getElementById('idTypeUtilisateurSupprimer').value = idTypeUtilisateurASupprimer;
    document.getElementById('formSuppression').submit();
}

function fermerModal() {
    document.getElementById('modalSuppression').style.display = 'none';
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('modalSuppression');
    if (event.target === modal) {
        fermerModal();
    }
}
</script>

<script src="../js/niveau_approbation.js"></script>
</body>
</html>