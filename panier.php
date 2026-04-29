<?php
// Démarre ou récupère la session actuelle  pour manipuler le panier
session_start();

// Inclut le fichier de configuration connexion à la base de données
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Bienvenue</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="panier.php">Panier</a>
        <a href="mesCommandes.php">Mes Commandes </a> 
    </nav>
</header>

<div class="container">
    <h2>Votre panier</h2>

    <?php 
    /*gestion des message  flash succes ou erreur 
     Si un message de succès existe en session on l'affiche*/
    if (isset($_SESSION['message_success'])): 
    ?>


        <div class="message message-success">
            <?= $_SESSION['message_success'] ?>
        </div>
        <!-- On supprime le message pour qu'il ne s'affiche plus au prochain rafraîchissement-->
        <?php unset($_SESSION['message_success']);  ?>
    
    <?php 
    // Sinon si un message d'erreur existe on l'affiche
    elseif (isset($_SESSION['message_error'])): ?>
        <div class="message message-error">
            <?= $_SESSION['message_error'] ?>
        </div>
        <!-- Suppression après affichage-->
        <?php unset($_SESSION['message_error']);  ?>
    <?php endif; ?>

    <?php 
    //  pour un message générique par exemple  avertissement stock)
    if (isset($_SESSION['message'])): ?>
        <div class="message" style="background:#fff3cd; color:#856404;">
            // Sécurise l'affichage contre les failles XSS
            <?= htmlspecialchars($_SESSION['message'])  ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>



    <?php 
    /*verification  du panier 
     Si la session panier est vide ou inexistante*/
    if (empty($_SESSION['panier'])): ?>
        <p class="message">Votre panier est vide !!</p>
        <p style="text-align:center;">
            <a href="index.php" class="btn" style="background:#007bff;">Voir les produits</a>
        </p>


    <?php else: ?>
        <table>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Total ligne</th>
                <th>Actions</th>
            </tr>
            <?php 
            $total_general = 0; // Initialise le compteur pour le montant total du panier
            
            // On boucle sur chaque élément stocké dans le panier l'index permet d'identifier la ligne
            foreach ($_SESSION['panier'] as $index => $item): 
                $total_ligne = $item['prix'] * $item['quantite']; // Calcul total pour cet article
                $total_general += $total_ligne; // Ajout au total cumulé
            ?>
            <tr>
                <td><?= htmlspecialchars($item['nom']) ?></td>
                
                <td><?= number_format($item['prix'], 2) ?> TND</td>
                
                <td>
                    <form action="modifier_panier.php" method="POST" style="display:inline;">
                       
                        <input type="hidden" name="index" value="<?= $index ?>"> <button type="submit" name="action" value="moins" class="btn" style="padding:5px 10px; font-size:14px;">–</button>
                        
                        <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1" style="width:50px; text-align:center;" readonly>
                        
                        <button type="submit" name="action" value="plus" class="btn" style="padding:5px 10px; font-size:14px;">+</button>
                    </form>
                </td>
                
                <td><?= number_format($total_ligne, 2) ?> TND</td>
                
                <td>
                    <form action="supprimer_panier.php" method="POST" style="display:inline;">
                        <input type="hidden" name="index" value="<?= $index ?>">
                        <button type="submit" class="btn" style="background:#dc3545;">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
<!--affiche total du panier -->
        <div class="total">
            Total du panier : <strong><?= number_format($total_general, 2) ?> TND</strong>
        </div>
<!--- button pour confirmer commande--> 
        <a href="checkout.php" class="btn" style="background:#28a745; font-size:18px; padding:15px 30px;">
             Finaliser la commande
        </a>
    <?php endif; ?>
</div>

</body>
</html>