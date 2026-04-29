<?php
// demarre la session pour maintenir la connexion 
session_start();
// Inclut le fichier de configuration pour acceder à la connexion base de données 
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>📜 Mes Commandes</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="panier.php">Panier</a>
        <a href="mes_commandes.php">Mes Commandes</a>
    </nav>
</header>

<div class="container">
    <h2>Historique des commandes</h2>
    


    
    <?php
    // Exécute une requête SQL pour récupérer toutes les commandes, les plus récentes en premier (DESC)
    $stmt = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC");
    // Verifie si la requete n'a renvoyé aucun resultat si l'historique est vide
    if ($stmt->rowCount() == 0) {
        echo "<p>Aucune commande pour le moment.</p>";
    } else {
        // Boucle "while"  tant qu'il y a une ligne de commande à lire dans les resultats
        while ($commande = $stmt->fetch()) {
            // Affiche un bloc HTML blanc avec une ombre pour chaque commande
            echo "<div style='background:white; padding:15px; margin:15px 0; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>";
            // affiche l'identifiant unique de la commande et sa date
            echo "<strong>Commande n° " . $commande['id_commande'] . "</strong> - " . $commande['date_commande'] . "<br>";
            // affiche le montant total formaté avec deux décimales et l'unite TND
            echo "Total : <strong>" . number_format($commande['total'], 2) . " TND</strong>";
            // Fermeture du bloc de la commande
            echo "</div>"; 
        }
    }
    ?>
</div>
</body>
</html>