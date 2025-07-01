<?php 
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$personnel_admin_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'ajouter':
            $nom_pers = trim($_POST['nom_pers'] ?? '');
            $prenom_pers = trim($_POST['prenoms_pers'] ?? '');
            $email_pers = trim($_POST['email_pers'] ?? '');
            $date_naiss_pers = $_POST['date_naiss_pers'] ?? '';
            $poste_pers = trim($_POST['poste_pers'] ?? '');
            $date_embauche_pers = $_POST['date_embauche_pers'] ?? '';
            $login_pers = trim($_POST['login_pers'] ?? '');
            $id_tu = $_POST['id_tu'] ?? '';

            // Validation
            if (empty($nom_pers) || empty($prenom_pers) || empty($email_pers) || 
                empty($date_naiss_pers) || empty($poste_pers) || empty($date_embauche_pers) || 
                empty($login_pers) || empty($id_tu)) {
                $error_message = "Tous les champs sont obligatoires.";
                break;
            }

            // Validation email
            if (!filter_var($email_pers, FILTER_VALIDATE_EMAIL)) {
                $error_message = "L'adresse email n'est pas valide.";
                break;
            }

            // Validation des dates
            if (!DateTime::createFromFormat('Y-m-d', $date_naiss_pers)) {
                $error_message = "La date de naissance n'est pas valide.";
                break;
            }

            if (!DateTime::createFromFormat('Y-m-d', $date_embauche_pers)) {
                $error_message = "La date d'embauche n'est pas valide.";
                break;
            }

            try {
                $pdo->beginTransaction();

                // ÉTAPE 1 : Vérifier que le type d'utilisateur existe
                $checkTypeUser = $pdo->prepare("SELECT COUNT(*) FROM type_utilisateur WHERE id_tu = ?");
                $checkTypeUser->execute([$id_tu]);
                if ($checkTypeUser->fetchColumn() == 0) {
                    throw new Exception("Le type d'utilisateur sélectionné n'existe pas !");
                }

                // Vérifier si le login existe déjà
                $checkLogin = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login_util = ?");
                $checkLogin->execute([$login_pers]);
                if ($checkLogin->fetchColumn() > 0) {
                    throw new Exception("Ce login existe déjà !");
                }

                // Vérifier si l'email existe déjà
                $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM personnel_admin WHERE email_pers = ?");
                $checkEmail->execute([$email_pers]);
                if ($checkEmail->fetchColumn() > 0) {
                    throw new Exception("Cette adresse email existe déjà !");
                }

                // Génération automatique du mot de passe : 2 lettres nom + 2 lettres prénom + PerAd@2025
                $mot_de_passe_genere = substr(strtoupper($nom_pers), 0, 2) . 
                                     substr(ucfirst(strtolower($prenom_pers)), 0, 2) . 
                                     'PerAd@2025';

                // 1. Créer l'utilisateur dans la table utilisateur
                $stmt1 = $pdo->prepare("INSERT INTO utilisateur (login_util, mdp_util, id_tu) VALUES (?, ?, ?)");
                $stmt1->execute([$login_pers, password_hash($mot_de_passe_genere, PASSWORD_DEFAULT), $id_tu]);
                $id_util = $pdo->lastInsertId();

                // 2. Créer le personnel admin avec l'ID utilisateur ET id_tu
                $stmt2 = $pdo->prepare("INSERT INTO personnel_admin (id_util, nom_pers, prenoms_pers, email_pers, date_naiss_pers, poste_pers, date_embauche_pers, login_pers, mdp_pers, id_tu) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt2->execute([$id_util, $nom_pers, $prenom_pers, $email_pers, $date_naiss_pers, 
                               $poste_pers, $date_embauche_pers, $login_pers, $mot_de_passe_genere, $id_tu]);

                $pdo->commit();
                $success_message = "Personnel ajouté avec succès ! Mot de passe généré : " . $mot_de_passe_genere;
                
                // Rediriger pour éviter la resoumission et nettoyer le formulaire
                header("Location: Personnel_admin.php?success=" . urlencode($success_message));
                exit;
                
            } catch (Exception $e) {
                $pdo->rollback();
                $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                
                // Affichage de debug pour comprendre le problème
                error_log("Erreur ajout personnel - ID_TU: " . $id_tu . " - Message: " . $e->getMessage());
            }
            break;

        case 'modifier':
            $id_pers_original = $_POST['id_pers_original'] ?? '';
            $nouveau_nom_pers = trim($_POST['nom_pers'] ?? '');
            $nouveau_prenom_pers = trim($_POST['prenoms_pers'] ?? '');
            $nouveau_email_pers = trim($_POST['email_pers'] ?? '');
            $nouveau_date_naiss_pers = $_POST['date_naiss_pers'] ?? '';
            $nouveau_poste_pers = trim($_POST['poste_pers'] ?? '');
            $nouvelle_date_embauche_pers = $_POST['date_embauche_pers'] ?? '';
            $nouveau_login_pers = trim($_POST['login_pers'] ?? '');
            $nouveau_id_tu = $_POST['id_tu'] ?? '';
            $regenerer_mot_de_passe = isset($_POST['regenerer_mot_de_passe']);

            // Validation
            if (empty($id_pers_original) || empty($nouveau_nom_pers) || empty($nouveau_prenom_pers) || 
                empty($nouveau_email_pers) || empty($nouveau_date_naiss_pers) || empty($nouveau_poste_pers) || 
                empty($nouvelle_date_embauche_pers) || empty($nouveau_login_pers) || empty($nouveau_id_tu)) {
                $error_message = "Tous les champs sont obligatoires.";
                break;
            }

            // Validation email
            if (!filter_var($nouveau_email_pers, FILTER_VALIDATE_EMAIL)) {
                $error_message = "L'adresse email n'est pas valide.";
                break;
            }

            try {
                $pdo->beginTransaction();
                
                // Vérifier que le type d'utilisateur existe
                $checkTypeUser = $pdo->prepare("SELECT COUNT(*) FROM type_utilisateur WHERE id_tu = ?");
                $checkTypeUser->execute([$nouveau_id_tu]);
                if ($checkTypeUser->fetchColumn() == 0) {
                    throw new Exception("Le type d'utilisateur sélectionné n'existe pas !");
                }
                
                // Récupérer l'id_util du personnel
                $stmt_get_id = $pdo->prepare("SELECT id_util FROM personnel_admin WHERE id_pers = ?");
                $stmt_get_id->execute([$id_pers_original]);
                $id_util = $stmt_get_id->fetchColumn();
                if (!$id_util) {
                    throw new Exception("Personnel administratif non trouvé !");
                }

                // Vérifier si le login existe déjà (sauf pour l'utilisateur actuel)
                $checkLogin = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE login_util = ? AND id_util != ?");
                $checkLogin->execute([$nouveau_login_pers, $id_util]);
                if ($checkLogin->fetchColumn() > 0) {
                    throw new Exception("Ce login existe déjà !");
                }

                // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
                $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM personnel_admin WHERE email_pers = ? AND id_pers != ?");
                $checkEmail->execute([$nouveau_email_pers, $id_pers_original]);
                if ($checkEmail->fetchColumn() > 0) {
                    throw new Exception("Cette adresse email existe déjà !");
                }

                if ($regenerer_mot_de_passe) {
                    // Régénération du mot de passe
                    $mot_de_passe_genere = substr(strtoupper($nouveau_nom_pers), 0, 2) . 
                                         substr(ucfirst(strtolower($nouveau_prenom_pers)), 0, 2) . 
                                         'PerAd@2025';
                    
                    // Mise à jour de l'utilisateur avec nouveau mot de passe et type
                    $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ?, mdp_util = ?, id_tu = ? WHERE id_util = ?");
                    $stmt_user->execute([$nouveau_login_pers, password_hash($mot_de_passe_genere, PASSWORD_DEFAULT), $nouveau_id_tu, $id_util]);
                    
                    // Mise à jour du personnel avec nouveau mot de passe ET id_tu
                    $stmt_pers = $pdo->prepare("UPDATE personnel_admin SET nom_pers = ?, prenoms_pers = ?, email_pers = ?, date_naiss_pers = ?, poste_pers = ?, date_embauche_pers = ?, login_pers = ?, mdp_pers = ?, id_tu = ? WHERE id_pers = ?");
                    $stmt_pers->execute([$nouveau_nom_pers, $nouveau_prenom_pers, $nouveau_email_pers, 
                                       $nouveau_date_naiss_pers, $nouveau_poste_pers, $nouvelle_date_embauche_pers, 
                                       $nouveau_login_pers, $mot_de_passe_genere, $nouveau_id_tu, $id_pers_original]);
                    
                    $success_message = "Personnel modifié avec succès ! Nouveau mot de passe : " . $mot_de_passe_genere;
                } else {
                    // Mise à jour sans changer le mot de passe
                    $stmt_user = $pdo->prepare("UPDATE utilisateur SET login_util = ?, id_tu = ? WHERE id_util = ?");
                    $stmt_user->execute([$nouveau_login_pers, $nouveau_id_tu, $id_util]);
                    
                    // Mise à jour du personnel sans mot de passe mais avec id_tu
                    $stmt_pers = $pdo->prepare("UPDATE personnel_admin SET nom_pers = ?, prenoms_pers = ?, email_pers = ?, date_naiss_pers = ?, poste_pers = ?, date_embauche_pers = ?, login_pers = ?, id_tu = ? WHERE id_pers = ?");
                    $stmt_pers->execute([$nouveau_nom_pers, $nouveau_prenom_pers, $nouveau_email_pers, 
                                       $nouveau_date_naiss_pers, $nouveau_poste_pers, $nouvelle_date_embauche_pers, 
                                       $nouveau_login_pers, $nouveau_id_tu, $id_pers_original]);
                    
                    $success_message = "Personnel modifié avec succès !";
                }

                $pdo->commit();
                
                // Rediriger pour éviter la resoumission du formulaire
                header("Location: Personnel_admin.php?success=" . urlencode($success_message));
                exit;
                
            } catch (Exception $e) {
                $pdo->rollback();
                $error_message = "Erreur lors de la modification : " . $e->getMessage();
            }
            break;

        case 'supprimer':
            $id_pers = $_POST['id_pers'] ?? '';

            if (empty($id_pers)) {
                $error_message = "ID du personnel manquant.";
                break;
            }

            try {
                $pdo->beginTransaction();
                
                // Récupérer l'id_util avant suppression
                $stmt_get_id = $pdo->prepare("SELECT id_util FROM personnel_admin WHERE id_pers = ?");
                $stmt_get_id->execute([$id_pers]);
                $id_util = $stmt_get_id->fetchColumn();

                if (!$id_util) {
                    throw new Exception("Personnel non trouvé !");
                }

                // Supprimer le personnel en premier
                $stmt_pers = $pdo->prepare("DELETE FROM personnel_admin WHERE id_pers = ?");
                $stmt_pers->execute([$id_pers]);

                // Puis supprimer l'utilisateur
                $stmt_user = $pdo->prepare("DELETE FROM utilisateur WHERE id_util = ?");
                $stmt_user->execute([$id_util]);

                $pdo->commit();
                $success_message = "Personnel supprimé avec succès !";
                
            } catch (Exception $e) {
                $pdo->rollback();
                $error_message = "Erreur lors de la suppression : " . $e->getMessage();
            }
            break;
    }
}

