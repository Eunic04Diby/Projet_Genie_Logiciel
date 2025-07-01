<?php
// Connexion à la BD
include('../config/connexion_BD.php');

$statistiques = [
    'rapports_deposes' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'déposé'")->fetchColumn(),
    'rapports_valides' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'validé'")->fetchColumn(),
    'rapports_refuses' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'rejeté'")->fetchColumn(),
    'soutenances' => $pdo->query("SELECT COUNT(*) FROM soutenance WHERE etat_sout = 'planifié'")->fetchColumn()
];

// Récupérer les soutenances pour le tableau
$sqlSoutenances = "
    SELECT 
    s.id_sout,
    e.Nom_Etud,
    e.Prenom_Etud,
    r.theme_mem AS sujet, 
    s.etat_sout,
    s.note_sout,
    s.date_sout,
    s.heure_sout
FROM soutenance s
JOIN etudiant e ON s.Num_Etud = e.Num_Etud
JOIN rapport_etudiant r ON s.id_rapport = r.id_rapport

";
$soutenances = $pdo->query($sqlSoutenances)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation des Statistiques</title>
         <link rel="stylesheet" href="../css/statistique.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

</head>
<body>

        <div class="page-header">
            <h1 class="page-title">Consultation des Statistiques</h1>
            <div class="action-buttons">
                <button class="export-btn">Exporter</button>
                <button class="edit-btn">Éditer</button>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?=$statistiques['rapports_deposes']?></div>
                <div class="stat-label">Rapports déposés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?=$statistiques['rapports_valides']?></div>
                <div class="stat-label">Rapports validés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?=$statistiques['rapports_refuses']?></div>
                <div class="stat-label">Rapports refusés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?=$statistiques['soutenances']?></div>
                <div class="stat-label">Soutenances planifiées</div>
            </div>
        </div>

        <!-- Section des graphiques -->
        <div class="charts-section">
            <div class="chart-container">
                <h3 class="chart-title">Répartition des statuts de rapport</h3>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Nombre de soutenances par mois</h3>
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- Tableau des soutenances -->
        <div class="table-section">
            <div class="table-header">Liste des soutenances</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Sujet</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Heure</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soutenances as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Nom_Etud'] . ' ' . $row['Prenom_Etud']) ?></td>
                            <td><?= htmlspecialchars($row['sujet']) ?></td>
                            <td><?= htmlspecialchars($row['etat_sout']) ?></td>
                            <td><?= htmlspecialchars($row['date_sout']) ?></td>
                            <td><?= htmlspecialchars($row['heure_sout']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

<script src="../js/statistique.js"></script>
</body>
</html> 