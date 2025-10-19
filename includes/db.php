<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : includes/db.php
 * Objectif : Connexion PDO sécurisée à la base de données
 */

declare(strict_types=1);

// ✅ Sécurité : empêcher accès direct au fichier
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Accès direct interdit.');
}

// ⚙️ Chargement de la configuration sécurisée
require_once __DIR__ . '/../config.php';

// ✅ DSN PDO avec variables de configuration
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

try {
    // ✅ Connexion sécurisée avec options
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Exceptions sur erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Résultats en tableau associatif
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Prépare les requêtes côté serveur (anti-injection)
    ]);
} catch (PDOException $e) {
    // 🚨 Journalisation propre (aucune fuite d'info)
    error_log('Erreur de connexion à la BDD : ' . $e->getMessage());
    exit('Une erreur interne est survenue. Merci de réessayer plus tard.');
}
