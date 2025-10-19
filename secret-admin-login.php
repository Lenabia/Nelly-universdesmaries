<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : secret-admin-login.php
 * Objectif : Accès discret à l'administration avec protection token + IP
 */

declare(strict_types=1);
session_start();

// ✅ Sécurité : Vérification IP locale uniquement
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

if (!in_array($clientIP, $allowedIPs)) {
    http_response_code(403);
    exit('Accès non autorisé.');
}

// ✅ Vérification du token secret
$secretToken = 'maries2025_secure_access';
$providedToken = $_GET['token'] ?? '';

if (!hash_equals($secretToken, $providedToken)) {
    http_response_code(403);
    exit('Token invalide.');
}

// ✅ Vérification de la méthode (GET uniquement)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

// ✅ Redirection discrète vers l'administration
header('Location: maries_portal_9834/messages.php');
exit;
