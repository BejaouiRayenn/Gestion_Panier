<?php
// Demarre la session pour manipuler le panier 
session_start();
// Inclut la connexion à la base de donnees 
require_once 'config.php';
// Verifie si l'ID du produit a bien ete  envoye  via le formulaire
if (isset($_POST['id_produit'])) {
    // Securise l'ID en le forçant en nombre entier
    $id_produit = (int)$_POST['id_produit'];
    // prèpare une requête pour recuperer les informations reelles du produit nom  prix et stock depuis la base
    $stmt = $pdo->prepare("SELECT id_produit, nom, prix, stock FROM produits WHERE id_produit = ?");
    //execution 
    $stmt->execute([$id_produit]);
    // récupère le résultat sous forme de tableau associatif Fetch_assoc
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    // Si le produit existe bien dans la base de données
    if ($produit) {
        // verification du stock si  on trouve le stock est inferieur ou eglae  0
        if ($produit['stock'] <= 0) {
            // On enregistre un message d'erreur et on n'ajoute rien
            $_SESSION['message'] = " Désolé, " . $produit['nom'] . " est en rupture de stock.";
        } else {
            // initialization si  le panier n'existe pas encore en session on crée un tableau vide
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = [];
            }
            $trouve = false;
            // On parcourt le panier actuel pour voir si le produit  est déjà  dans le panier utilisation de & pour modifier l'item directement
            foreach ($_SESSION['panier'] as &$item) {
                if ($item['id_produit'] == $id_produit) {
                    // Si le produit est present deja  on verifie  si on peut encore  ajouter selon le stock
                    if ($item['quantite'] + 1 <= $produit['stock']) {
                         // On augmente la quantité
                        $item['quantite']++;
                        $_SESSION['message'] = " Quantité de " . $produit['nom'] . " augmentée (+1)";
                    } else {
                        // Si on dépasse le stock dispo on affiche un avertissement
                        $_SESSION['message'] = " Stock maximum atteint pour " . $produit['nom'];
                    }
                    // On marque que le produit a ete trouve 
                    $trouve = true; 
                    // On sort de la boucle foreach
                    break; 
                }
            }
            // ajouter du nouveau    Si le produit n'etait pas encore dans le panier
            if (!$trouve) {
                // On ajoute un nouveau tableau associatif représentant le produit
                $_SESSION['panier'][] = [
                    'id_produit' => $produit['id_produit'],
                    'nom' => $produit['nom'],
                    // Le prix vient de la base
                    'prix' => $produit['prix'], 
                    'quantite' => 1
                ];
                $_SESSION['message'] =  $produit['nom'] . " ajouté au panier avec succès !";
            }
        }
    } else {
        // Si l'id envoy ne correspond à aucun produit en base
        $_SESSION['message'] = " Produit non trouvé.";
    }
}
//gestion de redirection 
$redirect = "index.php";
// Si l'utilisateur avait fait une recherche, on la garde dans l'URL pour ne pas perdre son filtre
if (!empty($_GET['search'])) {
    $redirect .= "?search=" . urlencode($_GET['search']);
}
// Redirige l'utilisateur vers la page d'accueil
header("Location: " . $redirect);
// Termine  le script
exit;
?>