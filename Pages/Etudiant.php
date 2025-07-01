<?php
include('../config/connexion_BD.php');

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

$success_message = '';
$error_message = '';
$mode_edition = false;
$etudiant_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'ajouter':
            $num_etud = $_POST['num_etud'] ?? '';
            $nom_etud = $_POST['nom_etud'] ?? '';
            $prenom_etud = $_POST['prenom_etud'] ?? '';
            $date_naiss_etud = $_POST['date_naiss_etud'] ?? '';
            $login_etud = $_POST['login_etud'] ?? '';
            $id_tu = $_POST['id_tu'] ?? '';


            if (!empty($num_etud) && !empty($nom_etud) && !empty($prenom_etud) && !empty($date_naiss_etud) && !empty($login_etud) && !empty($id_tu)) {
                try {
                    $pdo->beginTransaction();
                    
                    // Vérifier si le login existe déjà
                    $checkLogin = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login_util = ?");
                    $checkLogin->execute([$login_etud]);
                    if ($checkLogin->fetchColumn() > 0) {
                        throw new Exception("Ce login existe déjà !");
                    }
                    
                    // Génération automatique du mot de passe : 2 lettres nom + 2 lettres prénom + Etu + @2025
                    $mot_de_passe_genere = substr(strtoupper($nom_etud), 0, 2) . substr(ucfirst(strtolower($prenom_etud)), 0, 2) . 'Etu@2025';
                    
                    // 1. Créer l'utilisateur dans la table utilisateur
                    $stmt1 = $pdo->prepare("INSERT INTO utilisateur (login_util, mdp_util) VALUES (?, ?)");
                    $stmt1->execute([$login_etud, password_hash($mot_de_passe_genere, PASSWORD_DEFAULT)]);
                    $id_util = $pdo->lastInsertId();
                    
                    // 2. Créer l'étudiant avec l'ID utilisateur
                    $stmt2 = $pdo->prepare("INSERT INTO etudiant (id_util, Num_Etud, Nom_Etud, Prenom_Etud, Date_naiss_Etud, Login_Etud, Mdp_Etud, id_tu) VALUES (?, ?, ?, ?, ?, ?, ? ,?)");
                    $stmt2->execute([$id_util, $num_etud, $nom_etud, $prenom_etud, $date_naiss_etud, $login_etud, $mot_de_passe_genere, $id_tu]);
                    
                    $pdo->commit();
                    $success_message = "Étudiant ajouté avec succès ! Mot de passe généré : " . $mot_de_passe_genere;
                } catch (Exception $e) {
                    $pdo->rollback();
                    $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'modifier':
            $num_etud_original = $_POST['num_etud_original'] ?? '';
            $nouveau_num_etud = $_POST['num_etud'] ?? '';
            $nouveau_nom_etud = $_POST['nom_etud'] ?? '';
            $nouveau_prenom_etud = $_POST['prenom_etud'] ?? '';
            $nouvelle_date_naiss_etud = $_POST['date_naiss_etud'] ?? '';
            $nouveau_login_etud = $_POST['login_etud'] ?? '';
            $regenerer_mot_de_passe = isset($_POST['regenerer_mot_de_passe']);

            if (!empty($num_etud_original) && !empty($nouveau_num_etud) && !empty($nouveau_nom_etud) && !empty($nouveau_prenom_etud) && !empty($nouvelle_date_naiss_etud) && !empty($nouveau_login_etud)) {
                try {
                    $pdo->beginTransaction();
                    
                    // Récupérer l'id_util de l'étudiant
                    $stmt_get_id = $pdo->prepare("SELECT id_util FROM etudiant WHERE Num_Etud = ?");
                    $stmt_get_id->execute([$num_etud_original]);
                    $id_util = $stmt_get_id->fetchColumn();
                    
                    if (!$id_util) {
                        throw new Exception("Étudiant non trouvé !");
                    }
                    
                    // Vérifier si le nouveau login existe déjà (sauf pour cet utilisateur)
                    $checkLogin = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login_util = ? AND id_util != ?");
                    $checkLogin->execute([$nouveau_login_etud, $id_util]);
                    if ($checkLogin->fetchColumn() > 0) {
                        throw new Exception("Ce login existe déjà !");
                    }
                    
                    if ($regenerer_mot_de_passe) {
                        // Régénération du mot de passe
                        $mot_de_passe_genere = substr(strtoupper($nouveau_nom_etud), 0, 2) . substr(strtoupper($nouveau_prenom_etud), 0, 2) . 'Etu@2025';
                        
                        // Mise à jour de l'utilisateur avec nouveau mot de passe
                        $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ?, mdp_util = ? WHERE id_util = ?");
                        $stmt_user->execute([$nouveau_login_etud, password_hash($mot_de_passe_genere, PASSWORD_DEFAULT), $id_util]);
                        
                        // Mise à jour de l'étudiant
                        $stmt_etud = $pdo->prepare("UPDATE etudiant SET Num_Etud = ?, Nom_Etud = ?, Prenom_Etud = ?, Date_naiss_Etud = ?, Login_Etud = ?, Mdp_Etud = ?, id_tu = ? WHERE Num_Etud = ?");
                        $stmt_etud->execute([$nouveau_num_etud, $nouveau_nom_etud, $nouveau_prenom_etud, $nouvelle_date_naiss_etud, $nouveau_login_etud, $mot_de_passe_genere, $id_tu, $num_etud_original]);
                        
                        $success_message = "Étudiant modifié avec succès ! Nouveau mot de passe : " . $mot_de_passe_genere;
                    } else {
                        // Mise à jour sans changer le mot de passe
                        $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ? WHERE id_util = ?");
                        $stmt_user->execute([$nouveau_login_etud, $id_util]);
                        
                        $stmt_etud = $pdo->prepare("UPDATE etudiant SET Num_Etud = ?, Nom_Etud = ?, Prenom_Etud = ?, Date_naiss_Etud = ?, Login_Etud = ? WHERE Num_Etud = ?");
                        $stmt_etud->execute([$nouveau_num_etud, $nouveau_nom_etud, $nouveau_prenom_etud, $nouvelle_date_naiss_etud, $nouveau_login_etud, $num_etud_original]);
                        
                        $success_message = "Étudiant modifié avec succès !";
                    }
                    
                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollback();
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'supprimer':
            $num_etud = $_POST['num_etud'] ?? '';
            
            if (!empty($num_etud)) {
                try {
                    $pdo->beginTransaction();
                    
                    // Récupérer l'id_util avant suppression
                    $stmt_get_id = $pdo->prepare("SELECT id_util FROM etudiant WHERE Num_Etud = ?");
                    $stmt_get_id->execute([$num_etud]);
                    $id_util = $stmt_get_id->fetchColumn();
                    
                    if ($id_util) {
                        // Supprimer l'étudiant (la suppression de l'utilisateur se fera automatiquement grâce à ON DELETE CASCADE)
                        $stmt = $pdo->prepare("DELETE FROM etudiant WHERE Num_Etud = ?");
                        $stmt->execute([$num_etud]);
                        
                        // Ou si vous voulez supprimer explicitement l'utilisateur :
                        $stmt_user = $pdo->prepare("DELETE FROM utilisateur WHERE id_util = ?");
                         $stmt_user->execute([$id_util]);
                    }
                    
                    $pdo->commit();
                    $success_message = "Étudiant supprimé avec succès !";
                } catch (PDOException $e) {
                    $pdo->rollback();
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
            }
            break;
    }
}

// Gestion du mode édition
if (isset($_GET['modifier'])) {
    $num_etud_a_modifier = $_GET['modifier'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE Num_Etud = ?");
        $stmt->execute([$num_etud_a_modifier]);
        $etudiant_a_modifier = $stmt->fetch();
        if ($etudiant_a_modifier) {
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
    <title>Mise à Jour des Étudiants</title>
    <link rel="stylesheet" href="../css/etudiants.css">
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
        <h1 class="page-title">Mise à jour des étudiants</h1>

        <?php if (!empty($success_message)) : ?>
            <div class="success-message" style="display: block;">
                ✅ <?= htmlspecialchars($success_message) ?>
            </div>
        <?php elseif (!empty($error_message)) : ?>
            <div class="error-message" style="display: block;">
                ❌ <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <section class="form-section <?= $mode_edition ? 'form-edit-mode' : '' ?>">
            <?php if ($mode_edition) : ?>
                <div class="edit-mode-title">Mode modification - Étudiant: <?= htmlspecialchars($etudiant_a_modifier['Nom_Etud'] . ' ' . $etudiant_a_modifier['Prenom_Etud']) ?></div>
            <?php endif; ?>
            
            <form id="studentForm" method="POST" action="Etudiant.php">
                <?php if ($mode_edition) : ?>
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="num_etud_original" value="<?= htmlspecialchars($etudiant_a_modifier['Num_Etud']) ?>">
                <?php else : ?>
                    <input type="hidden" name="action" value="ajouter">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>NUMÉRO ÉTUDIANT :</label>
                        <input type="text" 
                               name="num_etud" 
                               placeholder="Ex: 20230001" 
                               value="<?= $mode_edition ? htmlspecialchars($etudiant_a_modifier['Num_Etud']) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label>NOM :</label>
                        <input type="text" 
                               name="nom_etud" 
                               placeholder="Ex: KOUASSI" 
                               value="<?= $mode_edition ? htmlspecialchars($etudiant_a_modifier['Nom_Etud']) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label>PRENOMS :</label>
                        <input type="text" 
                               name="prenom_etud" 
                               placeholder="Ex: Jean Baptiste" 
                               value="<?= $mode_edition ? htmlspecialchars($etudiant_a_modifier['Prenom_Etud']) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label>DATE DE NAISSANCE :</label>
                        <input type="date" 
                               name="date_naiss_etud" 
                               value="<?= $mode_edition ? htmlspecialchars($etudiant_a_modifier['Date_naiss_Etud']) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label>LOGIN (EMAIL) :</label>
                        <input type="email" 
                               name="login_etud" 
                               placeholder="jean.kouassi@exemple.com" 
                               value="<?= $mode_edition ? htmlspecialchars($etudiant_a_modifier['Login_Etud']) : '' ?>"
                               required>
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
                    <?php if ($mode_edition) : ?>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="regenerer_mot_de_passe" style="margin-right: 5px;">
                            Régénérer le mot de passe
                        </label>
                        <small style="display: block; color: #666; margin-top: 5px;">
                            Format : 2 lettres nom + 2 lettres prénom + Etu@2025
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="submit">
                    <?= $mode_edition ? 'Modifier' : 'Ajouter' ?>
                </button>
                <?php if ($mode_edition) : ?>
                    <a href="Etudiant.php" class="btn-cancel" style="margin-left: 10px; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                        Annuler
                    </a>
                <?php endif; ?>
            </form>
        </section>
        
        <section class="table-section">
            <div class="table-header">
                <span>NUMÉRO ÉTUDIANT</span>
                <span>NOM</span>
                <span>PRENOMS</span>
                <span>TYPE UTILISATEUR</span>
                <span>ACTION</span>
            </div>
            <div class="table-body">
                <?php
                try {
                    $stmt = $pdo->query("SELECT e.*, tu.lib_tu FROM etudiant e JOIN type_utilisateur tu ON e.id_tu = tu.id_tu ORDER BY Nom_Etud, Prenom_Etud");
                    $rows = $stmt->fetchAll();
                    if (count($rows) === 0) {
                        echo "<div class='empty-state'>Aucun étudiant enregistré.</div>";
                    } else {
                        foreach ($rows as $row) {
                            $num_etud_encoded = urlencode($row['Num_Etud']);
                            $row_class = ($mode_edition && $row['Num_Etud'] === $etudiant_a_modifier['Num_Etud']) ? 'style="background-color: #e7f3ff;"' : '';
                            
                            echo "<div class='table-row' {$row_class}>
                                    <span>" . htmlspecialchars($row['Num_Etud']) . "</span>
                                    <span>" . htmlspecialchars($row['Nom_Etud']) . "</span>
                                    <span>" . htmlspecialchars($row['Prenom_Etud']) . "</span>
                                    <span>" . htmlspecialchars($row['lib_tu']) . "</span>
                                    <span class='action-buttons'>
                                        <a href='Etudiant.php?modifier={$num_etud_encoded}' class='btn-edit'>Modifier</a>
                                        <button type='button' class='btn-delete' onclick='confirmerSuppression(\"{$num_etud_encoded}\", \"" . htmlspecialchars($row['Nom_Etud'] . ' ' . $row['Prenom_Etud']) . "\")'>Supprimer</button>
                                    </span>
                                  </div>";
                        }
                    }
                } catch (PDOException $e) {
                    echo "<div class='error-state'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                ?>
            </div>
        </section>

    <?php if (!$is_ajax): ?>

    <script src="../js/niveau_approbation.js"></script>

    <?php endif; ?>

</body>
</html>