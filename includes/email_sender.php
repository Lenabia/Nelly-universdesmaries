<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : includes/email_sender.php
 * Objectif : Système d'envoi d'emails automatiques personnalisés
 */

declare(strict_types=1);

// ✅ Sécurité : empêcher accès direct au fichier
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Accès direct interdit.');
}

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Envoie un email automatique personnalisé selon le motif de contact
 */
function sendAutomaticEmail(array $contactData): bool
{
    try {
        $mail = new PHPMailer(true);
        
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_USERNAME;
        $mail->Password   = GMAIL_APP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Expéditeur et destinataire
        $mail->setFrom(GMAIL_USERNAME, 'Nelly - Univers des Mariés');
        $mail->addAddress($contactData['email'], $contactData['nom']);
        $mail->addReplyTo(GMAIL_USERNAME, 'Nelly - Univers des Mariés');
        
        // Contenu de l'email selon le motif
        $emailContent = getEmailTemplate($contactData);
        
        $mail->isHTML(true);
        $mail->Subject = $emailContent['subject'];
        $mail->Body    = $emailContent['body'];
        $mail->AltBody = $emailContent['alt_body'];
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log('Erreur envoi email automatique : ' . $e->getMessage());
        return false;
    }
}

/**
 * Génère le template d'email selon le motif de contact
 */
function getEmailTemplate(array $data): array
{
    $motifs = [
        'planification_complete' => [
            'subject' => 'Votre mariage de rêve commence maintenant ! 💍',
            'template' => 'planification_complete'
        ],
        'coaching_specifique' => [
            'subject' => 'Votre point d\'organisation sera parfait ! ✨',
            'template' => 'coaching_specifique'
        ],
        'coordination_jour_j' => [
            'subject' => 'Nous nous occupons de tout le jour J ! 🎉',
            'template' => 'coordination_jour_j'
        ],
        'outils_digitaux' => [
            'subject' => 'Des outils sur mesure pour votre mariage ! 💻',
            'template' => 'outils_digitaux'
        ],
        'consultation_personnalisee' => [
            'subject' => 'Consultation personnalisée pour votre mariage ! 💫',
            'template' => 'consultation_personnalisee'
        ],
        'autre' => [
            'subject' => 'Merci pour votre message ! Nous étudions votre demande 💌',
            'template' => 'autre'
        ]
    ];
    
    $motif = $data['motif'] ?? 'consultation_personnalisee';
    $template = $motifs[$motif] ?? $motifs['consultation_personnalisee'];
    
    return [
        'subject' => $template['subject'],
        'body' => generateEmailBody($template['template'], $data),
        'alt_body' => generateEmailAltBody($template['template'], $data)
    ];
}

/**
 * Extrait le prénom d'un nom complet (gère les noms composés)
 */
function getFirstName(string $fullName): string
{
    $name = trim($fullName);
    $parts = explode(' ', $name);
    
    // Prendre le premier mot (même s'il contient un tiret pour les noms composés)
    return $parts[0];
}

/**
 * Génère les styles communs pour les emails
 */
function getEmailStyles(): array
{
    return [
        'container' => 'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #2b2b2b;',
        'title' => 'color: #5c4433; font-family: serif;',
        'highlight_box' => 'background: #f8f7f6; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #f5ad98;',
        'highlight_title' => 'color: #5c4433; margin-top: 0; font-family: serif;'
    ];
}

/**
 * Génère le corps HTML de l'email
 */
