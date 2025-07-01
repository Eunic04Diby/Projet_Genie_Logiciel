<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$semestre_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $code_semes = $_POST['code_semes'] ?? '';
    $libelle_semes = strtoupper(trim($_POST['lib_semes'] ?? ''));
    $id_semes = $_POST['id_semes'] ?? null;

    switch ($action) {
        case 'ajouter':
            if (empty($code_semes) || empty($libelle_semes)) {
                $error_message = "Tous les champs sont requis.";
                break;
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO semestre (code_semes, lib_semes) VALUES (?, ?)");
                $stmt->execute([$code_semes, $libelle_semes]);
                $success_message = "Semestre ajouté avec succès.";
            } catch (PDOException $e) {
                $error_message = "Erreur : " . $e->getMessage();
            }
            break;

        case 'modifier':
            if (empty($code_semes) || empty($libelle_semes) || empty($id_semes)) {
                $error_message = "Tous les champs sont requis pour la modification.";
                break;
            }

            try {
                $stmt = $pdo->prepare("UPDATE semestre SET code_semes = ?, lib_semes = ? WHERE id_semes = ?");
                $stmt->execute([$code_semes, $libelle_semes, $id_semes]);
                $success_message = "Semestre modifié avec succès.";
            } catch (PDOException $e) {
                $error_message = "Erreur : " . $e->getMessage();
            }
            break;

        case 'supprimer':
            $id_semes = $_POST['id_semes'] ?? '';
            if (empty($id_semes)) {
                $error_message = "ID manquant pour suppression.";
                break;
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM semestre WHERE id_semes = ?");
                $stmt->execute([$id_semes]);
                $success_message = "Semestre supprimé avec succès.";
            } catch (PDOException $e) {
                $error_message = "Erreur : " . $e->getMessage();
            }
            break;
    }
}

// Préremplissage en mode édition
if (isset($_GET['modifier'])) {
    $id_mod = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM semestre WHERE id_semes = ?");
    $stmt->execute([$id_mod]);
    $semestre_a_modifier = $stmt->fetch();

    if ($semestre_a_modifier) {
        $mode_edition = true;
    } else {
        $error_message = "Aucun semestre trouvé avec cet identifiant.";
        $mode_edition = false;
    }
}

// Récupération des semestres
$stmt = $pdo->query("SELECT * FROM semestre ORDER BY id_semes ASC");
$semestres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Semestres</title>
    <link rel="stylesheet" href="../css/maj_UE.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        .form-container, .table-container { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 250px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; }
        .validate-btn { background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
        .btn-edit, .btn-delete { padding: 6px 12px; margin-right: 5px; border-radius: 4px; border: none; cursor: pointer; }
        .btn-edit { background: #ffc107; }
        .btn-delete { background: #dc3545; color: white; }
        .success-message, .error-message { padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        .success-message { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-message { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #4CAF50; color: white; }
    </style>
</head>
<body>

<h1>Gestion des Semestres</h1>

<?php if ($success_message): ?>
    <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST">
        <?php if ($mode_edition && $semestre_a_modifier): ?>
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_semes" value="<?= $semestre_a_modifier['id_semes'] ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="ajouter">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="code_semes">Code du semestre *</label>
                <input type="text" id="code_semes" name="code_semes" placeholder="Ex: S1, S2, etc." required 
                       value="<?= $mode_edition && $semestre_a_modifier ? htmlspecialchars($semestre_a_modifier['code_semes']) : '' ?>" 
                       maxlength="10" pattern="S[1-9]" title="Format attendu : S1, S2, etc.">
            </div>

            <div class="form-group">
                <label for="lib_semes">Libellé du semestre *</label>
                <input type="text" id="lib_semes" name="lib_semes" placeholder="Ex: Semestre1, etc." required 
                       value="<?= $mode_edition && $semestre_a_modifier ? htmlspecialchars($semestre_a_modifier['lib_semes']) : '' ?>" 
                       maxlength="20" pattern="Semestre[1-9]" title="Format attendu : Semestre1 , etc.">
            </div>
        </div>

        <div style="margin-top: 15px;">
            <button type="submit" class="validate-btn"><?= $mode_edition ? 'Modifier' : 'Ajouter' ?></button>
            <?php if ($mode_edition): ?>
                <a href="MAJ_Semestre.php" class="validate-btn" style="background: #6c757d;">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <h2>Liste des semestres</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Libellé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($semestres) === 0): ?>
                <tr><td colspan="4">Aucun semestre enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($semestres as $sem): ?>
                    <tr>
                        <td><?= $sem['id_semes'] ?></td>
                        <td><?= htmlspecialchars($sem['code_semes']) ?></td>
                        <td><?= htmlspecialchars($sem['lib_semes']) ?></td>
                        <td>
                            <a href="?modifier=<?= $sem['id_semes'] ?>" class="btn-edit">Modifier</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce semestre ?');">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_semes" value="<?= $sem['id_semes'] ?>">
                                <button type="submit" class="btn-delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
