<?php
include("../config/connexion_BD.php");

function getCount($pdo, $query) {
    $stmt = $pdo->query($query);
    return $stmt->fetchColumn();
}

$data = [
    'etudiants_inscrits' => getCount($pdo, "SELECT COUNT(*) FROM etudiant"),
    'rapports_deposes' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'déposé'"),
    'rapports_approuves' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'approuvé'"),
    'rapports_commission' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'envoyé à la commission'"),
    'rapports_valides' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'validé'"),
    'rapports_rejetes' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat IN ('rejeté', 'anomalie')"),
    'messages_non_lus' => getCount($pdo, "SELECT COUNT(*) FROM messages WHERE lu = 0"),
    'dossiers_incomplets' => getCount($pdo, "SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'incomplet'"),
    'sans_entreprise' => getCount($pdo, "SELECT COUNT(*) FROM etudiant WHERE id_entr IS NULL"),
    'activite_semaine' => getCount($pdo, "
        SELECT COUNT(*) FROM rapport_etudiant WHERE date_depot >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    "),
    'alertes_critiques' => getCount($pdo, "
        SELECT COUNT(*) FROM rapport_etudiant WHERE etat = 'anomalie critique'
    ")
];

header('Content-Type: application/json');
echo json_encode($data);