function generateEmailBody(string $template, array $data): string
{
    $styles = getEmailStyles();
    $firstName = getFirstName($data['nom']);
    
    $templates = [
        'planification_complete' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! 💍</h2>
                <p>Merci pour votre confiance ! Je suis ravie de vous accompagner dans la <strong>planification complète</strong> de votre mariage prévu le <strong>' . formatDate($data['date_mariage']) . '</strong>.</p>
                
                <p>Ensemble, nous allons créer un événement inoubliable qui reflète parfaitement votre histoire d\'amour ! Chaque détail sera pensé pour vous et vos proches.</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">Prochaines étapes :</h3>
                    <ul>
                        <li>📞 Je vous appelle dans les 24h pour échanger sur vos rêves</li>
                        <li>📋 Nous créons ensemble votre planning personnalisé</li>
                        <li>🎨 Nous définissons votre style et vos priorités</li>
                        <li>📅 Nous planifions tous les détails jusqu\'au jour J</li>
                    </ul>
                </div>
                
                <p>Je suis impatiente de découvrir votre vision et de transformer vos rêves en réalité !</p>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight<br>
                📧 nelly.moonlight9@gmail.com</p>
            </div>
        ',
        
        'coaching_specifique' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! ✨</h2>
                <p>Parfait ! Je vais vous aider à organiser ce <strong>point spécifique</strong> de votre mariage du <strong>' . formatDate($data['date_mariage']) . '</strong>.</p>
                
                <p>Chaque détail compte pour que votre jour J soit parfait. Mon approche personnalisée vous permettra de gérer cette étape avec sérénité et efficacité.</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">Je vous propose :</h3>
                    <ul>
                        <li>📞 Un appel de 30min pour cerner vos besoins précis</li>
                        <li>💡 Des conseils personnalisés et pratiques</li>
                        <li>📋 Un plan d\'action détaillé</li>
                        <li>📅 Un suivi jusqu\'au jour J si nécessaire</li>
                    </ul>
                </div>
                
                <p>Je suis là pour vous accompagner et vous rassurer à chaque étape !</p>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight</p>
            </div>
        ',
        
        'coordination_jour_j' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! 🎉</h2>
                <p>Excellent ! Je me charge de <strong>tout coordonner le jour J</strong> pour votre mariage du <strong>' . formatDate($data['date_mariage']) . '</strong>.</p>
                
                <p>Votre jour J doit être parfait, et c\'est exactement ce que je vais m\'assurer ! Vous pourrez vous concentrer sur l\'essentiel : vivre pleinement ce moment magique.</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">Le jour J, je m\'occupe de :</h3>
                    <ul>
                        <li>⏰ Gérer le timing de toute la journée</li>
                        <li>🤝 Coordonner tous les prestataires</li>
                        <li>👰 Vous accompagner dans les moments clés</li>
                        <li>🚨 Résoudre tous les imprévus</li>
                        <li>😌 Vous laisser profiter pleinement</li>
                    </ul>
                </div>
                
                <p>Laissez-moi gérer tous les détails techniques pour que vous puissiez savourer chaque instant !</p>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight</p>
            </div>
        ',
        
        'outils_digitaux' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! 💻</h2>
                <p>Super ! Je vais créer des <strong>outils digitaux sur mesure</strong> pour votre mariage du <strong>' . formatDate($data['date_mariage']) . '</strong>.</p>
                
                <p>La technologie peut vraiment simplifier l\'organisation de votre mariage. Je vais vous créer des outils modernes et élégants qui s\'adaptent parfaitement à votre style !</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">Mes créations pour vous :</h3>
                    <ul>
                        <li>📱 Planning interactif personnalisé</li>
                        <li>💌 Invitations digitales élégantes</li>
                        <li>📊 Tableaux de suivi des tâches</li>
                        <li>🗓️ Calendrier de mariage partagé</li>
                        <li>📝 Liste de mariage en ligne</li>
                        <li>🎨 Design cohérent avec votre style</li>
                    </ul>
                </div>
                
                <p>Des outils qui vous feront gagner du temps et vous permettront de vous concentrer sur l\'essentiel !</p>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight</p>
            </div>
        ',
        
        'consultation_personnalisee' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! 💫</h2>
                <p>Merci ! Je vais vous proposer une <strong>consultation personnalisée</strong> pour votre mariage du <strong>' . formatDate($data['date_mariage']) . '</strong>.</p>
                
                <p>Chaque mariage est unique, et c\'est pourquoi cette consultation sera entièrement adaptée à vos envies et à votre vision. Un moment privilégié pour échanger et vous conseiller au mieux !</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">Cette consultation inclut :</h3>
                    <ul>
                        <li>💬 Échange sur vos envies et priorités</li>
                        <li>🎯 Définition de votre vision du mariage</li>
                        <li>💡 Conseils personnalisés et pratiques</li>
                        <li>📋 Recommandations adaptées à votre projet</li>
                        <li>❓ Réponses à toutes vos questions</li>
                    </ul>
                </div>
                
                <p>Je suis là pour vous accompagner et vous aider à clarifier vos idées !</p>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight</p>
            </div>
        ',
        
        'autre' => '
            <div style="' . $styles['container'] . '">
                <h2 style="' . $styles['title'] . '">Bonjour ' . htmlspecialchars($firstName) . ' ! 💌</h2>
                <p>Merci pour votre message ! J\'ai bien reçu votre demande concernant : <em>"' . htmlspecialchars($data['message_autre']) . '"</em></p>
                
                <p>Chaque demande est unique et mérite une attention particulière. Je vais étudier votre demande avec soin et vous recontacter très rapidement pour échanger sur vos besoins spécifiques.</p>
                
                <div style="' . $styles['highlight_box'] . '">
                    <h3 style="' . $styles['highlight_title'] . '">En attendant :</h3>
                    <p>N\'hésitez pas à me faire part de toute question ou précision supplémentaire. Je suis là pour vous accompagner !</p>
                </div>
                
                <p>À très bientôt !<br>
                <strong>Nelly</strong><br>
                Univers des Mariés by NellyInsight</p>
            </div>
        '
    ];
    
    return $templates[$template] ?? $templates['consultation_personnalisee'];
}

/**
 * Génère la version texte de l'email
 */
function generateEmailAltBody(string $template, array $data): string
{
    $firstName = getFirstName($data['nom']);
    return "Bonjour " . $firstName . ",\n\n" .
           "Merci pour votre message concernant votre mariage du " . formatDate($data['date_mariage']) . ".\n\n" .
           "Je vous recontacte dans les plus brefs délais.\n\n" .
           "Cordialement,\nNelly - Univers des Mariés by NellyInsight";
}

/**
 * Formate une date en français
 */
function formatDate(string $date): string
{
    $months = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];
    
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj) return $date;
    
    return $dateObj->format('j') . ' ' . $months[(int)$dateObj->format('n')] . ' ' . $dateObj->format('Y');
}
