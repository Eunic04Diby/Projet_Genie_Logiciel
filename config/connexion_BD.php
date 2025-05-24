<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=bdcov", $username, $password);
  // Activer le mode exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // Connexion réussie
  echo "Connexion réussie";
  
} catch(PDOException $e) {
  echo "Échec de la connexion : " . $e->getMessage();
}
?>
