<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=bdcov", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // Pas d'affichage ici
} catch(PDOException $e) {
  // Affiche une erreur propre si besoin, ou log dans un fichier
  die("Erreur de connexion à la base de données.");
}
?>
