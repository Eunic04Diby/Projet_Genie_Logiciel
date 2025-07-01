<?php
include('../config/connexion_BD.php');

$success = '';
$error = '';
$mode_edition = false;
$annee_a_modifier = null;

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $date_debut = $_POST['dte_deb'] ?? '';
    $date_fin = $_POST['dte_fin'] ?? '';
    $id_ac = $_POST['id_ac'] ?? null;

    if (empty($date_debut) || empty($date_fin)) {
        $error = "Les deux dates sont obligatoires.";
    } elseif ($date_fin <= $date_debut) {
        $error = "La date de fin doit être postérieure à la date de début.";
    } else {
        try {
            if ($action === 'ajouter') {
                $stmt = $pdo->prepare("INSERT INTO annee_academique (dte_deb, dte_fin) VALUES (?, ?)");
                $stmt->execute([$date_debut, $date_fin]);
                $success = "Année académique ajoutée.";
            } elseif ($action === 'modifier' && $id_ac) {
                $stmt = $pdo->prepare("UPDATE annee_academique SET dte_deb = ?, dte_fin = ? WHERE id_ac = ?");
                $stmt->execute([$date_debut, $date_fin, $id_ac]);
                $success = "Année académique modifiée.";
            } elseif ($action === 'supprimer' && $id_ac) {
                $stmt = $pdo->prepare("DELETE FROM annee_academique WHERE id_ac= ?");
                $stmt->execute([$id_ac]);
                $success = "Année supprimée.";
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}

// Mode édition
if (isset($_GET['modifier'])) {
    $id_ac = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM annee_academique WHERE id_ac = ?");
    $stmt->execute([$id_ac]);
    $annee_a_modifier = $stmt->fetch();
    $mode_edition = !!$annee_a_modifier;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Années Académiques</title>
    <link rel="stylesheet" href="../css/maj_UE.css">
    <style>
        .modal, .success-message, .error-message { display: none; }
        .modal.active { display: flex; align-items: center; justify-content: center; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 400px; text-align: center; }
        .modal-content h3 { margin-bottom: 10px; }
        .success-message, .error-message {
            padding: 10px; margin: 10px 0; border-radius: 4px;
        }
        .success-message { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; display: block; }
        .error-message { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; display: block; }
        .search-container input {
            padding: 8px; width: 300px; margin: 10px auto; display: block;
            border: 1px solid #ccc; border-radius: 4px;
        }
    </style>
</head>
<body>
<main>
    <h1 class="page-title"><?= $mode_edition ? "Modifier l'Année Académique" : "Ajouter une Année Académique" ?></h1>

    <?php if ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" onsubmit="return confirmerAction()">
            <input type="hidden" name="action" value="<?= $mode_edition ? 'modifier' : 'ajouter' ?>">
            <?php if ($mode_edition): ?>
                <input type="hidden" name="id_ac" value="<?= htmlspecialchars($annee_a_modifier['id_ac']) ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="dte_deb">Date de début *</label>
                    <input type="date" name="dte_deb" required value="<?= $mode_edition ? htmlspecialchars($annee_a_modifier['dte_deb']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="dte_fin">Date de fin *</label>
                    <input type="date" name="dte_fin" required value="<?= $mode_edition ? htmlspecialchars($annee_a_modifier['dte_fin']) : '' ?>">
                </div>
            </div>

            <div style="text-align: right;">
                <button class="validate-btn" type="submit"><?= $mode_edition ? 'Modifier' : 'Valider' ?></button>
                <?php if ($mode_edition): ?>
                    <a href="MAJ_Annee_Academique.php" class="validate-btn" style="background: gray;">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="🔍 Rechercher une année..." oninput="filtrer()">
    </div>

    <div class="table-container">
        <div class="table-header">Liste des Années Académiques</div>
        <table class="data-table" id="dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM annee_academique ORDER BY dte_deb DESC");
            $rows = $stmt->fetchAll();
            if (!$rows) {
                echo '<tr><td colspan="5" class="empty-state">Aucune année enregistrée.</td></tr>';
            } else {
                foreach ($rows as $row) {
                    $libelle = date('Y', strtotime($row['dte_deb'])) . ' - ' . date('Y', strtotime($row['dte_fin']));
                    echo '<tr>
                        <td>' . $row['id_ac'] . '</td>
                        <td>' . htmlspecialchars($row['dte_deb']) . '</td>
                        <td>' . htmlspecialchars($row['dte_fin']) . '</td>
                        <td class="action-buttons">
                            <a href="?modifier=' . $row['id_ac'] . '" class="btn-edit">Modifier</a>
                            <button type="button" class="btn-delete" onclick="confirmerSuppression(\'' . $row['id_ac'] . '\', \'' . $libelle . '\')">Supprimer</button>
                        </td>
                    </tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Suppression -->
    <div class="modal" id="modalSuppression">
        <div class="modal-content">
            <h3>Confirmer la suppression</h3>
            <p>Voulez-vous supprimer l’année <strong><span id="anneeLibelle"></span></strong> ?</p>
            <div style="margin-top: 15px;">
                <form method="POST" id="formSuppression">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="id_ac" id="id_ac_modal">
                    <button type="submit" class="btn-confirm" style="background: #d9534f; color: white;">Oui, supprimer</button>
                    <button type="button" onclick="fermerModal()" class="btn-cancel">Annuler</button>
                </form>
            </div>
        </div>
    </div>

</main>

<script>
function confirmerSuppression(id, libelle) {
    document.getElementById('id_ac_modal').value = id;
    document.getElementById('anneeLibelle').textContent = libelle;
    document.getElementById('modalSuppression').classList.add('active');
}

function fermerModal() {
    document.getElementById('modalSuppression').classList.remove('active');
}

function confirmerAction() {
    const form = event.target;
    const action = form.querySelector('[name=action]').value;
    const debut = form.querySelector('[name=dte_deb]').value;
    const fin = form.querySelector('[name=dte_fin]').value;
    return confirm(`Confirmer ${action === 'ajouter' ? 'l\'ajout' : 'la modification'} de l’année académique du ${debut} au ${fin} ?`);
}

function filtrer() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
</body>
</html>
