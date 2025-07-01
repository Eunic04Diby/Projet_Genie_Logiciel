<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$enseignant_a_modifier = null;

$grades = $pdo->query("SELECT * FROM grade ORDER BY nom_grade")->fetchAll(PDO::FETCH_ASSOC);
$fonctions = $pdo->query("SELECT * FROM fonction ORDER BY nom_fonct")->fetchAll(PDO::FETCH_ASSOC);
$specialites = $pdo->query("SELECT * FROM specialite ORDER BY lib_spe")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'ajouter':
    $nom_ens = $_POST['nom_ens'] ?? '';
    $prenoms_ens = $_POST['prenoms_ens'] ?? '';
    $date_naiss_ens = $_POST['date_naiss_ens'] ?? '';
    $login_ens = $_POST['login_ens'] ?? '';
    $mdp_ens = $_POST['mdp_ens'] ?? '';
    $id_spe = $_POST['id_spe'] ?? '';
    $id_grade = $_POST['id_grade'] ?? '';
    $date_grade = $_POST['date_grade'] ?? '';
    $id_fonction = $_POST['id_fonct'] ?? '';
    $date_fonction = $_POST['date_fonct'] ?? '';
    $id_tu = $_POST['id_tu'] ?? '';

    if (!empty($nom_ens) && !empty($prenoms_ens) && !empty($date_naiss_ens) && !empty($login_ens) && $id_spe && $id_grade && !empty($date_grade) && $id_fonction && !empty($date_fonction)) {
        try {
            $pdo->beginTransaction();

            // Étape 1 : Insertion dans utilisateur
            $mot_de_passe_hash = password_hash($mdp_ens, PASSWORD_DEFAULT);
            $stmt_user = $pdo->prepare("INSERT INTO utilisateur (login_util, mdp_util, id_tu) VALUES (?, ?, ?)");
            $stmt_user->execute([$login_ens, $mot_de_passe_hash, $id_tu]);

            $id_util = $pdo->lastInsertId(); // Récupérer l’ID utilisateur

            // Étape 2 : Insertion dans enseignant
            $stmt_ens = $pdo->prepare("INSERT INTO enseignant (id_util, nom_ens, prenoms_ens, date_naiss_ens, login_ens, mdp_ens, id_spe, id_grade, date_grade, id_fonct, date_fonct, id_tu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_ens->execute([$id_util, $nom_ens, $prenoms_ens, $date_naiss_ens, $login_ens, $mdp_ens, $id_spe, $id_grade, $date_grade, $id_fonction, $date_fonction, $id_tu]);

            $pdo->commit();
            $success_message = "Enseignant ajouté avec succès !";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $error_message = "Tous les champs sont obligatoires.";
    }
    break;


        case 'modifier':
            $id_ens_original = $_POST['id_ens_original'] ?? '';
            $nouveau_nom_ens = $_POST['nom_ens'] ?? '';
            $nouveau_prenoms_ens = $_POST['prenoms_ens'] ?? '';
            $nouvelle_date_naiss_ens = $_POST['date_naiss_ens'] ?? '';
            $nouveau_login_ens = $_POST['login_ens'] ?? '';
            $nouveau_mdp_ens = $_POST['mdp_ens'] ?? '';
            $nouveau_id_spe = $_POST['id_spe'] ?? '';
            $nouveau_id_grade = $_POST['id_grade'] ?? '';
            $nouvelle_date_grade = $_POST['date_grade'] ?? '';
            $nouveau_id_fonction = $_POST['id_fonct'] ?? '';
            $nouvelle_date_fonction = $_POST['date_fonct'] ?? '';
            $regenerer_mot_de_passe = isset($_POST['regenerer_mot_de_passe']);

            if (!empty($id_ens_original) && !empty($nouveau_nom_ens) && !empty($nouveau_prenoms_ens) && !empty($nouvelle_date_naiss_ens) && !empty($nouveau_login_ens) && $nouveau_id_spe && $nouveau_id_grade && !empty($nouvelle_date_grade) && $nouveau_id_fonction && !empty($nouvelle_date_fonction)) {
                try {
                    $pdo->beginTransaction();

                    $stmt_get_id = $pdo->prepare("SELECT id_util FROM enseignant WHERE id_ens = ?");
                    $stmt_get_id->execute([$id_ens_original]);
                    $id_util = $stmt_get_id->fetchColumn();
                    if (!$id_util) throw new Exception("Enseignant non trouvé !");

                    $checkLogin = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login_util = ? AND id_util != ?");
                    $checkLogin->execute([$nouveau_login_ens, $id_util]);
                    if ($checkLogin->fetchColumn() > 0) throw new Exception("Ce login existe déjà !");

                    if ($regenerer_mot_de_passe) {
                        $mot_de_passe_genere = substr(strtoupper($nouveau_nom_ens), 0, 2) . substr(strtoupper($nouveau_prenoms_ens), 0, 2) . 'Ens@2025';
                        $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ?, mdp_util = ? WHERE id_util = ?");
                        $stmt_user->execute([$nouveau_login_ens, password_hash($mot_de_passe_genere, PASSWORD_DEFAULT), $id_util]);
                        $nouveau_mdp_ens = $mot_de_passe_genere;
                    } else {
                        $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ? WHERE id_util = ?");
                        $stmt_user->execute([$nouveau_login_ens, $id_util]);
                    }

                    $stmt_pers = $pdo->prepare("UPDATE enseignant SET nom_ens = ?, prenoms_ens = ?, date_naiss_ens = ?, login_ens = ?, mdp_ens = ?, id_spe = ?, id_grade = ?, date_grade = ?, id_fonct = ?, date_fonct = ? WHERE id_ens = ?");
                    $stmt_pers->execute([$nouveau_nom_ens, $nouveau_prenoms_ens, $nouvelle_date_naiss_ens, $nouveau_login_ens, $nouveau_mdp_ens, $nouveau_id_spe, $nouveau_id_grade, $nouvelle_date_grade, $nouveau_id_fonction, $nouvelle_date_fonction, $id_ens_original]);

                    $pdo->commit();
                    $success_message = "Enseignant modifié avec succès !" . ($regenerer_mot_de_passe ? " Nouveau mot de passe : $mot_de_passe_genere" : "");

                } catch (Exception $e) {
                    $pdo->rollback();
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

       case 'supprimer':
    $id_ens = $_POST['id_ens'] ?? '';
    if (!empty($id_ens)) {
        try {
            $pdo->beginTransaction();

            // Récupérer l'ID utilisateur lié à l'enseignant
            $stmt_get_id = $pdo->prepare("SELECT id_util FROM enseignant WHERE id_ens = ?");
            $stmt_get_id->execute([$id_ens]);
            $id_util = $stmt_get_id->fetchColumn();

            if ($id_util) {
                // Supprimer l’enseignant
                $stmt_delete_ens = $pdo->prepare("DELETE FROM enseignant WHERE id_ens = ?");
                $stmt_delete_ens->execute([$id_ens]);

                // Supprimer aussi l'utilisateur associé
                $stmt_delete_util = $pdo->prepare("DELETE FROM utilisateur WHERE id_util = ?");
                $stmt_delete_util->execute([$id_util]);
            }

            $pdo->commit();
            $success_message = "Enseignant et compte utilisateur supprimés avec succès !";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
    break;


}
}

if (isset($_GET['modifier'])) {
    $id_ens_a_modifier = $_GET['modifier'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM enseignant WHERE id_ens = ?");
        $stmt->execute([$id_ens_a_modifier]);
        $enseignant_a_modifier = $stmt->fetch();
        if ($enseignant_a_modifier) {
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
    <title>Mise à jour des Enseignants</title>
        <link rel="stylesheet" href="../css/maj_enseignant.css">
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
        
        .success-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: none;
        }
        
        .error-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: none;
        }
    </style>
</head>
<body>

        <h1 class="page-title">Mise à jour des Enseignants</h1>

        <?php if (!empty($success_message)) : ?>
        <div class="success-message" style="display: block;">
            ✅ <?= htmlspecialchars($success_message) ?>
        </div>
    <?php elseif (!empty($error_message)) : ?>
        <div class="success-message" style="background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; border-color: #f5c6cb; display:block;">
            ❌ <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <section class="form-container">
        <div class="section-title"><?= $mode_edition ? 'Modifier un enseignant' : 'Ajouter un enseignant' ?></div>
        <form method="POST" action="MAJ_Enseignant.php">
            
            <?= $mode_edition ? '<input type="hidden" name="action" value="modifier"><input type="hidden" name="id_ens_original" value="' . htmlspecialchars($enseignant_a_modifier['id_ens']) . '">' : '<input type="hidden" name="action" value="ajouter">' ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom_ens" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['nom_ens']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Prénoms</label>
                    <input type="text" name="prenoms_ens" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['prenoms_ens']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naiss_ens" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['date_naiss_ens']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Login</label>
                    <input type="email" name="login_ens" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['login_ens']) : '' ?>">
                </div>
            </div>

        
            <div class="section-title" style="margin-top: 3rem;">Carrière</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Spécialité</label>
                    <select name="id_spe" required>
                        <option value="">-- Sélectionner une spécialité --</option>
                        <?php foreach ($specialites as $spe) : ?>
                            <option value="<?= $spe['id_spe'] ?>" <?= $mode_edition && $enseignant_a_modifier['id_spe'] == $spe['id_spe'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spe['lib_spe']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Grade</label>
                    <select name="id_grade" required>
                        <option value="">-- Sélectionner un grade --</option>
                        <?php foreach ($grades as $grade) : ?>
                            <option value="<?= $grade['id_grade'] ?>" <?= $mode_edition && $enseignant_a_modifier['id_grade'] == $grade['id_grade'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grade['nom_grade']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date :</label>
                    <input type="date" name="date_grade" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['date_grade']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Fonction</label>
                    <select name="id_fonct" required>
                        <option value="">-- Sélectionner une fonction --</option>
                        <?php foreach ($fonctions as $fonct) : ?>
                            <option value="<?= $fonct['id_fonct'] ?>" <?= $mode_edition && $enseignant_a_modifier['id_fonct'] == $fonct['id_fonct'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fonct['nom_fonct']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date :</label>
                    <input type="date" name="date_fonct" required value="<?= $mode_edition ? htmlspecialchars($enseignant_a_modifier['date_fonct']) : '' ?>">
                </div>

                <?php if ($mode_edition): ?>
                <div class="form-group">
                    <label style="margin-bottom: 10px;">Régénérer le mot de passe</label>
                    <label class="radio-option">
                        <input type="checkbox" name="regenerer_mot_de_passe">
                        <span>Oui (2 lettres nom + 2 lettres prénom + Ens@2025)</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                        <label for="id_tu">Type d'utilisateur :</label>
                        <select name="id_tu" id="id_tu" required>
                            <option value="">-- Choisir un type --</option>
                            <?php
                            try {
                                $types = $pdo->query("SELECT id_tu, lib_tu FROM type_utilisateur")->fetchAll();
                                foreach ($types as $type) {
                                    $selected = ($mode_edition && $etudiant_a_modifier['id_tu'] == $type['id_tu']) ? 'selected' : '';
                                    echo "<option value='{$type['id_tu']}' $selected>" . htmlspecialchars($type['lib_tu']) . "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option disabled>Erreur de chargement</option>";
                            }
                            ?>
                        </select>
            </div>

            <div class="button-container">
                <button type="submit" class="submit-btn"><?= $mode_edition ? 'Modifier' : 'Ajouter' ?></button>
                <?php if ($mode_edition): ?>
                    <a href="MAJ_Enseignant.php" class="submit-btn" style="background: var(--danger-red); margin-left: 1rem;">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="table-container">
        <div class="table-header">Liste du Personnel</div>
        <table class="personnel-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénoms</th>
                    <th>Grade</th>
                    <th>Fonction</th>
                    <th>Type utilisateur</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("
                        SELECT e.*, g.nom_grade, f.nom_fonct AS nom_fonction, tu.lib_tu
                        FROM enseignant e
                        LEFT JOIN grade g ON e.id_grade = g.id_grade
                        LEFT JOIN fonction f ON e.id_fonct = f.id_fonct
                        LEFT JOIN utilisateur u ON e.id_util = u.id_util
LEFT JOIN type_utilisateur tu ON u.id_tu = tu.id_tu
                        ORDER BY e.nom_ens, e.prenoms_ens
                    ");

                    $rows = $stmt->fetchAll();
                    if (!$rows) {
                        echo '<tr><td colspan="6" style="text-align:center;">Aucun personnel enregistré.</td></tr>';
                    } else {
                        foreach ($rows as $row) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['id_ens']) . '</td>
                                    <td>' . htmlspecialchars($row['nom_ens']) . '</td>
                                    <td>' . htmlspecialchars($row['prenoms_ens']) . '</td>
                                    <td>' . htmlspecialchars($row['nom_grade']) . '</td>
                                    <td>' . htmlspecialchars($row['nom_fonction']) . '</td>
                                    <td>' . htmlspecialchars($row['lib_tu']) . '</td>
                                    <td>
                                        <a href="MAJ_Enseignant.php?modifier=' . urlencode($row['id_ens']) . '" class="action-btn">Modifier</a>
                                        <button type="button" class="action-btn delete" onclick="confirmerSuppression(\'' . $row['id_ens'] . '\', \'' . htmlspecialchars($row['nom_ens'] . ' ' . $row['prenoms_ens']) . '\')">Supprimer</button>
                                    </td>
                                </tr>';
                        }
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6" style="text-align:center;">Erreur : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </section>

    <!-- Modal de confirmation -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Confirmer la suppression</h3>
           <p>Êtes-vous sûr de vouloir supprimer <span id="enseignantNom"></span> ?</p>
<div class="modal-buttons">
    <button class="modal-btn confirm" onclick="supprimerEnseignant()">Oui, supprimer</button> <!-- ici, appel à la bonne fonction -->
    <button class="modal-btn" onclick="fermerModal()">Annuler</button>
</div>
        </div>
    </div>

     <!-- Formulaire caché pour la suppression -->
    <form id="formSuppression" method="POST" style="display: none;">
        <input type="hidden" name="action" value="supprimer">
        <input type="hidden" name="id_ens" id="idEnsSupprimer">
    </form>

    <script>
       let idEnsASupprimer = '';

function confirmerSuppression(idEns, nom) {
    idEnsASupprimer = idEns;  // corrigé ici
    document.getElementById('enseignantNom').textContent = nom;  // corrigé id span
    document.getElementById('confirmModal').style.display = 'block';  // corrigé id modal
}

function supprimerEnseignant() {
    document.getElementById('idEnsSupprimer').value = idEnsASupprimer;
    document.getElementById('formSuppression').submit();
}

function fermerModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) {
        fermerModal();
    }
}

    </script>

</body>
</html>