<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : test_email.php
 * Objectif : Tester l'envoi d'emails automatiques
 */

// ✅ Sécurité : empêcher accès en production
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost:8888') {
    http_response_code(403);
    exit('Accès non autorisé.');
}

require_once 'includes/email_sender.php';

echo "<h2>🧪 Test d'envoi d'emails automatiques</h2>";

// Données de test
$testData = [
    'nom' => 'Test User',
    'email' => 'test@example.com', // Remplacez par votre email pour le test
    'motif' => 'planification_complete',
    'date_mariage' => '2025-06-15',
    'nombre_invites' => 80,
    'lieu_mariage' => 'Château de Versailles',
    'message_autre' => ''
];

echo "<p><strong>Test avec les données :</strong></p>";
echo "<ul>";
echo "<li>Nom : " . htmlspecialchars($testData['nom']) . "</li>";
echo "<li>Email : " . htmlspecialchars($testData['email']) . "</li>";
echo "<li>Motif : " . htmlspecialchars($testData['motif']) . "</li>";
echo "<li>Date : " . htmlspecialchars($testData['date_mariage']) . "</li>";
echo "</ul>";

echo "<p><strong>Tentative d'envoi...</strong></p>";

try {
    $result = sendAutomaticEmail($testData);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ Email envoyé avec succès !</p>";
        echo "<p>Vérifiez votre boîte email (et les spams).</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Erreur lors de l'envoi de l'email.</p>";
        echo "<p>Vérifiez la configuration SMTP dans email_sender.php</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Pour tester avec votre email :</strong></p>";
echo "<p>1. Remplacez 'test@example.com' par votre email dans ce fichier</p>";
echo "<p>2. Rechargez cette page</p>";
echo "<p>3. Vérifiez votre boîte email</p>";

echo "<hr>";
echo "<p><strong>Test du formulaire complet :</strong></p>";
echo "<p><a href='index.php'>Aller au formulaire de contact</a></p>";
?>
