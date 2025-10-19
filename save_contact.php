<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : save_contact.php
 * Objectif : Traitement sécurisé du formulaire de contact
 */

declare(strict_types=1);
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/email_sender.php';

// Vérification du token CSRF
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    exit('Requête non autorisée.');
}

// Validation et nettoyage des entrées
function sanitize(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Récupération et validation des données
$errors = [];
$data = [];

// Nom (obligatoire)
$name = sanitize($_POST['name'] ?? '');
if (empty($name)) {
    $errors['name'] = 'Le nom est obligatoire.';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Le nom doit contenir au moins 2 caractères.';
} else {
    $data['name'] = $name;
}

// Email (obligatoire + validation)
$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errors['email'] = 'L\'adresse email est obligatoire.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'L\'adresse email n\'est pas valide.';
} else {
    $data['email'] = $email;
}

// Téléphone (obligatoire + regex)
$phone = sanitize($_POST['phone'] ?? '');
$phoneRegex = '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/';
if (empty($phone)) {
    $errors['phone'] = 'Le numéro de téléphone est obligatoire.';
} elseif (!preg_match($phoneRegex, $phone)) {
    $errors['phone'] = 'Le numéro de téléphone n\'est pas valide (format français).';
} else {
    $data['phone'] = $phone;
}

// Motif (obligatoire)
$motif = sanitize($_POST['motif'] ?? '');
$validMotifs = ['planification_complete', 'coaching_specifique', 'coordination_jour_j', 'outils_digitaux', 'consultation_personnalisee', 'autre'];
if (empty($motif)) {
    $errors['motif'] = 'Veuillez sélectionner un motif de contact.';
} elseif (!in_array($motif, $validMotifs)) {
    $errors['motif'] = 'Le motif sélectionné n\'est pas valide.';
} else {
    $data['motif'] = $motif;
}

// Date de mariage (obligatoire + validation)
$date_mariage = $_POST['date_mariage'] ?? '';
if (empty($date_mariage)) {
    $errors['date_mariage'] = 'La date de mariage est obligatoire.';
} else {
    $date_mariage_obj = DateTime::createFromFormat('Y-m-d', $date_mariage);
    if (!$date_mariage_obj) {
        $errors['date_mariage'] = 'La date de mariage n\'est pas valide.';
    } elseif ($date_mariage_obj < new DateTime()) {
        $errors['date_mariage'] = 'La date de mariage doit être dans le futur.';
    } else {
        $data['date_mariage'] = $date_mariage;
    }
}

// Message autre (si motif = autre)
$message_autre = sanitize($_POST['message_autre'] ?? '');
$data['message_autre'] = $message_autre;

// Validation du message autre (si motif = autre)
if (isset($data['motif']) && $data['motif'] === 'autre' && empty($message_autre)) {
    $errors['message_autre'] = 'Veuillez préciser votre demande.';
}

// Consentement (obligatoire)
$consent = isset($_POST['consent']) ? 1 : 0;
if (!$consent) {
    $errors['consent'] = 'Vous devez accepter les conditions d\'utilisation.';
}

// Autres champs (optionnels)
$data['message'] = sanitize($_POST['message'] ?? '');
$data['nombre_invites'] = (int)($_POST['nombre_invites'] ?? 0);
$data['lieu_mariage'] = sanitize($_POST['lieu_mariage'] ?? '');

// Si des erreurs existent, retourner JSON
if (!empty($errors)) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['errors' => $errors, 'data' => $data]);
    exit;
}

// Utiliser les variables validées
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$motif = $data['motif'];
$date_mariage = $data['date_mariage'];
$message = $data['message'];
$nombre_invites = $data['nombre_invites'];
$lieu_mariage = $data['lieu_mariage'];
$message_autre = $data['message_autre'];

// Détermination de la priorité selon la date de mariage
$date_mariage_obj = DateTime::createFromFormat('Y-m-d', $date_mariage);
$jours_restants = (new DateTime())->diff($date_mariage_obj)->days;
if ($jours_restants < 30) {
    $priorite = 'urgent';
} elseif ($jours_restants < 90) {
    $priorite = 'normal';
} else {
    $priorite = 'faible';
}

try {
    // Préparation de la requête (anti injection SQL)
    $stmt = $pdo->prepare("
        INSERT INTO contacts (
            nom, email, telephone, message, date_envoi,
            motif_contact, date_mariage, nombre_invites, lieu_mariage, 
            message_autre, priorite, ip_address, user_agent
        )
        VALUES (
            :nom, :email, :telephone, :message, NOW(),
            :motif, :date_mariage, :nombre_invites, :lieu_mariage,
            :message_autre, :priorite, :ip_address, :user_agent
        )
    ");
    $stmt->execute([
        ':nom'           => $name,
        ':email'         => $email,
        ':telephone'     => $phone,
        ':message'       => $message,
        ':motif'         => $motif,
        ':date_mariage'  => $date_mariage,
        ':nombre_invites'=> $nombre_invites > 0 ? $nombre_invites : null,
        ':lieu_mariage'  => $lieu_mariage ?: null,
        ':message_autre' => $message_autre ?: null,
        ':priorite'      => $priorite,
        ':ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
        ':user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

    // Nettoyage du token après utilisation
    unset($_SESSION['csrf_token']);

    // Envoi de l'email automatique personnalisé
    $contactData = [
        'nom' => $name,
        'email' => $email,
        'telephone' => $phone,
        'motif' => $motif,
        'date_mariage' => $date_mariage,
        'nombre_invites' => $nombre_invites,
        'lieu_mariage' => $lieu_mariage,
        'message_autre' => $message_autre,
        'message' => $message,
        'priorite' => $priorite
    ];
    
    $emailSent = sendAutomaticEmail($contactData);
    
    // Envoi de la notification à Nelly
    if ($emailSent) {
        sendNotificationToNelly($contactData);
    }
    
    // Mise à jour du statut d'envoi d'email
    if ($emailSent) {
        $updateStmt = $pdo->prepare("UPDATE contacts SET email_envoye = 1 WHERE email = :email ORDER BY date_envoi DESC LIMIT 1");
        $updateStmt->execute([':email' => $email]);
    }

    // Retourner succès JSON
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
} catch (PDOException $e) {
    // Gestion d'erreurs granulaire
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
    
    // Journalisation sécurisée sans fuite d'informations
    error_log('Erreur BDD lors de l\'insertion du contact - Code: ' . $errorCode . ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'inconnue'));
    
    // Retourner erreur JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur technique est survenue. Veuillez réessayer.']);
    exit;
} catch (Exception $e) {
    // Gestion des erreurs imprévues
    error_log('Erreur inattendue lors de l\'insertion du contact: ' . $e->getMessage());
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur inattendue est survenue. Veuillez réessayer.']);
    exit;
}
