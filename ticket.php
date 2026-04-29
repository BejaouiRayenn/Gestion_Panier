<?php
// Démarre la session pour maintenir l'accès aux données utilisateur si besoin
session_start();
// Inclut la connexion à la base de données via l'objet $pdo
require_once 'config.php';

// Sécurité  Si l'ID de la commande n'est pas présent dans l'URL 
if (!isset($_GET['id'])) {
    // Redirige vers l'accueil car on ne peut pas afficher un ticket sans numéro
    header("Location: index.php");
    exit;
}

// Convertit l'ID reçu en nombre entier pour sécuriser la requête SQL
$id_commande = (int)$_GET['id'];

// Récupération des informations générales de la commande et du client
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_commande = ?");
$stmt->execute([$id_commande]);
// Stocke les données de la commande dans la variable $commande
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucune commande ne correspond à cet ID dans la base de données
if (!$commande) {
    echo "<h2>Commande non trouvée.</h2>";
    exit; // Arrête le script
}

// Récupération des produits liés à cette commande précise
// On utilise JOIN pour aller chercher le 'nom' du produit dans la table 'produits'
$stmt_detail = $pdo->prepare("
    SELECT d.*, p.nom 
    FROM details_commande d 
    JOIN produits p ON d.id_produit = p.id_produit 
    WHERE d.id_commande = ?
    ORDER BY d.id_detail
");
$stmt_detail->execute([$id_commande]);
// Récupère toutes les lignes de produits sous forme de tableau multidimensionnel
$details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Commande #<?= $id_commande ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /*Styles spécifiques au ticket (cadre, pointillés, ombres) */
        .ticket {
            max-width: 850px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 25px rgba(0,0,0,0.15);
            border: 3px dashed #007bff; /* Bordure en pointillés style "ticket" */
            font-family: Arial, sans-serif;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        .client-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .btn-print {
            background: #28a745;
            color: white;
            padding: 15px 40px;
            font-size: 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 40px auto;
        }
        /* RÈGLES POUR L'IMPRESSION : Masque les boutons et la navigation sur le papier */
        @media print {
            .btn-print, header, nav, .container > p { 
                display: none !important; 
            }
            .ticket { 
                box-shadow: none; 
                border: 2px dashed #000; 
                margin: 0; 
                padding: 30px;
            }
            body { background: white; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="ticket">
        <div class="ticket-header">
            <h1>🛒 Boutique en Ligne</h1>
            <h2>Ticket de Commande</h2>
            <p><strong>Commande N° <?= $id_commande ?></strong> — <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?>
        </div>

        <div class="client-info">
            <h3>Informations du Client</h3>
            <p><strong>Nom complet :</strong> <?= htmlspecialchars($commande['prenom_client'] ?? '') . ' ' . htmlspecialchars($commande['nom_client'] ?? '') ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($commande['email'] ?? 'Non renseigné') ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($commande['telephone'] ?? 'Non renseigné') ?></p>
            <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($commande['adresse'] ?? 'Non renseignée')) ?></p>
        </div>

        <h3>Détails de la commande</h3>
        <table>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
            <?php 
            $total_verif = 0; 
            // Variable de contrôle pour recalculer le total
            foreach ($details as $item): 
                $sous_total = $item['quantite'] * $item['prix_unitaire'];
                $total_verif += $sous_total;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['nom']) ?></td>
                <td style="text-align:center;"><?= $item['quantite'] ?></td>
                <td><?= number_format($item['prix_unitaire'], 2) ?> TND</td>
                <td><?= number_format($sous_total, 2) ?> TND</td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="total" style="font-size:28px; margin-top:40px; text-align:right;">
            <strong>Total payé : <?= number_format($commande['total'], 2) ?> TND</strong>
        </div>

        <p style="text-align:center; margin-top:50px; color:#555; font-style:italic;">
            Merci pour votre confiance !<br>
            Nous espérons vous revoir bientôt.
        </p>

        <button onclick="window.print()" class="btn-print">
            🖨️ Imprimer le ticket
        </button>
    </div>

    <p style="text-align:center; margin-top:30px;">
        <a href="index.php" class="btn">Retour à l'accueil</a>
    </p>
</div>

</body>
</html>