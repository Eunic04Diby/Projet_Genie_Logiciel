<?php 
include('../config/connexion_BD.php');

// Initialisation des variables
$success_message = '';
$error_message = '';
$mode_edition = false;
$ue_a_modifier = null;

// Récupération des données des tables de référence
$annees_academiques = [];
$niveaux_etude = [];
$semestres = [];

try {
    // Récupérer les années académiques
    $stmt = $pdo->query("SELECT id_ac, CONCAT(dte_deb, ' - ', dte_fin) as libelle_annee FROM annee_academique ORDER BY dte_deb DESC");
    $annees_academiques = $stmt->fetchAll();
    
    // Récupérer les niveaux d'étude
    $stmt = $pdo->query("SELECT id_niv_etu, lib_niv_etu FROM niveau_etude ORDER BY id_niv_etu");
    $niveaux_etude = $stmt->fetchAll();

    // Récupérer les semestres
    $stmt = $pdo->query("SELECT id_semes, lib_semes, code_semes FROM semestre ORDER BY id_semes");
    $semestres = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "Erreur lors du chargement des données de référence : " . $e->getMessage();
}

// Fonction de validation des données
function validerDonnees($data) {
    $erreurs = [];
    
    // Validation des champs obligatoires
    $champs_requis = ['id_ac', 'id_niv_etu', 'id_semes', 'id_UE', 'lib_UE', 'credit_UE'];
    foreach ($champs_requis as $champ) {
        if (empty($data[$champ])) {
            $erreurs[] = "Le champ " . ucfirst(str_replace('_', ' ', $champ)) . " est obligatoire.";
        }
    }
    
    // Validation spécifique de l'ID UE
    if (!empty($data['id_UE'])) {
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $data['id_UE'])) {
            $erreurs[] = "L'ID UE doit contenir entre 3 et 10 caractères alphanumériques en majuscules.";
        }
    }
    
    // Validation du libellé
    if (!empty($data['lib_UE'])) {
        if (strlen($data['lib_UE']) > 100) {
            $erreurs[] = "Le libellé ne peut pas dépasser 100 caractères.";
        }
    }
    
    // Validation des crédits
    if (!empty($data['credit_UE'])) {
        if (!is_numeric($data['credit_UE']) || $data['credit_UE'] < 1 || $data['credit_UE'] > 30) {
            $erreurs[] = "Le nombre de crédits doit être un nombre entre 1 et 30.";
        }
    }
    

    
    return $erreurs;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'ajouter':
            $donnees = [
                'id_ac' => trim($_POST['id_ac'] ?? ''),
                'id_niv_etu' => trim($_POST['id_niv_etu'] ?? ''),
                'id_semes' => trim($_POST['id_semes'] ?? ''),
                'id_UE' => trim(strtoupper($_POST['id_UE'] ?? '')),
                'lib_UE' => trim($_POST['lib_UE'] ?? ''),
                'credit_UE' => trim($_POST['credit_UE'] ?? '')
            ];

            $erreurs = validerDonnees($donnees);
            
            if (empty($erreurs)) {
                try {
                    // Vérifier si l'ID UE existe déjà
                    $checkUE = $pdo->prepare("SELECT COUNT(*) FROM ue WHERE id_UE = ?");
                    $checkUE->execute([$donnees['id_UE']]);
                    if ($checkUE->fetchColumn() > 0) {
                        throw new Exception("Cet ID UE existe déjà !");
                    }

                    // Vérifier que les clés étrangères existent
                    $checkAC = $pdo->prepare("SELECT COUNT(*) FROM annee_academique WHERE id_ac = ?");
                    $checkAC->execute([$donnees['id_ac']]);
                    if ($checkAC->fetchColumn() == 0) {
                        throw new Exception("L'année académique sélectionnée n'existe pas !");
                    }

                    $checkNiv = $pdo->prepare("SELECT COUNT(*) FROM niveau_etude WHERE id_niv_etu = ?");
                    $checkNiv->execute([$donnees['id_niv_etu']]);
                    if ($checkNiv->fetchColumn() == 0) {
                        throw new Exception("Le niveau d'étude sélectionné n'existe pas !");
                    }

                    $checkSem = $pdo->prepare("SELECT COUNT(*) FROM semestre WHERE id_semes = ?");
                    $checkSem->execute([$donnees['id_semes']]);
                    if ($checkSem->fetchColumn() == 0) {
                        throw new Exception("Le semestre sélectionné n'existe pas !");
                    }

                    // Insérer la nouvelle UE
                    $stmt = $pdo->prepare("INSERT INTO ue (id_ac, id_niv_etu, id_semes, id_UE, lib_UE, credit_UE) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$donnees['id_ac'], $donnees['id_niv_etu'], $donnees['id_semes'], $donnees['id_UE'], $donnees['lib_UE'], $donnees['credit_UE']]);

                    $success_message = "UE ajoutée avec succès !";
                } catch (Exception $e) {
                    $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error_message = implode('<br>', $erreurs);
            }
            break;

        case 'modifier':
            $id_UE_original = trim($_POST['id_UE_original'] ?? '');
            $donnees = [
                'id_ac' => trim($_POST['id_ac'] ?? ''),
                'id_niv_etu' => trim($_POST['id_niv_etu'] ?? ''),
                'semestre' => trim($_POST['id_semes'] ?? ''),
                'id_UE' => trim(strtoupper($_POST['id_UE'] ?? '')),
                'lib_UE' => trim($_POST['lib_UE'] ?? ''),
                'credit_UE' => trim($_POST['credit_UE'] ?? '')
            ];
            
            if (empty($id_UE_original)) {
                $error_message = "ID UE original manquant.";
                break;
            }

            $erreurs = validerDonnees($donnees);
            
            if (empty($erreurs)) {
                try {
                    // Vérifier si le nouvel ID UE existe déjà (sauf pour l'UE actuelle)
                    if ($donnees['id_UE'] !== $id_UE_original) {
                        $checkUE = $pdo->prepare("SELECT COUNT(*) FROM ue WHERE id_UE = ?");
                        $checkUE->execute([$donnees['id_UE']]);
                        if ($checkUE->fetchColumn() > 0) {
                            throw new Exception("Cet ID UE existe déjà !");
                        }
                    }

                    // Vérifier que les clés étrangères existent
                    $checkAC = $pdo->prepare("SELECT COUNT(*) FROM annee_academique WHERE id_ac = ?");
                    $checkAC->execute([$donnees['id_ac']]);
                    if ($checkAC->fetchColumn() == 0) {
                        throw new Exception("L'année académique sélectionnée n'existe pas !");
                    }

                    $checkNiv = $pdo->prepare("SELECT COUNT(*) FROM niveau_etude WHERE id_niv_etu = ?");
                    $checkNiv->execute([$donnees['id_niv_etud']]);
                    if ($checkNiv->fetchColumn() == 0) {
                        throw new Exception("Le niveau d'étude sélectionné n'existe pas !");
                    }

                    $checkSem = $pdo->prepare("SELECT COUNT(*) FROM semestre WHERE id_semes = ?");
                    $checkSem->execute([$donnees['id_semes']]);
                    if ($checkSem->fetchColumn() == 0) {
                        throw new Exception("Le semestre sélectionné n'existe pas !");
                    }

                    // Mettre à jour l'UE
                    $stmt = $pdo->prepare("UPDATE ue SET id_ac = ?, id_niv_etu = ?, id_semes = ?, id_UE = ?, lib_UE = ?, credit_UE = ? WHERE id_UE = ?");
                    $stmt->execute([$donnees['id_ac'], $donnees['id_niv_etu'], $donnees['id_semes'], $donnees['id_UE'], $donnees['lib_UE'], $donnees['credit_UE'], $id_UE_original]);

                    $success_message = "UE modifiée avec succès !";
                } catch (Exception $e) {
                    $error_message = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error_message = implode('<br>', $erreurs);
            }
            break;

        case 'supprimer':
            $id_UE = trim($_POST['id_UE'] ?? '');

            if (!empty($id_UE)) {
                try {
                    // Vérifier si l'UE existe
                    $checkUE = $pdo->prepare("SELECT COUNT(*) FROM ue WHERE id_UE = ?");
                    $checkUE->execute([$id_UE]);
                    if ($checkUE->fetchColumn() == 0) {
                        throw new Exception("Cette UE n'existe pas !");
                    }

                    // Vérifier si l'UE est utilisée dans d'autres tables (exemple avec une table notes)
                    // Décommentez et adaptez selon votre structure de base de données
                    /*
                    $checkUsage = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE id_UE = ?");
                    $checkUsage->execute([$id_UE]);
                    if ($checkUsage->fetchColumn() > 0) {
                        throw new Exception("Impossible de supprimer cette UE car elle est utilisée dans d'autres enregistrements !");
                    }
                    */
                    
                    $stmt = $pdo->prepare("DELETE FROM ue WHERE id_UE = ?");
                    $stmt->execute([$id_UE]);
                    
                    if ($stmt->rowCount() > 0) {
                        $success_message = "UE supprimée avec succès !";
                    } else {
                        $error_message = "Aucune UE n'a été supprimée.";
                    }
                } catch (Exception $e) {
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
            } else {
                $error_message = "ID UE manquant pour la suppression.";
            }
            break;

        default:
            $error_message = "Action non reconnue.";
            break;
    }
}

// Gestion du mode édition
if (isset($_GET['modifier']) && !empty($_GET['modifier'])) {
    $id_UE_a_modifier = trim($_GET['modifier']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM ue WHERE id_UE = ?");
        $stmt->execute([$id_UE_a_modifier]);
        $ue_a_modifier = $stmt->fetch();
        if ($ue_a_modifier) {
            $mode_edition = true;
        } else {
            $error_message = "UE non trouvée pour modification.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour des UE</title>
    <link rel="stylesheet" href="../css/maj_UE.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        .modal-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-confirm {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
        
        .form-edit-mode {
            background-color: #e7f3ff;
            border: 2px solid #007bff;
        }
        
        .edit-mode-title {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h1 class="page-title">Mise à jour des UE</h1>

    <?php if (!empty($success_message)) : ?>
        <div class="success-message" style="display: block;">
            ✅ <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)) : ?>
        <div class="error-message" style="display: block;">
            ❌ <?= $error_message ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="MAJ_UE.php">
            <?php if ($mode_edition): ?>
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id_UE_original" value="<?= htmlspecialchars($ue_a_modifier['id_UE']) ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="ajouter">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="id_ac">Année Académique *</label>
                    <select id="id_ac" name="id_ac" required>
                        <option value="">Sélectionner...</option>
                        <?php foreach ($annees_academiques as $annee) : ?>
                            <option value="<?= htmlspecialchars($annee['id_ac']) ?>" 
                                <?= ($mode_edition && $ue_a_modifier['id_ac'] == $annee['id_ac']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($annee['id_ac']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_niv_etu">Niveau d'Étude *</label>
                    <select id="id_niv_etu" name="id_niv_etu" required>
                        <option value="">Sélectionner...</option>
                        <?php foreach ($niveaux_etude as $niveau) : ?>
                            <option value="<?= htmlspecialchars($niveau['id_niv_etu']) ?>" 
                                <?= ($mode_edition && $ue_a_modifier['id_niv_etu'] == $niveau['id_niv_etu']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($niveau['lib_niv_etu']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_semes">Semestre *</label>
                    <select id="id_semes" name="id_semes" required>
                        <option value="">Sélectionner...</option>
                        <?php foreach ($semestres as $semestre) : ?>
                            <option value="<?= htmlspecialchars($semestre['id_semes']) ?>"
                                <?= ($mode_edition && $ue_a_modifier['id_semes'] == $semestre['id_semes']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($semestre['lib_semes']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_UE">ID UE *</label>
                    <input type="text" id="id_UE" name="id_UE" placeholder="Ex: UE001" required 
                           value="<?= $mode_edition ? htmlspecialchars($ue_a_modifier['id_UE']) : '' ?>"
                           pattern="[A-Z0-9]{3,10}" title="3 à 10 caractères alphanumériques en majuscules">
                </div>
                <div class="form-group">
                    <label for="lib_UE">Libellé *</label>
                    <input type="text" id="lib_UE" name="lib_UE" placeholder="Nom de l'UE" required maxlength="100"
                           value="<?= $mode_edition ? htmlspecialchars($ue_a_modifier['lib_UE']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="credit_UE">Crédit *</label>
                    <input type="number" id="credit_UE" name="credit_UE" placeholder="Nombre de crédits" 
                           min="1" max="30" required 
                           value="<?= $mode_edition ? htmlspecialchars($ue_a_modifier['credit_UE']) : '' ?>">
                </div>
            </div>

            <div style="display: flex; justify-content: end; gap: 10px;">
                <button type="submit" class="validate-btn">
                    <?= $mode_edition ? 'Modifier' : 'Valider' ?>
                </button>
                <?php if ($mode_edition): ?>
                    <a href="maj_UE.php" class="validate-btn" style="background: #6c757d; text-decoration: none; display: inline-block; text-align: center;">
                        Annuler
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="🔍 Rechercher dans le tableau..." />
    </div>

    <div class="table-container">
        <div class="table-header">
            Liste des UE
        </div>
        
        <div class="table-info" id="tableInfo">
            Chargement...
        </div>
        
        <table class="data-table" id="dataTable">
            <thead>
                <tr>
                    <th>Année Académique</th>
                    <th>Niveau d'Étude</th>
                    <th>Semestre</th>
                    <th>ID UE</th>
                    <th>Libellé</th>
                    <th>Crédit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                try {
                    // Requête avec jointures pour afficher les libellés complets
                   $stmt = $pdo->query("
                    SELECT 
                        u.*,
                        CONCAT(a.dte_deb, ' - ', a.dte_fin) as annee_libelle,
                        n.lib_niv_etu as niveau_libelle,
                        s.lib_semes as semestre
                    FROM ue u
                    LEFT JOIN annee_academique a ON u.id_ac = a.id_ac
                    LEFT JOIN niveau_etude n ON u.id_niv_etu = n.id_niv_etu
                    LEFT JOIN semestre s ON u.id_semes = s.id_semes
                    ORDER BY a.dte_deb DESC, n.id_niv_etu, u.id_semes, u.id_UE
                ");

                    $rows = $stmt->fetchAll();
                    $nombre_total = count($rows);
                    
                    if (!$rows) {
                        echo '<tr id="no-data"><td colspan="7" style="text-align:center;">Aucune UE enregistrée.</td></tr>';
                    } else {
                        foreach ($rows as $row) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['annee_libelle'] ?? 'Non définie') . '</td>
                                    <td>' . htmlspecialchars($row['niveau_libelle'] ?? 'Non défini') . '</td>
                                    <td>' . htmlspecialchars($row['semestre']) . '</td>
                                    <td>' . htmlspecialchars($row['id_UE']) . '</td>
                                    <td>' . htmlspecialchars($row['lib_UE']) . '</td>
                                    <td>' . htmlspecialchars($row['credit_UE']) . '</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="maj_UE.php?modifier=' . urlencode($row['id_UE']) . '" class="btn-edit" title="Modifier cette UE">Modifier</a>
                                            <button type="button" class="btn-delete" title="Supprimer cette UE" onclick="confirmerSuppression(\'' . htmlspecialchars($row['id_UE'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['lib_UE'], ENT_QUOTES) . '\')">Supprimer</button>
                                        </div>
                                    </td>
                                </tr>';
                        }
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="7" style="text-align:center;">Erreur lors du chargement : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    $nombre_total = 0;
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="modalSuppression" class="modal">
        <div class="modal-content">
            <h3>Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer l'UE "<span id="nomUE"></span>" ?</p>
            <p style="color: #dc3545; font-size: 14px;">Cette action est irréversible.</p>
            <div class="modal-buttons">
                <button type="button" class="btn-confirm" onclick="supprimerUE()">Oui, supprimer</button>
                <button type="button" class="btn-cancel" onclick="fermerModal()">Annuler</button>
            </div>
        </div>
    </div>

    <!-- Formulaire caché pour la suppression -->
    <form id="formSuppression" method="POST" style="display: none;">
        <input type="hidden" name="action" value="supprimer">
        <input type="hidden" name="id_UE" id="idUESupprimer">
    </form>

    <script>
        let idUEASupprimer = '';

        function confirmerSuppression(idUE, libelle) {
            idUEASupprimer = idUE;
            document.getElementById('nomUE').textContent = libelle;
            document.getElementById('modalSuppression').style.display = 'block';
        }

        function supprimerUE() {
            document.getElementById('idUESupprimer').value = idUEASupprimer;
            document.getElementById('formSuppression').submit();
        }

        function fermerModal() {
            document.getElementById('modalSuppression').style.display = 'none';
        }

        // Fermer la modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('modalSuppression');
            if (event.target === modal) {
                fermerModal();
            }
        }

        // Fonction améliorée de filtrage du tableau
        function filtrerTableau() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr:not(#no-data)');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchValue);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Mettre à jour l'info du tableau
            const totalRows = rows.length;
            const infoElement = document.getElementById('tableInfo');
            if (searchValue) {
                infoElement.textContent = `${visibleCount} UE(s) trouvée(s) sur ${totalRows} au total`;
            } else {
                infoElement.textContent = `${totalRows} UE(s) au total`;
            }
        }

        // Initialiser l'info du tableau au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const totalRows = document.querySelectorAll('#tableBody tr:not(#no-data)').length;
            document.getElementById('tableInfo').textContent = `${totalRows} UE(s) au total`;
        });

        // Ajouter l'événement de recherche
        document.getElementById('searchInput').addEventListener('input', filtrerTableau);

        // Validation côté client pour l'ID UE
        document.getElementById('id_UE').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Confirmation avant soumission du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const action = this.querySelector('input[name="action"]').value;
            if (action === 'ajouter' || action === 'modifier') {
                const idUE = document.getElementById('id_UE').value;
                const libUE = document.getElementById('lib_UE').value;
                
                if (!confirm(`Confirmer ${action === 'ajouter' ? 'l\'ajout' : 'la modification'} de l'UE "${idUE} - ${libUE}" ?`)) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html> 