<?php
session_start();
include('../config/connexion_BD.php');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username && $password) {
    $stmt = $pdo->prepare("
    SELECT u.*, tu.id_gu 
    FROM utilisateur u 
    JOIN type_utilisateur tu ON u.id_tu = tu.id_tu
    WHERE u.login_util = ?
");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['mdp_util'])) {
    $_SESSION['id_util'] = $user['id_util'];
    $_SESSION['login_util'] = $user['login_util'];
    $_SESSION['id_tu'] = $user['id_tu'];
    $_SESSION['id_gu'] = $user['id_gu']; // important !

    switch ($user['id_gu']) {
        case 1:
            header("Location: Tableau_bord_etudiant.php");
            break;
        case 2:
            header("Location: Tableau_bord_enseignant.php");
            break;
        case 3:
            header("Location: Tableau_bord_admin.php");
            break;
        default:
            $_SESSION['erreur_connexion'] = "Groupe utilisateur inconnu.";
            header("Location: Connexion.php");
            exit();
    }
    exit();
}
}

