<?php
/**
 * Univers des Mariés by NellyInsight
 * Fichier : 404.php
 * Objectif : Page d'erreur 404 avec design cohérent
 */

// Définir le code de statut HTTP 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Page non trouvée - Univers des Mariés by NellyInsight">
  <title>Page non trouvée - Univers des Mariés by NellyInsight</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .error-container {
      text-align: center;
      padding: 4rem 2rem;
      min-height: 60vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .error-code {
      font-size: 8rem;
      font-family: var(--font-title);
      color: var(--accent);
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    .error-message {
      font-size: 1.5rem;
      color: var(--primary);
      margin-bottom: 2rem;
    }
    .error-description {
      font-size: 1.1rem;
      color: var(--dark);
      margin-bottom: 3rem;
      max-width: 600px;
      line-height: 1.6;
    }
    .back-links {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      justify-content: center;
    }
    .back-links a {
      background: var(--primary);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      text-decoration: none;
      transition: var(--transition);
    }
    .back-links a:hover {
      background: var(--highlight);
      transform: translateY(-2px);
    }
  </style>
</head>

<body>
  <!-- 🧭 HEADER -->
  <header>
    <h1>Univers des Mariés by NellyInsight</h1>
    <nav>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="index.php#apropos">À propos</a></li>
        <li><a href="index.php#services">Services</a></li>
        <li><a href="index.php#contact">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- 🚫 PAGE D'ERREUR 404 -->
  <main class="error-container">
    <div class="error-code">404</div>
    <h2 class="error-message">Oups ! Page introuvable</h2>
    <p class="error-description">
      La page que vous recherchez semble avoir disparu dans les limbes de l'organisation de mariage. 
      Ne vous inquiétez pas, même les meilleurs planificateurs perdent parfois le fil ! 
      Retournons ensemble sur le bon chemin.
    </p>
    
    <div class="back-links">
      <a href="index.php" aria-label="Retour à la page d'accueil">
        🏠 Retour à l'accueil
      </a>
      <a href="index.php#services" aria-label="Découvrir nos services">
        💍 Nos services
      </a>
      <a href="index.php#contact" aria-label="Nous contacter">
        📞 Nous contacter
      </a>
    </div>
  </main>

  <!-- 🦶 FOOTER -->
  <footer>
    <p>&copy; <?= date('Y'); ?> Univers des Mariés by NellyInsight — Tous droits réservés.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>
