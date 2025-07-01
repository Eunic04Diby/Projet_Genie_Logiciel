<?php
include('../config/connexion_BD.php');

$success = '';
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($code && $description) {
        try {
            $stmt = $pdo->prepare("INSERT INTO permissions (code, description) VALUES (?, ?)");
            $stmt->execute([$code, $description]);
            $success = "✅ Action ajoutée avec succès.";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = "❌ Ce code d'action existe déjà.";
            } else {
                $error = "❌ Erreur lors de l'ajout : " . $e->getMessage();
            }
        }
    } else {
        $error = "❌ Tous les champs sont requis.";
    }
}

// Charger toutes les permissions
$permissions = $pdo->query("SELECT * FROM permissions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Action</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; margin-bottom: 30px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 8px; margin-bottom: 15px; }
        button { padding: 10px 20px; }
        .message { margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; max-width: 700px; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

    <h1>Ajouter une Action (Permission)</h1>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="code">Code de l'action <small>(ex: `deposer_rapport`)</small></label>
        <input type="text" name="code" id="code" required>

        <label for="description">Description</label>
        <input type="text" name="description" id="description" required>

        <button type="submit">Ajouter</button>
    </form>

    <h2>Actions déjà enregistrées</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissions as $perm): ?>
                <tr>
                    <td><?= $perm['id'] ?></td>
                    <td><?= htmlspecialchars($perm['code']) ?></td>
                    <td><?= htmlspecialchars($perm['description']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
