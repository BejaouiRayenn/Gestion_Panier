<?php
// demarre la session pour récupérer le contenu du panier
session_start();
// inclut le fichier de configuration pour les eventuels reglages ou accès DB
require_once 'config.php';
// Securite  si le panier est vide l'utilisateur n'a rien à faire ici
if (empty($_SESSION['panier'])) {
    // On le redirige vers la page du panier
    header("Location: panier.php");
    // On arrête l'exécution du script
    exit;
}
// Initialisation de la variable pour calculer le montant total de la commande
$total = 0;
// On boucle sur chaque article du panier en session
foreach ($_SESSION['panier'] as $item) {
    // On ajoute au total le calcul : (prix de l'article * sa quantité)
    $total += $item['prix'] * $item['quantite'];
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser la commande</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Finaliser votre commande</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="panier.php">Retour au panier</a>
    </nav>
</header>

<div class="container">
    <h2>Informations du client</h2>

    <form action="valider_commande.php" method="POST">
        <div style="max-width:600px; margin:0 auto; background:white; padding:25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
            
            <label>Prénom <span style="color:red;">*</span></label><br>
            <input type="text" name="prenom" required style="width:100%; padding:10px; margin:8px 0;"><br><br>
            
            <label>Nom <span style="color:red;">*</span></label><br>
            <input type="text" name="nom" required style="width:100%; padding:10px; margin:8px 0;"><br><br>
            
                        <label>Email <span style="color:red;">*</span></label><br>
            <input type="email" 
                   name="email" 
                   id="email"
                   required 
                   placeholder="exemple@gmail.com"
                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                   title="Veuillez entrer une adresse email valide"
                   style="width:100%; padding:10px; margin:8px 0;">
            <small style="color:#666;"></small><br><br>
            
                        <label>Téléphone <span style="color:red;">*</span></label><br>
            <input type="tel" 
                   name="telephone" 
                   id="telephone"
                   required 
                   placeholder=" ex: 98123456"
                   pattern="[0-9]{8,}"
                   maxlength="15"
                   title="Le numéro de téléphone doit contenir uniquement des chiffres (minimum 8 chiffres)"
                   style="width:100%; padding:10px; margin:8px 0;">
            <small style="color:#666;"></small><br><br>
            
            <label>Adresse complète <span style="color:red;">*</span></label><br>
            <textarea name="adresse" required rows="4" style="width:100%; padding:10px; margin:8px 0;"></textarea><br><br>

            <hr> <h3 style="text-align:right; color:#28a745;">
                Total à payer : <strong><?= number_format($total, 2) ?> TND</strong>
            </h3>

            <button type="submit" class="btn" style="width:100%; padding:15px; font-size:18px; background:#28a745;">
                 Confirmer et payer la commande
            </button>
        </div>
    </form>
</div>

</body>
</html>