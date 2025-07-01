<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';
$mode_edition = false;
$inscription_a_modifier = null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'ajouter':
            $num_etu = $_POST['num_etu'] ?? '';
            $id_ac = $_POST['id_ac'] ?? '';
            $id_niv_etu = $_POST['id_niv_etu'] ?? '';
            $dte_insc = $_POST['dte_insc'] ?? '';
            $montant_insc = $_POST['montant_insc'] ?? '';

            if ($num_etu && $id_ac && $id_niv_etu && $dte_insc && $montant_insc) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO inscription (num_etu, id_ac, id_niv_etu, dte_insc, montant_insc) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$num_etu, $id_ac, $id_niv_etu, $dte_insc, $montant_insc]);
                    $success_message = "Inscription ajoutée avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'modifier':
            $id_insc = $_POST['id_insc'] ?? '';
            $num_etu = $_POST['num_etu'] ?? '';
            $id_ac = $_POST['id_ac'] ?? '';
            $id_niv_etu = $_POST['id_niv_etu'] ?? '';
            $dte_insc = $_POST['dte_insc'] ?? '';
            $montant_insc = $_POST['montant_insc'] ?? '';

            if ($id_insc && $num_etu && $id_ac && $id_niv_etu && $dte_insc && $montant_insc) {
                try {
                    $stmt = $pdo->prepare("UPDATE inscrire SET num_etu = ?, id_ac = ?, id_niv_etu = ?, dte_insc = ?, montant_insc = ? WHERE id_insc = ?");
                    $stmt->execute([$num_etu, $id_ac, $id_niv_etu, $dte_insc, $montant_insc, $id_insc]);
                    $success_message = "Inscription modifiée avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = "Tous les champs sont obligatoires.";
            }
            break;

        case 'supprimer':
            $id_insc = $_POST['id_insc'] ?? '';
            if ($id_insc) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM inscription WHERE id_insc = ?");
                    $stmt->execute([$id_insc]);
                    $success_message = "Inscription supprimée avec succès !";
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
            }
            break;
    }
}

// Mode édition
if (isset($_GET['modifier'])) {
    $id_insc = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM inscription WHERE id_insc = ?");
    $stmt->execute([$id_insc]);
    $inscription_a_modifier = $stmt->fetch();
    if ($inscription_a_modifier) {
        $mode_edition = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Inscriptions</title>
    <link rel="stylesheet" href="../css/inscription.css">
</head>
<body>
    <h2>Gestion des Inscriptions</h2>

    <?php if ($success_message): ?>
        <div class="success-message"> <?= htmlspecialchars($success_message) ?> </div>
    <?php elseif ($error_message): ?>
        <div class="error-message"> <?= htmlspecialchars($error_message) ?> </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="<?= $mode_edition ? 'modifier' : 'ajouter' ?>">
        <?php if ($mode_edition): ?>
            <input type="hidden" name="id_insc" value="<?= $inscription_a_modifier['id_insc'] ?>">
        <?php endif; ?>

        <label>Étudiant :</label>
        <select name="num_etu" required>
            <option value="">-- Choisir --</option>
            <?php
            $etudiants = $pdo->query("SELECT Num_Etu, Nom_Etu, Prenom_Etu FROM etudiant")->fetchAll();
            foreach ($etudiants as $e) {
                $selected = ($mode_edition && $inscription_a_modifier['num_etu'] == $e['Num_Etu']) ? 'selected' : '';
                echo "<option value='{$e['Num_Etu']}' $selected>{$e['Nom_Etud$']} {$e['Prenom_Etu']}</option>";
            }
            ?>
        </select><br>

        <label>Année académique :</label>
        <select name="id_ac" required>
            <option value="">-- Choisir --</option>
            <?php
            $acs = $pdo->query("SELECT id_ac, CONCAT(dte_deb, '-', dte_fin) AS libelle FROM annee_academique")->fetchAll();
            foreach ($acs as $ac) {
                $selected = ($mode_edition && $inscription_a_modifier['id_ac'] == $ac['id_ac']) ? 'selected' : '';
                echo "<option value='{$ac['id_ac']}' $selected>{$ac['libelle']}</option>";
            }
            ?>
        </select><br>

        <label>Niveau :</label>
        <select name="id_niv_etu" required>
            <option value="">-- Choisir --</option>
            <?php
            $niveaux = $pdo->query("SELECT id_niv_etu, lib_niv_etu FROM niveau_etude")->fetchAll();
            foreach ($niveaux as $niv) {
                $selected = ($mode_edition && $inscription_a_modifier['id_niv_etu'] == $niv['id_niv_etu']) ? 'selected' : '';
                echo "<option value='{$niv['id_niv_etu']}' $selected>{$niv['lib_niv_etu']}</option>";
            }
            ?>
        </select><br>

        <label>Date d'inscription : <input type="date" name="dte_insc" required value="<?= $mode_edition ? $inscription_a_modifier['dte_insc'] : '' ?>"></label><br>

        <label>Montant : <input type="number" name="montant_insc" required value="<?= $mode_edition ? $inscription_a_modifier['montant_insc'] : '' ?>"></label><br>

        <button type="submit"> <?= $mode_edition ? 'Modifier' : 'Ajouter' ?> </button>
        <?php if ($mode_edition): ?>
            <a href="Inscription.php">Annuler</a>
        <?php endif; ?>
    </form>

    <h3>Liste des Inscriptions</h3>
    <table border="1">
        <tr>
            <th>Étudiant</th>
            <th>Année</th>
            <th>Niveau</th>
            <th>Date</th>
            <th>Montant</th>
            <th>Action</th>
        </tr>
        <?php
        $rows = $pdo->query("SELECT i.*, e.Nom_Etu, e.Prenom_Etu, CONCAT(ac.dte_deb, '-', ac.dte_fin) AS annee, n.lib_niv_etu
                              FROM inscrire i
                              JOIN etudiant e ON i.num_etu = e.Num_Etu
                              JOIN annee_academique ac ON i.id_ac = ac.ID_AC
                              JOIN niveau_etude n ON i.id_niv_etu = n.id_niv_etu
                              ORDER BY i.dte_insc DESC")->fetchAll();
        foreach ($rows as $row) {
            echo "<tr>
                    <td>{$row['Nom_Etu']} {$row['Prenom_Etu']}</td>
                    <td>{$row['annee']}</td>
                    <td>{$row['lib_niv_etu']}</td>
                    <td>{$row['dte_insc']}</td>
                    <td>{$row['montant_insc']} FCFA</td>
                    <td>
                        <a href='?modifier={$row['id_insc']}'>Modifier</a>
                        <form method='POST' style='display:inline'>
                            <input type='hidden' name='action' value='supprimer'>
                            <input type='hidden' name='id_insc' value='{$row['id_insc']}'>
                            <button type='submit' onclick=\"return confirm('Confirmer la suppression ?')\">Supprimer</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>