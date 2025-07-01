<?php
session_start();
include('../config/connexion_BD.php');

// Statistiques
$stats = [
    'etudiants_inscrits' => $pdo->query("SELECT COUNT(*) FROM etudiant")->fetchColumn(),
    'rapports_deposes' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'déposé'")->fetchColumn(),
    'rapports_approuves' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'approuvé'")->fetchColumn(),
    'rapports_valides' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'validé'")->fetchColumn(),
    'rapports_rejetes' => $pdo->query("SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'rejeté'")->fetchColumn(),
];

// Permissions dynamiques
$id_tu = $_SESSION['id_tu'] ?? 8;

$sql = "SELECT p.code, p.description, p.categorie, p.sous_categorie
        FROM permissions p 
        JOIN role_permission rp ON p.id = rp.id_permission
        WHERE rp.id_tu = ? AND rp.lecture = 1
        ORDER BY p.categorie,p.sous_categorie, p.description";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_tu]);
$permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$menus = [];
foreach ($permissions as $perm) {
    $categorie = $perm['categorie'];
    $sousCategorie = $perm['sous_categorie'] ?: 'Autres';

    $menus[$categorie][$sousCategorie][] = $perm;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Admin</title>
    <link rel="stylesheet" href="../css/tableau_bord_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        header {
            background: #34495e;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            display: flex;
            height: calc(100vh - 60px);
            
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar details {
        margin-bottom: 10px;
        }
        .sidebar summary {
        cursor: pointer;
        font-weight: bold;
        color: #1abc9c;
        padding: 5px 0;
        }
        .sidebar h4 {
            margin-top: 20px;
            border-bottom: 1px solid #555;
            padding-bottom: 5px;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar li {
            margin: 10px 0;
            color: #fff;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            color: #1abc9c;
        }
        main #main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f4f4f4;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">LOGO</div>
        <div class="search-container"><button class="search-btn">RECHERCHER</button></div>
        <div class="user-profile">NOM UTILISATEUR</div>
    </header>

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php foreach ($menus as $categorie => $groupes): ?>
        <div class="menu-section">
            <h4><?= htmlspecialchars($categorie) ?></h4>
            <?php foreach ($groupes as $nomGroupe => $actions): ?>
                <details>
                    <summary><?= htmlspecialchars($nomGroupe) ?></summary>
                    <ul>
                        <?php foreach ($actions as $action): ?>
                            <li>
                                <a href="#" class="sidebar-link" data-page="<?= htmlspecialchars($action['code']) ?>.php">
                                    <?= htmlspecialchars($action['description']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </details>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
        </aside>


        <!-- Contenu dynamique -->
        <main id="main-content">
                <h1 class="page-title">Tableau de Bord Administrateur</h1>
                <div class="dashboard-grid">
                    <div class="metric-card"><div class="metric-icon">👥</div><div class="metric-number"><?= $stats['etudiants_inscrits'] ?></div><div class="metric-label">Étudiants inscrits</div></div>
                    <div class="metric-card"><div class="metric-icon">📄</div><div class="metric-number"><?= $stats['rapports_deposes'] ?></div><div class="metric-label">Rapports déposés</div></div>
                    <div class="metric-card"><div class="metric-icon">✅</div><div class="metric-number"><?= $stats['rapports_approuves'] ?></div><div class="metric-label">Rapports approuvés</div></div>
                    <div class="metric-card"><div class="metric-icon">📋</div><div class="metric-number"><?= $stats['rapports_valides'] ?></div><div class="metric-label">Rapports validés</div></div>
                    <div class="metric-card"><div class="metric-icon">❌</div><div class="metric-number"><?= $stats['rapports_rejetes'] ?></div><div class="metric-label">Rapports rejetés</div></div>
                </div>

                <!-- Section des graphiques -->
            <div class="charts-section">
                <div class="chart-container">
                    <h3 class="chart-title">Évolution des dépôts de rapports</h3>
                    <canvas id="barChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Répartition par spécialité</h3>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <!-- Section historique des activités -->
            <div class="activity-section">
                <div class="activity-header">
                    <h3 class="activity-title">Historique des activités récentes</h3>
                    <button class="download-btn" title="Télécharger le rapport">⬇</button>
                </div>
                <div class="activity-content">
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-line"></div>
                        <span>Validation de 8 rapports de stage aujourd'hui</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-line"></div>
                        <span>Planification de 5 soutenances pour la semaine prochaine</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-line"></div>
                        <span>Mise à jour des critères d'évaluation</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-line"></div>
                        <span>Traitement de 4 réclamations étudiantes</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.sidebar-link');
        const mainContent = document.getElementById('main-content');

        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');

                fetch(page)
                    .then(response => {
                        if (!response.ok) throw new Error('Page non trouvée');
                        return response.text();
                    })
                    .then(html => {
                        mainContent.innerHTML = html;
                        history.pushState(null, '', '?page=' + page);
                    })
                    .catch(error => {
                        mainContent.innerHTML = "<p style='color:red'>Erreur lors du chargement de la page.</p>";
                    });
            });
        });

        // Charger une page par défaut si définie dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');
        if (page) {
            fetch(page)
                .then(res => res.text())
                .then(html => document.getElementById('main-content').innerHTML = html)
                .catch(err => console.error("Erreur de chargement :", err));
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    function loadPage(page) {
    fetch(page, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('main-content').innerHTML = html;
        history.pushState(null, '', '?page=' + page);
    })
    .catch(() => {
        document.getElementById('main-content').innerHTML = "<p style='color:red'>Erreur lors du chargement de la page.</p>";
    });
}


    // Exemple : charger la page étudiants au démarrage
    loadPage('Etudiant.php');

    // Exemple : si tu as un menu avec liens <a data-page="etudiants_content.php">, tu peux les gérer ici :
    document.querySelectorAll('a[data-page]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            loadPage(page);
            history.pushState(null, '', '?page=' + page);
        });
    });

    // Gestion du bouton retour navigateur
    window.addEventListener('popstate', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page') || 'etudiants_content.php';
        loadPage(page);
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("studentForm");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // Empêche la redirection

            const formData = new FormData(form);

            fetch("Etudiant.php", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest" // important pour signaler que c'est une requête AJAX
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("main-content").innerHTML = html; // Recharge uniquement le contenu du main
            })
            .catch(error => {
                alert("Une erreur s’est produite : " + error.message);
            });
        });
    }
});


    </script>
</body>
</html>
