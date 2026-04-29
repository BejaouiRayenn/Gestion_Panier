
<?php
// demarrage du session pour stocker les donnes utilisateur  panier
session_start();
// Inclure le fichier de configuration pour connexion à la base de données
require_once 'config.php';
// recupererer la valeur du  baarre recherche depuis GET 
// trim pour supprimer lespace 
// ? c est loperateur ternaire 
//condition ? valeur_si_vrai : valeur_si_faux;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
//toujous vrai 
// je prends toute les produits 
// permet d' ajouter des conditions AND  facilement 
// 
$query = "SELECT * FROM produits WHERE 1=1";
//Tableau pour stocker les valeurs sécurisées
$params = [];
// si  l utilisateur creer quelque chose 
if (!empty($search)) {
    //ajoutons une conditions pour chercher dans le mom 
    // ou bien dans la description 
    $query .= " AND (nom ILIKE ? OR description ILIKE ?)";
    // ajouter les valeur pour la description or pour le nom 
    $params[] = "%$search%";
    $params[] = "%$search%";
}
// Trier les produits par ID
$query .= " ORDER BY id_produit";
// execution securise par rapport query 
// Prépare la requête
$stmt = $pdo->prepare($query);
// execution 
$stmt->execute($params);
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - Panier d'Achat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>🛒 Boutique en Ligne</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="panier.php">Panier 
            <?php 
            //si panier existe
            if (!empty($_SESSION['panier'])) {
                //calculons total des quantites
                //array_column récupère toutes les quantités
                //array_sum  fait la somme

                $nb = array_sum(array_column($_SESSION['panier'], 'quantite'));
                echo "($nb)";
            }
            ?>
        </a>
        <a href="mes_commandes.php">Mes Commandes</a>
    </nav>
</header>

<div class="container">
    <h1>Nos Produits</h1>

     <!--Barre de recherche 
     Envoie les données dans l'URL'-->
    <form method="GET" style="text-align:center; margin-bottom:30px;">
        <!--Protège contre XSS -->
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
               placeholder="Rechercher un produit..." 
               style="padding:12px; width:400px; font-size:16px; border-radius:6px; border:1px solid #ccc;">
        <button type="submit" class="btn" style="padding:12px 20px;">🔍 Rechercher</button>
    </form>

    <div class="produits">
        <!--si aucun resultat-->
        <?php if ($stmt->rowCount() == 0): ?>
            <p style="grid-column:1/-1; text-align:center;">Aucun produit trouvé pour "<?= htmlspecialchars($search) ?>"</p>
        <?php else: 
        //parcourt les produits un par un
            while ($produit = $stmt->fetch(PDO::FETCH_ASSOC)): 
        ?>
            <div class="card">
                <?php
                //chemin image 
                //si image null=default
$img_path = 'images/' . ($produit['image'] ?? 'default.jpg');
// image non existe 
//image temporaire generer
if (!file_exists($img_path)) {
    $img_path = 'https://via.placeholder.com/280x200/007bff/ffffff?text=' . urlencode(substr($produit['nom'], 0, 12));
}
?>

<img 
    src="<?= $img_path ?>" 
    alt="<?= htmlspecialchars($produit['nom']) ?>"  
    style="width:100%; height:200px; object-fit: cover;"
                     alt="<?= htmlspecialchars($produit['nom']) ?>">
                <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                <!--Prix avec 2 chiffres après virgule-->
                <p><?= number_format($produit['prix'], 2) ?> TND</p>
                <!-- Description sécurisée-->
                <p class="description"><?= htmlspecialchars($produit['description']) ?></p>
                <!-- gestion du stock-->
                <!--coleur dynamique -->
                <!--vert stock suffisante--> 
                <!-- rouge faible stock-->
                <p style="color:<?= $produit['stock'] > 10 ? '#28a745' : '#dc3545' ?>;">
                    Stock : <?= $produit['stock'] ?> 
                </p>
                <!-- Si produit disponible-->
                <?php if ($produit['stock'] > 0): ?>
                    <!--Envoie ID produit au script panier-->
                <form action="ajouter_panier.php" method="POST">
                    <!-- Champ caché  grace ala methode POST -->
                    <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                    <button type="submit" class="btn">🛒 Ajouter au panier</button>
                </form>
                <?php else: ?>
                    <!-- button desactivee si pas du stock -->
                    <button class="btn" style="background:#6c757d; cursor:not-allowed;">Rupture de stock</button>
                <?php endif; ?>
            </div>
        <?php endwhile; endif; ?>
    </div>
</div>

</body>
</html>