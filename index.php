<?php
// Protection de session pour token CSRF
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Univers des Mariés by NellyInsight - Planification de mariage et coaching personnalisé.">
  <title>Univers des Mariés by NellyInsight</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <!-- 🧭 HEADER -->
  <header>
    <h1>Univers des Mariés by NellyInsight</h1>
    <button class="burger" aria-label="Menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <nav class="nav-mobile">
      <ul>
        <li><a href="#accueil">Accueil</a></li>
        <li><a href="#apropos">À propos</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- 🏠 HERO -->
  <section id="accueil" class="hero">
    <div class="hero-image">
      <img src="img/coaching.webp" alt="Coaching et planification de mariage" class="hero-img">
    </div>
    <div class="hero-content">
      <h2>Votre mariage, notre passion</h2>
      <p>Planifiez votre rêve avec nous</p>
      <a href="#services" class="btn">Nos services</a>
    </div>
  </section>

  <!-- 💡 À PROPOS -->
  <section id="apropos">
    <div class="about-container">
      <div class="about-text">
        <h3>Votre experte mariage</h3>
        <p class="about-intro">
          De la stratégie digitale à l'organisation émotionnelle… il n'y a eu qu'un pas — et je l'ai franchi.
        </p>
        <p>
          J'ai évolué plusieurs années dans le monde du digital : d'abord commerciale, puis développeuse web, avant de devenir cheffe de projet IT.<br>
          Autant te dire que gérer des deadlines, du stress et des prestataires, je connais !
        </p>
        <p>
          <strong>Spoiler :</strong> c'est exactement ce qu'il faut pour organiser un mariage 😅<br>
          Mais j'avais envie de plus de sens, plus d'humain, plus de magie.
        </p>
        <p>
          Alors j'ai décidé de mettre mon expertise tech et ma rigueur de gestionnaire au service d'un jour unique : le tien.
        </p>
        <p>
          <strong>Aujourd'hui, je suis Wedding Emotional Planner.<br>
          Et mon job, c'est de te faire respirer.</strong>
        </p>
        <div class="about-highlights">
          <p>💻 <strong>Mon parcours digital ?</strong> Il t'aide à tout structurer simplement, même sans aimer les tableaux Excel.</p>
          <p>📋 <strong>Ma casquette cheffe de projet ?</strong> Elle sécurise chaque étape, pour que rien ne te dépasse.</p>
          <p>🧘🏾‍♀️ <strong>Ma vision humaine ?</strong> Elle t'accompagne avec douceur, au bon rythme, sans pression.</p>
        </div>
        <p class="about-conclusion">
          Parce qu'un beau mariage, ce n'est pas que du beau — c'est surtout de la paix, de la clarté, et beaucoup de joie.
        </p>
        <a href="#contact" class="btn">Contactez-moi</a>
      </div>
      <div class="about-image">
        <img src="img/Elegance-Pictures-60.webp" alt="Nelly - Wedding Emotional Planner" class="about-img">
      </div>
    </div>
  </section>

  <!-- 💍 SERVICES -->
  <section id="services">
    <h3>Nos services</h3>
    <div class="services">
      <div class="service-card">
        <img src="img/book.webp" alt="Planification de mariage" class="service-icon">
        <h4>Planification de mariage</h4>
        <p>Transformez vos rêves en réalité.</p>
      </div>
      <div class="service-card">
        <img src="img/coach.webp" alt="Coaching organisation mariage" class="service-icon">
        <h4>Coaching organisation mariage</h4>
        <p>Accompagnement personnalisé pour votre mariage.</p>
      </div>
      <div class="service-card">
        <img src="img/coordination.webp" alt="Coordination de mariage" class="service-icon">
        <h4>Coordination de mariage</h4>
        <p>Assistance le jour J pour une journée sans stress.</p>
      </div>
      <div class="service-card">
        <img src="img/ordi.webp" alt="Outils digitaux pour mariage" class="service-icon">
        <h4>Outils digitaux pour mariage</h4>
        <p>Des solutions innovantes pour votre planification.</p>
      </div>
    </div>
  </section>
  

  <!-- 📞 CONTACT -->
  <section id="contact">
    <h3>Restons en contact</h3>
    <p>Faites-nous part de vos projets 💌</p>

    <form action="save_contact.php" method="POST" novalidate role="form" aria-label="Formulaire de contact enrichi">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <!-- Informations personnelles -->
      <div class="form-section">
        <h4>Vos informations</h4>
        
        <label for="name">Nom *</label>
        <input type="text" id="name" name="name" placeholder="Jane Smith" required maxlength="100" 
               aria-describedby="name-help" aria-required="true">

        <label for="email">Adresse e-mail *</label>
        <input type="email" id="email" name="email" placeholder="email@website.com" required maxlength="150" 
               aria-describedby="email-help" aria-required="true">

        <label for="phone">Téléphone *</label>
        <input type="tel" id="phone" name="phone" placeholder="06 12 34 56 78" required maxlength="20" 
               aria-describedby="phone-help" aria-required="true">
      </div>

      <!-- Informations sur le mariage -->
      <div class="form-section">
        <h4>Votre projet de mariage</h4>
        
        <label for="motif">Motif de votre contact *</label>
        <select id="motif" name="motif" required aria-required="true" aria-describedby="motif-help">
          <option value="">Sélectionnez votre besoin</option>
          <option value="planification_complete">Planification complète (A à Z)</option>
          <option value="coaching_specifique">Coaching organisationnel (points spécifiques)</option>
          <option value="coordination_jour_j">Coordination jour J uniquement</option>
          <option value="outils_digitaux">Outils digitaux (planning, invitations, etc.)</option>
          <option value="consultation_personnalisee">Consultation personnalisée</option>
          <option value="autre">Autre (préciser ci-dessous)</option>
        </select>

        <div id="autre-motif" class="hidden">
          <label for="message_autre">Précisez votre demande *</label>
          <input type="text" id="message_autre" name="message_autre" placeholder="Décrivez votre besoin spécifique" 
                 maxlength="200" aria-describedby="autre-help">
        </div>

        <label for="date_mariage">Date de votre mariage *</label>
        <input type="date" id="date_mariage" name="date_mariage" required aria-required="true" 
               aria-describedby="date-help" min="<?= date('Y-m-d') ?>">

        <label for="nombre_invites">Nombre d'invités (approximatif)</label>
        <input type="number" id="nombre_invites" name="nombre_invites" placeholder="50" min="1" max="1000" 
               aria-describedby="invites-help">

        <label for="lieu_mariage">Lieu prévu du mariage</label>
        <input type="text" id="lieu_mariage" name="lieu_mariage" placeholder="Ville, région ou lieu spécifique" 
               maxlength="200" aria-describedby="lieu-help">
      </div>

      <!-- Message libre -->
      <div class="form-section">
        <h4>Votre message</h4>
        <label for="message">Message libre</label>
        <textarea id="message" name="message" rows="5" placeholder="Décrivez vos rêves, vos questions, vos envies..." 
                  aria-describedby="message-help"></textarea>
      </div>

      <!-- Consentement -->
      <div>
        <label for="consent">
          <input type="checkbox" id="consent" name="consent" required aria-required="true">
          J'autorise ce site à stocker mes informations afin de répondre à ma demande.
        </label>
      </div>

      <button type="submit" aria-describedby="submit-help">Envoyer ma demande</button>
    </form>

    <div class="contact-details">
      <div class="contact-item">
        <span class="contact-icon">📍</span>
        <span>France Métropolitaine</span>
      </div>
      <div class="contact-item">
        <span class="contact-icon">💬</span>
        <span>Réponse sous 24h</span>
      </div>
      <div class="contact-item">
        <span class="contact-icon">✨</span>
        <span>Accompagnement personnalisé</span>
      </div>
    </div>
  </section>

  <!-- 🦶 FOOTER -->
  <footer>
    <p>&copy; <?= date('Y'); ?> Univers des Mariés by NellyInsight — Tous droits réservés.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>
