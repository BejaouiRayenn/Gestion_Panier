<?php
// Connexion à PostgreSQL avec PDO
date_default_timezone_set('Africa/Tunis');
$host     = 'localhost';
$dbname   = 'gestion_panier';
$user     = 'postgres';     
$password = 'admin';             

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // setAttribute modifier le comportement de ta connexion à la base de données
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // exeption
    throw new Exception(" Erreur de connexion à PostgreSQL : " . $e->getMessage());
}
?>