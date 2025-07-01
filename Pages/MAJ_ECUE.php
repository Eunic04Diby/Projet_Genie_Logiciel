<?php
include('../config/connexion_BD.php');

$success_message = '';
$error_message = '';

// Récupération des données nécessaires
try {
    // Niveaux d'étude
    $stmt = $pdo->query("SELECT id_niv_etu, lib_niv_etu FROM niveau_etude ORDER BY id_niv_etu");
    $niveaux_etude = $stmt->fetchAll();

    // UEs avec semestre, niveau et année académique
    $stmtUE = $pdo->query("
        SELECT ue.id_ue, ue.id_semes, ue.id_niv_etu, ue.id_ac,
               CONCAT(a.dte_deb, ' - ', a.dte_fin) AS libelle_annee
        FROM ue
        JOIN annee_academique a ON ue.id_ac = a.id_ac
        ORDER BY ue.id_ue ASC
    ");
    $ues = $stmtUE->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur chargement données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider'])) {
    $id_ue = $_POST['id_ue'] ?? '';
    $id_ecue = strtoupper(trim($_POST['id_ecue'] ?? ''));
    $credit = $_POST['credit'] ?? '';

    if (empty($id_ue) || empty($id_ecue) || empty($credit)) {
        $error_message = "Tous les champs sont obligatoires.";
    } elseif (!is_numeric($credit) || $credit < 1 || $credit > 30) {
        $error_message = "Le crédit doit être un nombre entre 1 et 30.";
    } else {
        try {
            // Vérifier que l’UE existe
            $stmt = $pdo->prepare("SELECT * FROM ue WHERE id_ue = ?");
            $stmt->execute([$id_ue]);
            $ue_data = $stmt->fetch();

            if (!$ue_data) {
                $error_message = "L'UE sélectionnée n'existe pas.";
            } else {
                // Vérifier doublon ECUE
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM ecue WHERE id_ecue = ?");
                $stmt->execute([$id_ecue]);

                if ($stmt->fetchColumn() > 0) {
                    $error_message = "Cet ECUE existe déjà.";
                } else {
                    // Insertion
                    $stmt = $pdo->prepare("INSERT INTO ecue (id_ue, id_ecue, credit_ecue) VALUES (?, ?, ?)");
                    $stmt->execute([$id_ue, $id_ecue, $credit]);
                    $success_message = "ECUE ajouté avec succès.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erreur SQL : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout ECUE</title>
    <style>
        :root {
            --primary-green: #4CAF50;
            --danger-red: #e74c3c;
            --background-white: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 2rem;
        }

        h2 {
            color: var(--primary-green);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-container {
            background: var(--background-white);
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-group select,
        .form-group input {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input[readonly] {
            background: #f0f0f0;
        }

        .form-button {
            grid-column: 1 / -1;
            text-align: center;
        }

        .form-button button {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .form-button button:hover {
            background: #388e3c;
        }

        .message {
            text-align: center;
            font-weight: bold;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0 auto;
            max-width: 1000px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: center;
        }

        th {
            background: #e0f2f1;
            color: var(--text-dark);
        }
    </style>
</head>
<body>

<h2>MISE À JOUR DES ECUE</h2>

<?php if (!empty($success_message)): ?>
    <div class="message success-message"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="message error-message"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<form method="POST" action="MAJ_ECUE.php">
    <div class="form-container">
        <!-- UE -->
        <div class="form-group">
            <label for="id_ue">UE (avec année)</label>
            <select name="id_ue" id="id_ue" onchange="remplirInfosUE()" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($ues as $ue): ?>
                <option 
                    value="<?= $ue['id_ue'] ?>"
                    data-semestre="<?= $ue['id_semes'] ?>"
                    data-niv="<?= $ue['id_niv_etu'] ?>"
                    data-annee="<?= $ue['libelle_annee'] ?>"
                >
                    <?= $ue['id_ue'] ?>
                </option>
            <?php endforeach; ?>
            </select>

        </div>

        <!-- Semestre -->
        <div class="form-group">
            <label for="semestre">Semestre</label>
            <input type="text" name="semestre" id="semestre" readonly>
        </div>

        <!-- Niveau -->
        <div class="form-group">
            <label>Niveau</label>
            <span id="niveau_affiche" style="padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">---</span>
            <input type="hidden" name="id_niv_etu" id="id_niv_etu">
        </div>

        <!-- Année académique -->
        <div class="form-group">
            <label>Année académique</label>
            <input type="text" name="annee_ac" id="annee_ac" readonly>
        </div>


        <!-- ECUE -->
        <div class="form-group">
            <label for="id_ecue">ECUE</label>
            <input type="text" name="id_ecue" id="id_ecue" required>
        </div>

        <!-- Crédit -->
        <div class="form-group">
            <label for="credit">Crédit</label>
            <input type="number" name="credit" id="credit" min="1" max="30" required>
        </div>

        <div class="form-button">
            <button type="submit" name="valider">VALIDER</button>
        </div>
    </div>
</form>

<!-- Liste ECUE -->
<table>
    <thead>
        <tr>
            <th>Année</th>
            <th>UE</th>
            <th>Semestre</th>
            <th>Niveau</th>
            <th>ECUE</th>
            <th>Crédit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("
            SELECT 
                e.id_ecue, e.credit_ecue, e.id_ue,
                ue.id_semes, ue.id_niv_etu,
                n.lib_niv_etu,
                CONCAT(a.dte_deb, ' - ', a.dte_fin) AS annee_lib
            FROM ecue e
            JOIN ue ON ue.id_ue = e.id_ue
            JOIN niveau_etude n ON n.id_niv_etu = ue.id_niv_etu
            JOIN annee_academique a ON a.id_ac = ue.id_ac
            ORDER BY e.id_ecue DESC
        ");
        while ($ecue = $stmt->fetch()): ?>
            <tr>
                <td><?= htmlspecialchars($ecue['annee_lib']) ?></td>
                <td><?= htmlspecialchars($ecue['id_ue']) ?></td>
                <td><?= htmlspecialchars($ecue['id_semes']) ?></td>
                <td><?= htmlspecialchars($ecue['lib_niv_etu']) ?></td>
                <td><?= htmlspecialchars($ecue['id_ecue']) ?></td>
                <td><?= htmlspecialchars($ecue['credit_ecue']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    const niveauxLib = {
        <?php foreach ($niveaux_etude as $niveau): ?>
        "<?= $niveau['id_niv_etu'] ?>": "<?= $niveau['lib_niv_etu'] ?>",
        <?php endforeach; ?>
    };

    function remplirInfosUE() {
        const select = document.getElementById('id_ue');
        const option = select.options[select.selectedIndex];

        const semestre = option.dataset.semestre || '';
        const idNiv = option.dataset.niv || '';
        const annee = option.dataset.annee || '';

        document.getElementById('semestre').value = semestre;
        document.getElementById('id_niv_etu').value = idNiv;
        document.getElementById('niveau_affiche').textContent = niveauxLib[idNiv] || '---';
        document.getElementById('annee_ac').value = annee;
    }
</script>


</body>
</html>
