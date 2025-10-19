<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : admin/messages.php
 * Objectif : Interface sécurisée d’administration des messages
 */

declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/db.php';

// =============================
// 🔒 Authentification sécurisée avec hash
// =============================

// Vérification de la session et timeout
$sessionTimeout = SESSION_LIFETIME ?? 7200; // 2 heures par défaut
if (!isset($_SESSION['logged_in']) || 
    (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionTimeout)) {
    
    // Nettoyer la session expirée
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionTimeout) {
        session_destroy();
        session_start();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        if (!empty($password) && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            header('Location: messages.php');
            exit;
        } else {
            $error = 'Mot de passe incorrect.';
            error_log('Tentative de connexion admin échouée depuis IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'inconnue'));
        }
    }

    // Formulaire de connexion minimal
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion Admin — Univers des Mariés</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
      <div class="admin-login">
        <h2>🔐 Accès Administrateur</h2>
        <?php if (!empty($error)): ?>
          <p class="admin-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
          <input type="password" name="password" placeholder="Mot de passe admin" required>
          <button type="submit">Se connecter</button>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// =============================
// 📄 Pagination & Récupération
// =============================
$messagesParPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $messagesParPage;

// Compter le nombre total
$totalStmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$totalMessages = (int)$totalStmt->fetchColumn();
$totalPages = max(1, ceil($totalMessages / $messagesParPage));

// Requête sécurisée
$stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY date_envoi DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $messagesParPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages reçus — Univers des Mariés</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <header class="admin-header">
    <h1 class="admin-title">Messages reçus 💌</h1>
    <form method="POST" action="logout.php">
      <button type="submit" class="admin-logout">Déconnexion</button>
    </form>
  </header>

  <section>
    <?php if (empty($messages)): ?>
      <div class="admin-empty">
        <p>Aucun message reçu pour le moment.</p>
      </div>
    <?php else: ?>
      <div class="admin-table-container">
        <table class="admin-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Date Mariage</th>
            <th>Priorité</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars($msg['nom']) ?></td>
              <td><?= htmlspecialchars($msg['email']) ?></td>
              <td><?= $msg['date_mariage'] ? date('d/m/Y', strtotime($msg['date_mariage'])) : '-' ?></td>
              <td>
                <span class="priority-badge priority-<?= $msg['priorite'] ?>">
                  <?= ucfirst($msg['priorite']) ?>
                </span>
              </td>
              <td>
                <button class="btn btn-view" onclick="openModal(<?= htmlspecialchars(json_encode($msg)) ?>)">
                  Voir
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i === $page): ?>
            <strong>[<?= $i ?>]</strong>
          <?php else: ?>
            <a href="?page=<?= $i ?>">[<?= $i ?>]</a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Modal -->
  <div id="messageModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3 id="modalTitle">Message</h3>
      <div id="modalBody"></div>
    </div>
  </div>

  <script>
    function openModal(msg) {
      const modal = document.getElementById('messageModal');
      const title = document.getElementById('modalTitle');
      const body = document.getElementById('modalBody');
      
      title.textContent = `Message de ${msg.nom}`;
      
      const motifs = {
        'planification_complete': 'Planification complète',
        'coaching_specifique': 'Coaching spécifique',
        'coordination_jour_j': 'Coordination jour J',
        'outils_digitaux': 'Outils digitaux',
        'consultation_personnalisee': 'Consultation',
        'autre': 'Autre'
      };
      
      body.innerHTML = `
        <div class="message-details">
          <p><strong>Email :</strong> ${msg.email}</p>
          <p><strong>Téléphone :</strong> ${msg.telephone || '-'}</p>
          <p><strong>Motif :</strong> ${motifs[msg.motif_contact] || msg.motif_contact}</p>
          <p><strong>Date de mariage :</strong> ${msg.date_mariage ? new Date(msg.date_mariage).toLocaleDateString('fr-FR') : '-'}</p>
          <p><strong>Nombre d'invités :</strong> ${msg.nombre_invites || '-'}</p>
          <p><strong>Lieu :</strong> ${msg.lieu_mariage || '-'}</p>
          <p><strong>Priorité :</strong> <span class="priority-badge priority-${msg.priorite}">${msg.priorite}</span></p>
          <p><strong>Date d'envoi :</strong> ${new Date(msg.date_envoi).toLocaleString('fr-FR')}</p>
          <hr>
          <h4>Message :</h4>
          ${msg.motif_contact === 'autre' && msg.message_autre ? 
            `<p><strong>Demande spécifique :</strong><br>${msg.message_autre.replace(/\n/g, '<br>')}</p>` : 
            ''
          }
          ${msg.message ? 
            `<p><strong>Message complémentaire :</strong><br>${msg.message.replace(/\n/g, '<br>')}</p>` : 
            '<p><em>Aucun message complémentaire</em></p>'
          }
        </div>
      `;
      
      modal.style.display = 'block';
    }
    
    function closeModal() {
      document.getElementById('messageModal').style.display = 'none';
    }
    
    // Fermer modal
    document.querySelector('.close').onclick = closeModal;
    window.onclick = function(event) {
      const modal = document.getElementById('messageModal');
      if (event.target === modal) closeModal();
    }
  </script>
</body>
</html>
