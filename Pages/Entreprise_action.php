<?php
include('../config/connexion_BD.php');

/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'];
    $libelle = $_POST['libelle'];

    if (!empty($identifiant) && !empty($libelle)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO entreprise (id_entr, lib_entr) VALUES (?, ?)");
            $stmt->execute([$identifiant, $libelle]);
            echo "<p style='color: green;'>✅ Données insérées avec succès !</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tous les champs sont obligatoires.</p>";
    }
}
?>*/


// Récupération de l'action à effectuer
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'ajouter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_entr = $_POST['identifiant'] ?? '';
            $lib_entr = $_POST['libelle'] ?? '';

            if (!empty($id_entr) && !empty($lib_entr)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO entreprise (id_entr, lib_entr) VALUES (?, ?)");
                    $stmt->execute([$id_entr, $lib_entr]);
                    header('Location: MAJ_Entreprise.php?success=Entreprise ajoutée avec succès');
                    exit;
                } catch (PDOException $e) {
                    header('Location: MAJ_Entreprise.php?error=' . urlencode("Erreur lors de l'insertion : " . $e->getMessage()));
                    exit;
                }
            } else {
                header('Location: MAJ_Entreprise.php?error=' . urlencode("Tous les champs sont obligatoires"));
                exit;
            }
        }
        break;

    case 'modifier':
        $id_entr = $_GET['id'] ?? $_POST['id_original'] ?? '';
        
        if (empty($id_entr)) {
            header('Location: MAJ_Entreprise.php?error=ID entreprise manquant');
            exit;
        }

        // Si c'est une soumission de formulaire (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouveau_id = $_POST['identifiant'] ?? '';
            $nouveau_libelle = $_POST['libelle'] ?? '';

            if (!empty($nouveau_id) && !empty($nouveau_libelle)) {
                try {
                    $stmt = $pdo->prepare("UPDATE entreprise SET id_entr = ?, lib_entr = ? WHERE id_entr = ?");
                    $stmt->execute([$nouveau_id, $nouveau_libelle, $id_entr]);
                    header('Location: MAJ_Entreprise.php?success=Entreprise modifiée avec succès');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error = "Tous les champs sont obligatoires.";
            }
        }

        // Récupération des données actuelles pour affichage
        try {
            $stmt = $pdo->prepare("SELECT * FROM entreprise WHERE id_entr = ?");
            $stmt->execute([$id_entr]);
            $entreprise = $stmt->fetch();
            
            if (!$entreprise) {
                header('Location: MAJ_Entreprise.php?error=Entreprise non trouvée');
                exit;
            }
        } catch (PDOException $e) {
            header('Location: MAJ_Entreprise.php?error=' . urlencode($e->getMessage()));
            exit;
        }
        break;

    case 'supprimer':
        $id_entr = $_GET['id'] ?? '';
        
        if (empty($id_entr)) {
            header('Location: MAJ_Entreprise.php?error=ID entreprise manquant');
            exit;
        }

        // Si c'est une confirmation de suppression (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirmation = $_POST['confirmation'] ?? '';
            
            if ($confirmation === 'oui') {
                try {
                    $stmt = $pdo->prepare("DELETE FROM entreprise WHERE id_entr = ?");
                    $stmt->execute([$id_entr]);
                    header('Location: MAJ_Entreprise.php?success=Entreprise supprimée avec succès');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de la suppression : " . $e->getMessage();
                }
            } else {
                header('Location: MAJ_Entreprise.php');
                exit;
            }
        }

        // Récupération des données de l'entreprise pour confirmation
        try {
            $stmt = $pdo->prepare("SELECT * FROM entreprise WHERE id_entr = ?");
            $stmt->execute([$id_entr]);
            $entreprise = $stmt->fetch();
            
            if (!$entreprise) {
                header('Location: MAJ_Entreprise.php?error=Entreprise non trouvée');
                exit;
            }
        } catch (PDOException $e) {
            header('Location: MAJ_Entreprise.php?error=' . urlencode($e->getMessage()));
            exit;
        }
        break;

    default:
        header('Location: MAJ_Entreprise.php');
        exit;
}

// Affichage des formulaires selon l'action
?>
