<?php
// Démarrage de la session pour la protection CSRF
session_start();

// Génération du token CSRF si inexistant
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include('../config/connexion_BD.php');

// Classe pour gérer les fonctions
class FonctionManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Validation des données d'entrée
    private function validateFonction($id_fonct, $nom_fonct) {
        $errors = [];
        
        // Validation ID fonction
        if (empty($id_fonct)) {
            $errors[] = "L'identifiant est obligatoire.";
        } elseif (strlen($id_fonct) > 20) {
            $errors[] = "L'identifiant ne peut pas dépasser 20 caractères.";
        } elseif (!preg_match('/^[A-Za-z0-9_-]+$/', $id_fonct)) {
            $errors[] = "L'identifiant ne peut contenir que des lettres, chiffres, tirets et underscores.";
        }
        
        // Validation nom fonction
        if (empty($nom_fonct)) {
            $errors[] = "Le nom de la fonction est obligatoire.";
        } elseif (strlen($nom_fonct) > 100) {
            $errors[] = "Le nom ne peut pas dépasser 100 caractères.";
        }
        
        return $errors;
    }
    
    // Vérifier l'unicité de l'ID
    private function isIdUnique($id_fonct, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM fonction WHERE id_fonct = ?";
        $params = [$id_fonct];
        
        if ($exclude_id) {
            $sql .= " AND id_fonct != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }
    
    // Vérifier les dépendances avant suppression
    private function hasDependencies($id_fonct) {
        // Exemple : vérifier si la fonction est utilisée par des utilisateurs
        // Adaptez selon votre structure de base de données
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE id_fonct = ?");
        $stmt->execute([$id_fonct]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Ajouter une fonction
    public function ajouterFonction($id_fonct, $nom_fonct) {
        $errors = $this->validateFonction($id_fonct, $nom_fonct);
        
        if (!$this->isIdUnique($id_fonct)) {
            $errors[] = "Cet identifiant existe déjà.";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $stmt = $this->pdo->prepare("INSERT INTO fonction (id_fonct, nom_fonct) VALUES (?, ?)");
            $stmt->execute([$id_fonct, $nom_fonct]);
            return ['success' => true, 'message' => 'Fonction ajoutée avec succès !'];
        } catch (PDOException $e) {
            error_log("Erreur ajout fonction: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Une erreur est survenue lors de l\'ajout.']];
        }
    }
    
    // Modifier une fonction
    public function modifierFonction($id_original, $nouveau_id, $nouveau_nom) {
        $errors = $this->validateFonction($nouveau_id, $nouveau_nom);
        
        if ($id_original !== $nouveau_id && !$this->isIdUnique($nouveau_id, $id_original)) {
            $errors[] = "Ce nouvel identifiant existe déjà.";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE fonction SET id_fonct = ?, nom_fonct = ? WHERE id_fonct = ?");
            $stmt->execute([$nouveau_id, $nouveau_nom, $id_original]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Fonction modifiée avec succès !'];
            } else {
                return ['success' => false, 'errors' => ['Fonction non trouvée.']];
            }
        } catch (PDOException $e) {
            error_log("Erreur modification fonction: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Une erreur est survenue lors de la modification.']];
        }
    }
    
    // Supprimer une fonction
    public function supprimerFonction($id_fonct) {
        if ($this->hasDependencies($id_fonct)) {
            return ['success' => false, 'errors' => ['Cette fonction ne peut pas être supprimée car elle est utilisée.']];
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM fonction WHERE id_fonct = ?");
            $stmt->execute([$id_fonct]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Fonction supprimée avec succès !'];
            } else {
                return ['success' => false, 'errors' => ['Fonction non trouvée.']];
            }
        } catch (PDOException $e) {
            error_log("Erreur suppression fonction: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Une erreur est survenue lors de la suppression.']];
        }
    }
    
    // Récupérer une fonction par ID
    public function getFonctionById($id_fonct) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM fonction WHERE id_fonct = ?");
            $stmt->execute([$id_fonct]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération fonction: " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer toutes les fonctions
    public function getAllFonctions() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM fonction ORDER BY nom_fonct ASC LIMIT 1000");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération fonctions: " . $e->getMessage());
            return [];
        }
    }
}

// Fonction pour vérifier le token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fonction pour échapper les données HTML
function escapeHtml($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Initialisation
$fonctionManager = new FonctionManager($pdo);
$success_message = '';
$error_messages = [];
$mode_edition = false;
$fonction_a_modifier = null;

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error_messages[] = "Token de sécurité invalide.";
    } else {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'ajouter':
                $id_fonct = trim($_POST['identifiant'] ?? '');
                $nom_fonct = trim($_POST['libelle'] ?? '');
                
                $result = $fonctionManager->ajouterFonction($id_fonct, $nom_fonct);
                if ($result['success']) {
                    $success_message = $result['message'];
                    // Redirection pour éviter le re-submit
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($result['message']));
                    exit;
                } else {
                    $error_messages = $result['errors'];
                }
                break;

            case 'modifier':
                $id_original = trim($_POST['id_original'] ?? '');
                $nouveau_id = trim($_POST['identifiant'] ?? '');
                $nouveau_nom = trim($_POST['libelle'] ?? '');
                
                $result = $fonctionManager->modifierFonction($id_original, $nouveau_id, $nouveau_nom);
                if ($result['success']) {
                    $success_message = $result['message'];
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($result['message']));
                    exit;
                } else {
                    $error_messages = $result['errors'];
                    // Rester en mode édition
                    $fonction_a_modifier = $fonctionManager->getFonctionById($id_original);
                    $mode_edition = true;
                }
                break;

            case 'supprimer':
                $id_fonct = trim($_POST['id_fonction'] ?? '');
                
                $result = $fonctionManager->supprimerFonction($id_fonct);
                if ($result['success']) {
                    $success_message = $result['message'];
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($result['message']));
                    exit;
                } else {
                    $error_messages = $result['errors'];
                }
                break;
        }
    }
}

