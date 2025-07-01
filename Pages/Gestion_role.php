<?php
include('../config/connexion_BD.php');

// Récupérer les rôles
$roles = $pdo->query("SELECT * FROM type_utilisateur ORDER BY lib_tu")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les permissions (actions)
$permissions = $pdo->query("SELECT * FROM permissions ORDER BY description")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le rôle sélectionné
$selectedRoleId = $_POST['lib_tu'] ?? null;

// Enregistrement des permissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $selectedRoleId = (int)$_POST['lib_tu'];

    // Supprimer les permissions existantes pour le rôle
    $pdo->prepare("DELETE FROM role_permission WHERE id_tu = ?")->execute([$selectedRoleId]);

    // Ajouter les nouvelles permissions
    if (!empty($_POST['permissions']) && is_array($_POST['permissions'])) {
        $stmt = $pdo->prepare("INSERT INTO role_permission 
            (id_tu, id_permission, creation, lecture, modification, suppression) 
            VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($_POST['permissions'] as $id_permission => $droits) {
            $creation = isset($droits['creation']) ? 1 : 0;
            $lecture = isset($droits['lecture']) ? 1 : 0;
            $modification = isset($droits['modification']) ? 1 : 0;
            $suppression = isset($droits['suppression']) ? 1 : 0;
            $stmt->execute([$selectedRoleId, $id_permission, $creation, $lecture, $modification, $suppression]);
        }
    }

    $success = true;
}

// Charger les permissions existantes pour ce rôle
$rolePermissions = [];
if ($selectedRoleId) {
    $stmt = $pdo->prepare("SELECT * FROM role_permission WHERE id_tu = ?");
    $stmt->execute([$selectedRoleId]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rp) {
        $rolePermissions[$rp['id_permission']] = $rp;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des rôles et permissions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        h1 { margin-bottom: 20px; }
        select { padding: 5px; margin-bottom: 10px; }
        button { padding: 10px 20px; margin-top: 10px; }
        .success { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>

<h1>Gestion des rôles et permissions</h1>

<?php if (isset($success)) : ?>
    <div class="success">✅ Permissions enregistrées avec succès.</div>
<?php endif; ?>

<form method="POST" action="Gestion_role.php">
    <label>Sélectionnez un rôle :</label>
    <select name="lib_tu" onchange="this.form.submit()">
        <option value="">-- Choisir un rôle --</option>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role['id_tu'] ?>" <?= ($selectedRoleId == $role['id_tu']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($role['lib_tu']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selectedRoleId): ?>
<form method="POST" action="">
    <input type="hidden" name="lib_tu" value="<?= $selectedRoleId ?>">

    <table>
        <thead>
            <tr>
                <th>Action</th>
                <th>Création</th>
                <th>Lecture</th>
                <th>Modification</th>
                <th>Suppression</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissions as $perm): 
                $droits = $rolePermissions[$perm['id']] ?? [];
            ?>
            <tr>
                <td><?= htmlspecialchars($perm['description']) ?></td>
                <td><input type="checkbox" name="permissions[<?= $perm['id'] ?>][creation]" <?= !empty($droits['creation']) ? 'checked' : '' ?>></td>
                <td><input type="checkbox" name="permissions[<?= $perm['id'] ?>][lecture]" <?= !empty($droits['lecture']) ? 'checked' : '' ?>></td>
                <td><input type="checkbox" name="permissions[<?= $perm['id'] ?>][modification]" <?= !empty($droits['modification']) ? 'checked' : '' ?>></td>
                <td><input type="checkbox" name="permissions[<?= $perm['id'] ?>][suppression]" <?= !empty($droits['suppression']) ? 'checked' : '' ?>></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" name="save">Enregistrer</button>
</form>
<?php endif; ?>

</body>
</html>
