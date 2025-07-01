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
            $id_niv_acc = $_POST['identifiant'] ?? '';
            $lib_niv_acc = $_POST['libelle'] ?? '';

            if (!empty($id_niv_acc) && !empty($lib_niv_acc)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO niveau_acces_donnees (id_niv_acc, lib_niv_acc) VALUES (?, ?)");
                    $stmt->execute([$id_niv_acc, $lib_niv_acc]);
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
                    $stmt = $pdo->prepare("UPDATE niveau_acces_donnees SET id_niv_acc = ?, lib_niv_acc = ? WHERE id_niv_acc = ?");
                    $stmt->execute([$nouveau_id, $nouveau_libelle, $id_original]);
                    $success_message = "Niveau modifié avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'supprimer':
            $id_niv_acc = $_POST['id_niv_acc'] ?? '';
            
            if (!empty($id_niv_acc)) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM niveau_acces_donnees WHERE id_niv_acc = ?");
                    $stmt->execute([$id_niv_acc]);
                    $success_message = "Niveau supprimé avec succès !";
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
        $stmt = $pdo->prepare("SELECT * FROM niveau_acces_donnees WHERE id_niv_acc = ?");
        $stmt->execute([$id_a_modifier]);
        $niveau_acces_donnees_a_modifier = $stmt->fetch();
        if ($niveau_acces_donnees_a_modifier) {
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
   
        <h1 class="page-title">Mise à jour du niveau d'accès</h1>

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
            <div class="edit-mode-title">Mode modification - Niveau d'acces au données: <?= htmlspecialchars($niveau_acces_donnees_a_modifier['lib_niv_acc']) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form-row" action="Niveau_Acces.php">
            <?php if ($mode_edition) : ?>
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id_original" value="<?= htmlspecialchars($niveau_acces_donnees_a_modifier['id_niv_acc']) ?>">
            <?php else : ?>
                <input type="hidden" name="action" value="ajouter">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="identifiant">Identifiant</label>
                <input type="text" 
                       id="identifiant" 
                       name="identifiant" 
                       placeholder="Saisir l'identifiant" 
                       value="<?= $mode_edition ? htmlspecialchars($niveau_acces_donnees_a_modifier['id_niv_acc']) : '' ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input type="text" 
                       id="libelle" 
                       name="libelle" 
                       placeholder="Saisir le libellé" 
                       value="<?= $mode_edition ? htmlspecialchars($niveau_acces_donnees_a_modifier['lib_niv_acc']) : '' ?>"
                       required>
            </div>
            <button class="btn-add" type="submit">
                <?= $mode_edition ? 'Modifier' : 'Ajouter' ?>
            </button>
            <?php if ($mode_edition) : ?>
                <a href="Niveau_Acces.php" class="btn-cancel" style="margin-left: 10px; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                    Annuler
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header">Liste des niveaux d'accès aux données</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="numero-col">N°</th>
                    <th>Identifiant</th>
                    <th>Libellé</th>
                    <th class="action-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM niveau_acces_donnees");
                    $rows = $stmt->fetchAll();
                    if (count($rows) === 0) {
                        echo "<tr><td colspan='4' class='empty-state'>Aucun type utilisateur enregistré.</td></tr>";
                    } else {
                        $numero = 1;
                        foreach ($rows as $row) {
                            $id_encoded = urlencode($row['id_niv_acc']);
                            $row_class = ($mode_edition && $row['id_niv_acc'] === $niveau_acces_donnees_a_modifier['id_niv_acc']) ? 'style="background-color: #e7f3ff;"' : '';
                            
                            echo "<tr {$row_class}>
                                    <td class='numero-col'>{$numero}</td>
                                    <td>" . htmlspecialchars($row['id_niv_acc']) . "</td>
                                    <td>" . htmlspecialchars($row['lib_niv_acc']) . "</td>
                                    <td class='action-col'>
                                        <div class='action-buttons'>
                                            <a href='Niveau_Acces.php?modifier={$id_encoded}' class='btn-edit'>Modifier</a>
                                            <button type='button' class='btn-delete' onclick='confirmerSuppression(\"{$id_encoded}\", \"" . htmlspecialchars($row['lib_niv_acc']) . "\")'>Supprimer</button>
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
        <p>Êtes-vous sûr de vouloir supprimer le traitement "<span id="nomNivAcc"></span>" ?</p>
        <div class="modal-buttons">
            <button type="button" class="btn-confirm" onclick="supprimerNivAcc()">Oui, supprimer</button>
            <button type="button" class="btn-cancel" onclick="fermerModal()">Annuler</button>
        </div>
    </div>
</div>

<!-- Formulaire caché pour la suppression -->
<form id="formSuppression" method="POST" style="display: none;">
    <input type="hidden" name="action" value="supprimer">
    <input type="hidden" name="id_niv_acc" id="idNivAccSupprimer">
</form>

<script>
let idNivAccASupprimer = '';

function confirmerSuppression(id, nom) {
    idNivAccASupprimer = id;
    document.getElementById('nomNivAcc').textContent = nom;
    document.getElementById('modalSuppression').style.display = 'block';
}

function supprimerNivAcc() {
    document.getElementById('idNivAccSupprimer').value = idNivAccASupprimer;
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