<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['panier']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: panier.php");
    exit;
}

/* Récupérer les informations du client*/
// Récupération et validation des données du formulaire
$nom_client     = trim($_POST['nom'] ?? '');
$prenom_client  = trim($_POST['prenom'] ?? '');
$email          = trim($_POST['email'] ?? '');
$telephone      = trim($_POST['telephone'] ?? '');
$adresse        = trim($_POST['adresse'] ?? '');

// Validation de l'email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message_error'] = " Veuillez entrer une adresse email valide.";
    header("Location: checkout.php");
    exit;
}

// Validation des champs obligatoires
if (empty($nom_client) || empty($prenom_client) || empty($adresse)) {
    $_SESSION['message_error'] = " Veuillez remplir tous les champs obligatoires.";
    header("Location: checkout.php");
    exit;
}
// Validation du téléphone (obligatoire + uniquement des chiffres)
if (empty($telephone) || !preg_match('/^[0-9]{8,}$/', $telephone)) {
    $_SESSION['message_error'] = " Le numéro de téléphone est obligatoire et doit contenir uniquement des chiffres (minimum 8 chiffres).";
    header("Location: checkout.php");
    exit;
}

/* Calculer le total*/
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}

try {
    $pdo->beginTransaction();

    // Insérer la commande avec les infos client
    $stmt = $pdo->prepare("
        INSERT INTO commandes 
        (total, nom_client, prenom_client, email, telephone, adresse) 
        VALUES (?, ?, ?, ?, ?, ?) 
        RETURNING id_commande
    ");
    $stmt->execute([$total, $nom_client, $prenom_client, $email, $telephone, $adresse]);
    $id_commande = $stmt->fetchColumn();

    // Insérer les détails de commande 
    $stmt_detail = $pdo->prepare("
        INSERT INTO details_commande (id_commande, id_produit, quantite, prix_unitaire) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_SESSION['panier'] as $item) {
        $stmt_detail->execute([$id_commande, $item['id_produit'], $item['quantite'], $item['prix']]);
    }

    // Mise à jour du stock (comme avant)
    $stmt_stock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id_produit = ?");
    foreach ($_SESSION['panier'] as $item) {
        $stmt_stock->execute([$item['quantite'], $item['id_produit']]);
    }

    $pdo->commit();

    unset($_SESSION['panier']);

    // Redirection vers le ticket
    header("Location: ticket.php?id=" . $id_commande);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message_error'] = " Erreur lors de l'enregistrement : " . $e->getMessage();
    header("Location: panier.php");
    exit;
}
?>