/* =========================================================
   Univers des Mariés by NellyInsight
   script.js — Effets légers, sûrs et élégants
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  // 🔹 Sécurité : désactivation du JS inline dangereux
  "use strict";

  /* ==========================================
     1️⃣ Fade-in au scroll pour les sections
  ========================================== */
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.2 }
  );

  document.querySelectorAll("section").forEach((section) => {
    section.classList.add("hidden");
    observer.observe(section);
  });

  /* ==========================================
     2️⃣ Animation douce sur les liens d’ancrage
  ========================================== */
  const smoothLinks = document.querySelectorAll('a[href^="#"]');
  smoothLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      const targetId = link.getAttribute("href");
      const target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  /* ==========================================
     3️⃣ Animation de bouton au survol
  ========================================== */
  const buttons = document.querySelectorAll(".btn, button");
  buttons.forEach((btn) => {
    btn.addEventListener("mouseenter", () => {
      btn.style.transform = "scale(1.05)";
      btn.style.transition = "transform 0.2s ease";
    });
    btn.addEventListener("mouseleave", () => {
      btn.style.transform = "scale(1)";
    });
  });

  /* ==========================================
     4️⃣ Gestion du formulaire enrichi
  ========================================== */
  const motifSelect = document.getElementById("motif");
  const autreMotifDiv = document.getElementById("autre-motif");
  const messageAutreInput = document.getElementById("message_autre");

  if (motifSelect && autreMotifDiv) {
    motifSelect.addEventListener("change", () => {
      if (motifSelect.value === "autre") {
        autreMotifDiv.classList.remove("hidden");
        messageAutreInput.required = true;
      } else {
        autreMotifDiv.classList.add("hidden");
        messageAutreInput.required = false;
        messageAutreInput.value = "";
      }
    });
  }

  /* ==========================================
     5️⃣ Envoi AJAX et gestion d'erreurs
  ========================================== */
  const form = document.querySelector("form");
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault(); // Empêcher le rechargement de page

      const button = form.querySelector("button");
      const formData = new FormData(form);

      // Désactiver le bouton
      if (button) {
        button.disabled = true;
        button.textContent = "Envoi en cours...";
      }

      // Nettoyer les erreurs précédentes
      clearErrors();

      try {
        const response = await fetch("save_contact.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.text();

        if (response.ok) {
          // Succès - afficher message et rediriger
          showSuccessMessage();
          setTimeout(() => {
            window.location.href = "index.php?success=1";
          }, 2000);
        } else {
          // Erreur - parser la réponse pour extraire les erreurs
          try {
            const errorData = JSON.parse(result);
            displayErrors(errorData.errors || {});
          } catch (parseError) {
            // Si ce n'est pas du JSON, afficher l'erreur générique
            showGenericError("Une erreur est survenue. Veuillez réessayer.");
          }
        }
      } catch (error) {
        showGenericError("Erreur de connexion. Veuillez réessayer.");
      } finally {
        // Réactiver le bouton
        if (button) {
          button.disabled = false;
          button.textContent = "Envoyer ma demande";
        }
      }
    });
  }

  // Fonction pour nettoyer les erreurs
  function clearErrors() {
    // Supprimer tous les messages d'erreur existants
    document
      .querySelectorAll(".field-error")
      .forEach((error) => error.remove());

    // Supprimer les bordures rouges
    document.querySelectorAll("input, select, textarea").forEach((field) => {
      field.style.borderColor = "";
    });

    // Supprimer les messages généraux
    const existingMessage = document.querySelector(".message.error");
    if (existingMessage) {
      existingMessage.remove();
    }
  }

  // Fonction pour afficher les erreurs
  function displayErrors(errors) {
    Object.keys(errors).forEach((fieldName) => {
      const field = document.querySelector(`[name="${fieldName}"]`);
      if (field) {
        // Ajouter bordure rouge
        field.style.borderColor = "#dc3545";

        // Ajouter message d'erreur
        const errorDiv = document.createElement("div");
        errorDiv.className = "field-error";
        errorDiv.innerHTML = `❌ ${errors[fieldName]}`;

        // Insérer après le champ
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
      }
    });
  }

  // Fonction pour afficher une erreur générique
  function showGenericError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "message error";
    errorDiv.innerHTML = `<p>❌ ${message}</p>`;

    // Insérer avant le formulaire
    form.parentNode.insertBefore(errorDiv, form);
  }

  // Fonction pour afficher le message de succès
  function showSuccessMessage() {
    const successDiv = document.createElement("div");
    successDiv.className = "message success";
    successDiv.innerHTML =
      "<p>✅ Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais 💌</p>";

    // Insérer avant le formulaire
    form.parentNode.insertBefore(successDiv, form);

    // Désactiver le formulaire
    form
      .querySelectorAll("input, select, textarea, button")
      .forEach((field) => {
        field.disabled = true;
      });
  }
});
