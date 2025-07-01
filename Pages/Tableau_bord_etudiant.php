<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Étudiant</title>
  <style>
    /* Reset & global */
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary-blue: #2563eb;
      --primary-green: #10b981;
      --primary-purple: #8b5cf6;
      --primary-orange: #f59e0b;
      --danger-red: #ef4444;
      --warning-yellow: #f59e0b;
      --success-green: #10b981;
      --background-light: #f8fafc;
      --background-white: #ffffff;
      --text-dark: #1e293b;
      --text-gray: #64748b;
      --border-light: #e2e8f0;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      --sidebar-width: 250px;
    }

    body {
      font-family: 'Inter', sans-serif, Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: var(--text-dark);
      display: flex;
      flex-direction: column;
    }

    /* Header */
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 70px;
      background-color: #2d3e50;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-green);
      border: 2px solid var(--primary-green);
      padding: 0.3rem 1rem;
      border-radius: 8px;
      user-select: none;
      background-color: transparent;
      cursor: default;
    }

    /* Search - remplacement du bouton par champ de recherche */
    .search-container {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      max-width: 400px;
      margin: 0 2rem;
    }

    .search-container input[type="search"] {
      width: 100%;
      padding: 0.5rem 1rem;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      outline-offset: 2px;
      outline-color: var(--primary-green);
      transition: box-shadow 0.3s ease;
    }

    .search-container input[type="search"]:focus {
      box-shadow: 0 0 8px var(--primary-green);
    }

    /* User profile */
    .user-profile {
      font-size: 1rem;
      font-weight: 500;
      color: #cbd5e1;
      user-select: none;
    }

    /* Layout */
    .main-layout {
      display: flex;
      margin-top: 70px; /* sous header */
      flex-grow: 1;
      min-height: calc(100vh - 70px);
      background: var(--background-light);
    }

    /* Sidebar */
    .sidebar {
      width: var(--sidebar-width);
      background-color: #2d3e50;
      color: var(--primary-green);
      padding: 1.5rem 1rem;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      overflow-y: auto;
      position: sticky;
      top: 70px;
    }

    .sidebar .menu-section h4 {
      font-size: 1rem;
      text-transform: uppercase;
      margin-bottom: 1rem;
      color: #cbd5e1;
      border-bottom: 1px solid #334155;
      padding-bottom: 0.5rem;
      letter-spacing: 1px;
    }

    .sidebar details {
      margin-bottom: 1.2rem;
      border-left: 3px solid transparent;
      border-radius: 4px;
      transition: all 0.2s ease;
    }

    .sidebar details[open] {
      border-left-color: var(--primary-green);
      background-color: #1e293b;
      box-shadow: 0 0 0 1px var(--primary-green);
    }

    .sidebar summary {
      list-style: none;
      cursor: pointer;
      padding: 0.6rem 0.8rem;
      font-weight: 600;
      color: var(--primary-green);
      position: relative;
      user-select: none;
      outline-offset: 4px;
    }

    .sidebar summary:hover,
    .sidebar summary:focus {
      color: var(--background-white);
      outline: 2px solid var(--primary-green);
      outline-offset: 2px;
    }

    .sidebar summary::before {
      content: "►";
      display: inline-block;
      margin-right: 8px;
      transform: rotate(0deg);
      transition: transform 0.2s ease;
    }

    .sidebar details[open] summary::before {
      transform: rotate(90deg);
    }

    .sidebar ul {
      list-style: none;
      padding-left: 1.5rem;
      margin-top: 0.4rem;
    }

    .sidebar-link {
      display: block;
      color: #cbd5e1;
      text-decoration: none;
      padding: 0.4rem 0.8rem;
      margin-bottom: 0.25rem;
      border-radius: 4px;
      font-size: 0.95rem;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sidebar-link:hover,
    .sidebar-link:focus {
      background-color: var(--primary-green);
      color: var(--background-white);
      outline: none;
    }

    /* Dashboard container */
    .dashboard-container {
      flex: 1;
      padding: 2rem 2.5rem;
      overflow-y: auto;
    }

    /* Header section */
    .header {
      background: var(--background-white);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-lg);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .user-info h1 {
      font-size: 2rem;
      color: var(--text-dark);
      margin-bottom: 0.5rem;
      user-select: none;
    }

    .user-details {
      color: var(--text-gray);
      font-size: 1.1rem;
      user-select: none;
    }

    .header-actions {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .notification-bell {
      position: relative;
      background: var(--primary-blue);
      color: white;
      border: none;
      padding: 1rem;
      border-radius: 50%;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      font-size: 1.25rem;
      user-select: none;
    }

    .notification-bell:hover,
    .notification-bell:focus {
      transform: scale(1.1);
      box-shadow: var(--shadow-lg);
      outline: none;
    }

    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: var(--danger-red);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      user-select: none;
    }

    /* Stat Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .stat-card {
      background: var(--background-white);
      padding: 1.5rem;
      border-radius: 1rem;
      box-shadow: var(--shadow-lg);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      cursor: default;
      user-select: none;
    }

    .stat-card:hover,
    .stat-card:focus-within {
      transform: translateY(-4px);
      box-shadow: 0 12px 20px -4px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-dark);
    }

    .stat-label {
      color: var(--text-gray);
      font-size: 1rem;
      margin-top: 0.25rem;
    }

    .stat-icon {
      font-size: 2.2rem;
      padding: 1rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      box-shadow: var(--shadow);
      user-select: none;
    }

    /* Icon colors */
    .stat-icon.blue {
      background-color: var(--primary-blue);
    }

    .stat-icon.green {
      background-color: var(--primary-green);
    }

    .stat-icon.purple {
      background-color: var(--primary-purple);
    }

    /* Quick actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .action-btn {
      background-color: var(--background-white);
      padding: 1.2rem;
      border-radius: 1rem;
      text-align: center;
      text-decoration: none;
      color: var(--text-dark);
      font-weight: 600;
      box-shadow: var(--shadow);
      transition: background-color 0.2s ease, transform 0.2s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      user-select: none;
    }

    .action-btn:hover,
    .action-btn:focus {
      background-color: var(--primary-green);
      color: white;
      transform: scale(1.05);
      outline: none;
    }

    .action-icon {
      font-size: 2rem;
      margin-bottom: 0.5rem;
      user-select: none;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .main-layout {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        min-height: auto;
        position: relative;
        top: 0;
        padding: 1rem 1.5rem;
        order: 2;
      }
      .dashboard-container {
        padding: 1.5rem 1rem;
        order: 1;
      }
      .search-container {
        margin: 0 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Barre du haut -->
  <header>
    <div class="logo" aria-label="Logo de la plateforme">LOGO</div>
    <div class="search-container">
      <input type="search" placeholder="Rechercher..." aria-label="Recherche" />
    </div>
    <div class="user-profile" aria-label="Profil utilisateur">Jean KOUASSI</div>
  </header>

  <!-- Contenu principal -->
  <div class="main-layout" role="main">
    <!-- Barre latérale -->
    <aside class="sidebar" role="navigation" aria-label="Menu de navigation principal">
      <div class="menu-section">
        <h4>Navigation</h4>
        <details>
          <summary tabindex="0">Autres</summary>
          <ul>
            <li><a href="AC25.php" class="sidebar-link" tabindex="0">Réception des comptes rendus</a></li>
            <li><a href="AC27.php" class="sidebar-link" tabindex="0">Réclamation</a></li>
          </ul>
        </details>
        <details>
          <summary tabindex="0">Gestion des étudiants</summary>
          <ul>
            <li><a href="deposer rapport.php" class="sidebar-link" tabindex="0">Dépôt de rapport</a></li>
          </ul>
        </details>
      </div>
    </aside>

    <!-- Contenu du tableau de bord -->
    <section class="dashboard-container" aria-label="Tableau de bord étudiant">
      <div class="header">
        <div class="user-info">
          <h1>Bonjour, Jean KOUASSI ! 👋</h1>
          <div class="user-details" aria-live="polite">
            Licence 3 - Informatique | Semestre 5 | 2024-2025
          </div>
        </div>
        <div class="header-actions">
          <button class="notification-bell" aria-label="Notifications, 3 non lues" aria-haspopup="true">
            🔔
            <span class="notification-badge" aria-hidden="true">3</span>
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid" role="list" aria-label="Statistiques principales">
        <article class="stat-card" role="listitem" tabindex="0" aria-label="Moyenne Générale: 15.2">
          <div class="stat-header">
            <div>
              <div class="stat-value">15.2</div>
              <div class="stat-label">Moyenne Générale</div>
            </div>
            <div class="stat-icon blue" aria-hidden="true">📊</div>
          </div>
        </article>

        <article class="stat-card" role="listitem" tabindex="0" aria-label="Crédits Acquis: 127">
          <div class="stat-header">
            <div>
              <div class="stat-value">127</div>
              <div class="stat-label">Crédits Acquis</div>
            </div>
            <div class="stat-icon green" aria-hidden="true">⭐</div>
          </div>
        </article>

        <article class="stat-card" role="listitem" tabindex="0" aria-label="ECUE Validées: 8 sur 12">
          <div class="stat-header">
            <div>
              <div class="stat-value">8/12</div>
              <div class="stat-label">ECUE Validées</div>
            </div>
            <div class="stat-icon purple" aria-hidden="true">📚</div>
          </div>
        </article>
      </div>

      <!-- Quick Actions -->
      <nav class="quick-actions" aria-label="Actions rapides">
        <a href="#" class="action-btn" tabindex="0" aria-label="Accéder à mes notes">
          <span class="action-icon" aria-hidden="true">📝</span>
          <div>Mes Notes</div>
        </a>
        <a href="#" class="action-btn" tabindex="0" aria-label="Accéder aux paiements">
          <span class="action-icon" aria-hidden="true">💳</span>
          <div>Paiements</div>
        </a>
        <a href="#" class="action-btn" tabindex="0" aria-label="Accéder aux documents">
          <span class="action-icon" aria-hidden="true">📄</span>
          <div>Documents</div>
        </a>
        <a href="#" class="action-btn" tabindex="0" aria-label="Accéder aux résultats">
          <span class="action-icon" aria-hidden="true">🎓</span>
          <div>Résultats</div>
        </a>
      </nav>
    </section>
  </div>

</body>
</html>