// Mode édition via GET
if (isset($_GET['modifier'])) {
    $id_a_modifier = trim($_GET['modifier']);
    $fonction_a_modifier = $fonctionManager->getFonctionById($id_a_modifier);
    if ($fonction_a_modifier) {
        $mode_edition = true;
    } else {
        $error_messages[] = "Fonction non trouvée.";
    }
}

// Message de succès via GET (après redirection)
if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
}

// Récupération des fonctions
$fonctions = $fonctionManager->getAllFonctions();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour des fonctions</title>
    <link rel="stylesheet" href="../css/niveau_approbation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
           .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            display: block;
        }
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            display: block;
        }
        
        /* Mode édition - Style spécial */
        .form-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .form-container.form-edit-mode {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);
        }
        
        .edit-mode-title {
            background: #2196f3;
            color: white;
            padding: 12px 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 16px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2196f3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .form-edit-mode .form-group input {
            background-color: white;
            border-color: #2196f3;
        }
        
        .btn-add, .btn-cancel {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 120px;
        }
        
        .btn-add {
            background: #4caf50;
            color: white;
        }
        
        .btn-add:hover {
            background: #45a049;
            transform: translateY(-1px);
        }
        
        .form-edit-mode .btn-add {
            background: #4caf50;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        /* Table styles */
        .table-container {
            margin-top: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table-header {
            background: #4caf50;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-right: 8px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: #007bff;
            color: white;
        }
        
        .btn-edit:hover {
            background: #0056b3;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border: 0;
        }
        
        .empty-state {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
        }
        
        /* Modal styles */
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
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .modal-content h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        
        .modal-buttons {
            margin-top: 20px;
            text-align: right;
        }
        
        .btn-confirm {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        
        .btn-confirm:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

    <h1 class="page-title">Mise à jour des fonctions</h1>

    <?php if (!empty($success_message)) : ?>
        <div class="success-message" role="alert" aria-live="polite">
            ✅ <?= escapeHtml($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_messages)) : ?>
        <div class="error-messages" role="alert" aria-live="polite">
            ❌ 
            <?php if (count($error_messages) === 1) : ?>
                <?= escapeHtml($error_messages[0]) ?>
            <?php else : ?>
                <ul>
                    <?php foreach ($error_messages as $error) : ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-container <?= $mode_edition ? 'form-edit-mode' : '' ?>">
        <?php if ($mode_edition) : ?>
            <div class="edit-mode-title">
                Mode modification - Fonction: <?= escapeHtml($fonction_a_modifier['nom_fonct']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-row" action="MAJ_Fonction.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['csrf_token']) ?>">
            
            <?php if ($mode_edition) : ?>
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id_original" value="<?= escapeHtml($fonction_a_modifier['id_fonct']) ?>">
            <?php else : ?>
                <input type="hidden" name="action" value="ajouter">
            <?php endif; ?>

            <div class="form-group">
                <label for="identifiant">ID Fonction *</label>
                <input type="text" 
                       id="identifiant" 
                       name="identifiant" 
                       placeholder="Saisir l'identifiant"
                       value="<?= $mode_edition ? escapeHtml($fonction_a_modifier['id_fonct']) : '' ?>"
                       maxlength="20"
                       pattern="[A-Za-z0-9_-]+"
                       title="Seuls les lettres, chiffres, tirets et underscores sont autorisés"
                       required
                       aria-describedby="identifiant-help">
            </div>
            
            <div class="form-group">
                <label for="libelle">Nom de la fonction *</label>
                <input type="text" 
                       id="libelle" 
                       name="libelle" 
                       placeholder="Saisir le nom"
                       value="<?= $mode_edition ? escapeHtml($fonction_a_modifier['nom_fonct']) : '' ?>"
                       maxlength="100"
                       required
                       aria-describedby="libelle-help">
            </div>
            
            <button class="btn-add" type="submit">
                <?= $mode_edition ? 'Modifier' : 'Ajouter' ?>
            </button>
            
            <?php if ($mode_edition) : ?>
                <a href="<?= escapeHtml($_SERVER['PHP_SELF']) ?>" class="btn-cancel" style="margin-left: 10px;">
                    Annuler
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header">Liste des fonctions</div>
        <table class="data-table" role="table">
            <thead>
                <tr>
                    <th scope="col" class="numero-col">N°</th>
                    <th scope="col">ID Fonction</th>
                    <th scope="col">Nom de la fonction</th>
                    <th scope="col" class="action-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fonctions)) : ?>
                    <tr>
                        <td colspan="4" class="empty-state">Aucune fonction enregistrée.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($fonctions as $index => $fonction) : ?>
                        <?php 
                        $row_class = ($mode_edition && $fonction['id_fonct'] === $fonction_a_modifier['id_fonct']) 
                            ? 'style="background-color: #e7f3ff;"' : '';
                        ?>
                        <tr <?= $row_class ?>>
                            <td><?= $index + 1 ?></td>
                            <td><?= escapeHtml($fonction['id_fonct']) ?></td>
                            <td><?= escapeHtml($fonction['nom_fonct']) ?></td>
                            <td>
                                <a href="<?= escapeHtml($_SERVER['PHP_SELF']) ?>?modifier=<?= urlencode($fonction['id_fonct']) ?>" 
                                   class="btn-edit"
                                   title="Modifier la fonction <?= escapeHtml($fonction['nom_fonct']) ?>">
                                    Modifier
                                </a>
                                <button type="button" 
                                        class="btn-delete" 
                                        onclick="confirmerSuppression('<?= escapeHtml($fonction['id_fonct']) ?>', '<?= escapeHtml($fonction['nom_fonct']) ?>')"
                                        title="Supprimer la fonction <?= escapeHtml($fonction['nom_fonct']) ?>">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<!-- Modal de confirmation -->
<div id="modalSuppression" class="modal" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-content">
        <h3 id="modal-title">Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer la fonction "<span id="nomFonction"></span>" ?</p>
        <div class="modal-buttons">
            <button type="button" class="btn-confirm" onclick="supprimerFonction()">
                Oui, supprimer
            </button>
            <button type="button" class="btn-cancel" onclick="fermerModal()">
                Annuler
            </button>
        </div>
    </div>
</div>

<!-- Formulaire caché pour suppression -->
<form id="formSuppression" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="supprimer">
    <input type="hidden" name="id_fonction" id="idFonctionSupprimer">
</form>
<script src="../js/niveau_approbation.js"></script>
</body>
</html>