<?php
// Démarre la session pour accéder au panier stocké sur le serveur
session_start();

// Vérifie si l'identifiant index  du produit à supprimer a bien été envoyé par le formulaire
if (isset($_POST['index'])) {
    // Convertit l'index en nombre entier  pour sécuriser la donnée reçue
    $index = (int)$_POST['index'];
    // Vérifie si le produit existe réellement dans le panier avant  de le supprimer
    if (isset($_SESSION['panier'][$index])) {
        // Supprime l'élément correspondant à l'index du tableau session 'panier'
        unset($_SESSION['panier'][$index]);
        /*réindexation  array_values récupère toutes les valeurs du tableau et recrée des index de 0 à n
         Cela évite d'avoir des trous dans les clés du tableau par exemple  passer de l'index 0 à l'index 2*/
        $_SESSION['panier'] = array_values($_SESSION['panier']);
    }
}
// redirige l'utilisateur vers la page d'affichage du panier
header("Location: panier.php");

// arrête immédiatement l'exécution du script après la demande de redirection
exit;
?>