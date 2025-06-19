<?php
include('../config/connexion_BD.php'); // Connexion à la base de données

// AJOUT D’UNE ACTION
if (isset($_POST['ajouter'])) {
    $id_action = $_POST['id_action'];
    $lib_action = $_POST['lib_action'];

    // Préparer et exécuter l'insertion
    $stmt = $pdo->prepare("INSERT INTO action (id_action, lib_action) VALUES (:id_action, :lib_action)");

    try {
        $stmt->execute([
            'id_action' => $id_action,
            'lib_action' => $lib_action
        ]);
        header('Location: MAJ_action.php'); // Rediriger après l'ajout
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout : " . $e->getMessage();
    }
}

// SUPPRESSION D’UNE ACTION
if (isset($_GET['delete'])) {
    $id_action = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM action WHERE id_action = :id_action");

    try {
        $stmt->execute(['id_action' => $id_action]);
        header('Location: MAJ_action.php'); // Rediriger après suppression
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}
?>