// Gestion du mode édition
if (isset($_GET['modifier'])) {
    $id_pers_a_modifier = $_GET['modifier'];
    try {
        $stmt = $pdo->prepare("
            SELECT pa.*, u.id_tu 
            FROM personnel_admin pa 
            LEFT JOIN utilisateur u ON pa.id_util = u.id_util 
            WHERE pa.id_pers = ?
        ");
        $stmt->execute([$id_pers_a_modifier]);
        $personnel_admin_a_modifier = $stmt->fetch();
        if ($personnel_admin_a_modifier) {
            $mode_edition = true;
        } else {
            $error_message = "Personnel non trouvé !";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}

// Gestion du message de succès depuis l'URL
if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à Jour du Personnel Administratif</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/personnel_admin.css">
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
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            font-size: 14px;
        }
        
        .btn-confirm {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-confirm:hover {
            background-color: #c82333;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        
        .success-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: none;
        }
        
        .error-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: none;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-mode-indicator {
            background-color: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .edit-mode-indicator h3 {
            color: #007bff;
            margin: 0 0 10px 0;
        }

        .required {
            color: #dc3545;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>

        <h1 class="page-title">Gestion du Personnel Administratif</h1>

        <?php if (!empty($success_message)) : ?>
            <div class="success-message" style="display: block;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)) : ?>
            <div class="error-message" style="display: block;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <section class="form-container">
            <?php if ($mode_edition): ?>
                <div class="edit-mode-indicator">
                    <h3><i class="fas fa-edit"></i> Mode édition</h3>
                    <p>Vous modifiez les informations de : <strong><?= htmlspecialchars($personnel_admin_a_modifier['nom_pers'] . ' ' . $personnel_admin_a_modifier['prenoms_pers']) ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="section-title">
                <?= $mode_edition ? 'Modifier un personnel' : 'Ajouter un personnel' ?>
            </div>
            
            <form method="POST" id="personnelForm">
                <?php if ($mode_edition): ?>
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="id_pers_original" value="<?= htmlspecialchars($personnel_admin_a_modifier['id_pers']) ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="ajouter">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nom <span class="required">*</span></label>
                        <input type="text" name="nom_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['nom_pers']) : (isset($_POST['nom_pers']) ? htmlspecialchars($_POST['nom_pers']) : '') ?>"
                               maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Prénoms <span class="required">*</span></label>
                        <input type="text" name="prenoms_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['prenoms_pers']) : (isset($_POST['prenoms_pers']) ? htmlspecialchars($_POST['prenoms_pers']) : '') ?>"
                               maxlength="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['email_pers']) : (isset($_POST['email_pers']) ? htmlspecialchars($_POST['email_pers']) : '') ?>"
                               maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Date de naissance <span class="required">*</span></label>
                        <input type="date" name="date_naiss_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['date_naiss_pers']) : (isset($_POST['date_naiss_pers']) ? htmlspecialchars($_POST['date_naiss_pers']) : '') ?>"
                               max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                    </div>
                </div>

                <div class="section-title" style="margin-top: 3rem;">Informations professionnelles</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Poste <span class="required">*</span></label>
                        <input type="text" name="poste_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['poste_pers']) : (isset($_POST['poste_pers']) ? htmlspecialchars($_POST['poste_pers']) : '') ?>"
                               maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Date d'embauche <span class="required">*</span></label>
                        <input type="date" name="date_embauche_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['date_embauche_pers']) : (isset($_POST['date_embauche_pers']) ? htmlspecialchars($_POST['date_embauche_pers']) : '') ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Login <span class="required">*</span></label>
                        <input type="email" name="login_pers" required 
                               value="<?= $mode_edition ? htmlspecialchars($personnel_admin_a_modifier['login_pers']) : (isset($_POST['login_pers']) ? htmlspecialchars($_POST['login_pers']) : '') ?>"
                               maxlength="100"
                               placeholder="exemple@domaine.com">
                    </div>
                    <div class="form-group">
                        <label for="id_tu">Type d'utilisateur <span class="required">*</span></label>
                        <select name="id_tu" id="id_tu" required>
                            <option value="">-- Choisir un type --</option>
                            <?php
                            try {
                                $types = $pdo->query("SELECT id_tu, lib_tu FROM type_utilisateur ORDER BY lib_tu")->fetchAll();
                                foreach ($types as $type) {
                                    $selected = '';
                                    if ($mode_edition && $personnel_admin_a_modifier['id_tu'] == $type['id_tu']) {
                                        $selected = 'selected';
                                    } elseif (!$mode_edition && isset($_POST['id_tu']) && $_POST['id_tu'] == $type['id_tu']) {
                                        $selected = 'selected';
                                    }
                                    echo "<option value='{$type['id_tu']}' $selected>" . htmlspecialchars($type['lib_tu']) . "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option disabled>Erreur de chargement</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php if ($mode_edition): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Options de mot de passe</label>
                            <div class="checkbox-group">
                                <input type="checkbox" name="regenerer_mot_de_passe" id="regenerer_mot_de_passe">
                                <label for="regenerer_mot_de_passe">Régénérer le mot de passe (Format: 2 lettres nom + 2 lettres prénom + PerAd@2025)</label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="button-container">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> <?= $mode_edition ? 'Modifier' : 'Ajouter' ?>
                    </button>
                    <?php if ($mode_edition): ?>
                        <a href="Personnel_admin.php" class="submit-btn" style="background: var(--danger-red); margin-left: 1rem; text-decoration: none;">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="table-container">
            <div class="table-header">
                <i class="fas fa-users"></i> Liste du Personnel
            </div>
            <div class="table-responsive">
                <table class="personnel-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Email</th>
                            <th>Poste</th>
                            <th>Type utilisateur</th>
                            <th>Date embauche</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("
                                SELECT pa.*, tu.lib_tu 
                                FROM personnel_admin pa
                                LEFT JOIN utilisateur u ON pa.id_util = u.id_util
                                LEFT JOIN type_utilisateur tu ON u.id_tu = tu.id_tu
                                ORDER BY pa.nom_pers, pa.prenoms_pers
                            ");
                            $rows = $stmt->fetchAll();
                            
                            if (!$rows) {
                                echo '<tr><td colspan="8" style="text-align:center; padding: 20px;">
                                        <i class="fas fa-info-circle"></i> Aucun personnel enregistré.
                                      </td></tr>';
                            } else {
                                foreach ($rows as $row) {
                                    echo '<tr>
                                            <td>' . htmlspecialchars($row['id_pers']) . '</td>
                                            <td>' . htmlspecialchars($row['nom_pers']) . '</td>
                                            <td>' . htmlspecialchars($row['prenoms_pers']) . '</td>
                                            <td>' . htmlspecialchars($row['email_pers']) . '</td>
                                            <td>' . htmlspecialchars($row['poste_pers']) . '</td>
                                            <td>' . htmlspecialchars($row['lib_tu'] ?? 'Non défini') . '</td>
                                            <td>' . date('d/m/Y', strtotime($row['date_embauche_pers'])) . '</td>
                                            <td>
                                                <a href="Personnel_admin.php?modifier=' . urlencode($row['id_pers']) . '" 
                                                   class="action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                                <button type="button" class="action-btn delete" 
                                                        onclick="confirmerSuppression(\'' . $row['id_pers'] . '\', \'' . htmlspecialchars($row['nom_pers'] . ' ' . $row['prenoms_pers']) . '\')"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </td>
                                          </tr>';
                                }
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="8" style="text-align:center; color: #dc3545; padding: 20px;">
                                    <i class="fas fa-exclamation-triangle"></i> Erreur : ' . htmlspecialchars($e->getMessage()) . '
                                  </td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

    <!-- Modal de confirmation de suppression -->
    <div id="modalSuppression" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i> Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer le personnel