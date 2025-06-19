<?php
include('../config/connexion_BD.php');

$id_action = '';
$lib_action = '';
$update = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_action = $_POST['id_action'] ?? '';
    $lib_action = $_POST['lib_action'] ?? '';

    if (isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare("INSERT INTO action (id_action, lib_action) VALUES (?, ?)");
        $stmt->execute([$id_action, $lib_action]);
        echo "<script>alert('Action ajoutée avec succès !'); window.location.href='MAJ_action.php';</script>";
        exit();
    } elseif (isset($_POST['modifier'])) {
        $stmt = $pdo->prepare("UPDATE action SET lib_action = ? WHERE id_action = ?");
        $stmt->execute([$lib_action, $id_action]);
        echo "<script>alert('Action modifiée avec succès !'); window.location.href='MAJ_action.php';</script>";
        exit();
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM action WHERE id_action = ?");
    $stmt->execute([$_GET['delete']]);
    echo "<script>alert('Action supprimée avec succès !'); window.location.href='MAJ_action.php';</script>";
    exit();
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM action WHERE id_action = ?");
    $stmt->execute([$_GET['edit']]);
    $row = $stmt->fetch();
    if ($row) {
        $id_action = $row['id_action'];
        $lib_action = $row['lib_action'];
        $update = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour des actions</title>
    <link rel="stylesheet" href="../css/niveau_approbation.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>

<header>
    <div class="logo">LOGO</div>
    <div class="search-bar"><input type="text" placeholder="Rechercher..."></div>
    <div class="user-profile">NOM UTILISATEUR</div>
</header>

<aside class="sidebar">
    <ul>
        <li class="active"><span>📋</span><span>Fonctions</span></li>
        <li><span>👥</span><span>Utilisateurs</span></li>
        <li><span>⚙️</span><span>Paramètres</span></li>
        <li><span>📊</span><span>Rapports</span></li>
    </ul>
</aside>

<main>
    <h1 class="page-title">Mise à jour des actions</h1>

    <div class="form-card">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Identifiant</label>
                    <input type="text" name="id_action" placeholder="Saisir l'identifiant" value="<?= htmlspecialchars($id_action) ?>" <?= $update ? 'readonly' : '' ?> required>
                </div>
                <div class="form-group">
                    <label>Libellé</label>
                    <input type="text" name="lib_action" placeholder="Saisir le libellé" value="<?= htmlspecialchars($lib_action) ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="<?= $update ? 'modifier' : 'ajouter' ?>" class="btn-add">
                        <?= $update ? 'Mettre à jour' : 'Ajouter' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
<br>
    <div class="table-card">
        <div class="table-header">Liste des actions</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Identifiant</th>
                    <th>Libellé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM action");
                $numero = 1;
                foreach ($stmt as $row):
                ?>
                <tr>
                    <td><?= $numero++ ?></td>
                    <td><?= htmlspecialchars($row['id_action']) ?></td>
                    <td><?= htmlspecialchars($row['lib_action']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id_action'] ?>" class="btn-edit">Modifier</a>
                        <a href="?delete=<?= $row['id_action'] ?>" class="btn-delete" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
