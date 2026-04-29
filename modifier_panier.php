<?php
// Démarre la session pour pouvoir modifier les données du panier stockées sur le serveur
session_start();

// Vérifie si les données nécessaires index du produit et action + ou - ont bien été envoyées via le formulaire POST
if (isset($_POST['index'], $_POST['action'])) {
    // Convertit l'index reçu en nombre entier (int) par sécurité pour éviter toute injection de texte
    $index = (int)$_POST['index'];
    // Vérifie si le produit correspondant à cet index existe bien dans le panier en session
    if (isset($_SESSION['panier'][$index])) {
        // Si l'utilisateur a cliqué sur le bouton "+"
        if ($_POST['action'] === 'plus') {
            // Incrémente  à la quantité de ce produit
            $_SESSION['panier'][$index]['quantite']++;
            
        } 
        // Sinon, si l'utilisateur a cliqué sur le bouton –
        elseif ($_POST['action'] === 'moins') {
            // Décrémente  à la quantité de ce produit
            $_SESSION['panier'][$index]['quantite']--;
            // Vérifie si la quantité est tombée à 0 ou moins suite à la diminution
            if ($_SESSION['panier'][$index]['quantite'] <= 0) {
                // Si la quantité est nulle, on supprime carrément le produit du panier
                unset($_SESSION['panier'][$index]);
            }
        }
    }
}

// Redirige l'utilisateur vers la page du panier pour qu'il voie les modifications immédiatement
header("Location: panier.php");
// Interrompt l'exécution du script pour s'assurer que la redirection est bien effectuée et que rien d'autre ne s'exécute
exit;
?>