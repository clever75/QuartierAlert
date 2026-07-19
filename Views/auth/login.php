<?php
// ============================================================
//  Views/auth/login.php
//  ⚠️  CE FICHIER NE CONTIENT QUE DU HTML — zéro logique
//  Toute la logique est dans AuthController.php->login()
//
//  Variables injectées par le contrôleur :
//    $erreur  (string)  — message d'erreur ou ''
//    $otp     (bool)    — true = afficher l'étape OTP
// ============================================================

// Valeurs par défaut (sécurité si le contrôleur oublie une variable)
$erreur = $erreur ?? '';
$otp    = $otp    ?? false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $otp ? 'Vérification — ' : 'Connexion — ' ?>Mia Dzra Do</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/login.css">
</head>
<body>

<?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

<main class="auth-page">

    <!-- Panneau gauche : visuel -->
    <div class="auth-visuel" aria-hidden="true">
        <div class="auth-visuel-contenu">
            <div class="auth-logo-grand">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <p class="auth-slogan">
                La voix citoyenne<br>de votre quartier.
            </p>
            <p class="auth-pays">🇹🇬 Plateforme citoyenne togolaise</p>

            <!-- Chiffres en bas du panneau -->
            <div class="auth-chiffres">
                <div class="auth-chiffre">
                    <span class="chiffre-num">+1 200</span>
                    <span class="chiffre-label">Citoyens</span>
                </div>
                <div class="auth-chiffre">
                    <span class="chiffre-num">340</span>
                    <span class="chiffre-label">Problèmes résolus</span>
                </div>
                <div class="auth-chiffre">
                    <span class="chiffre-num">6</span>
                    <span class="chiffre-label">Communes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau droit : formulaire -->
    <div class="auth-formulaire">
        <div class="auth-carte">

            <!-- En-tête -->
            <a href="index.php?page=accueil" class="auth-retour">
                <i class="fa-solid fa-arrow-left"></i> Retour au site
            </a>

            <?php if (!$otp): ?>
            <!-- ════════════════════════════════════════════
                 ÉTAPE 1 — Connexion normale
                 Même formulaire pour citoyen ET admin.
                 Le contrôleur distingue les rôles après.
                 ════════════════════════════════════════════ -->

                <h1 class="auth-titre">Connexion</h1>
                <p class="auth-sous-titre">
                    Pas encore de compte ?
                    <a href="index.php?page=auth&action=register">S'inscrire gratuitement</a>
                </p>

                <?php if ($erreur): ?>
                <div class="auth-alerte" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=auth&action=login" novalidate>
                    <input type="hidden" name="action" value="login">

                    <div class="champ-groupe">
                        <label class="champ-label" for="identifiant">
                            Téléphone ou adresse e-mail
                        </label>
                        <div class="champ-icone">
                            <i class="fa-solid fa-user"></i>
                            <input
                                type="text"
                                id="identifiant"
                                name="identifiant"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="90 00 00 00 ou exemple@gmail.com"
                                value="<?= htmlspecialchars($_POST['identifiant'] ?? '') ?>"
                            >
                        </div>
                        <span class="champ-aide">
                            Entrez vos 8 chiffres (sans le +228) ou votre email.
                        </span>
                    </div>

                    <div class="champ-groupe">
                        <label class="champ-label" for="password">Mot de passe</label>
                        <div class="champ-icone champ-mdp">
                            <i class="fa-solid fa-lock"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            >
                            <button type="button" class="toggle-mdp" aria-label="Afficher le mot de passe" onclick="toggleMdp()">
                                <i class="fa-regular fa-eye" id="icone-oeil"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-connexion">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        Se connecter
                    </button>
                </form>

                <!--
                    NOTE PÉDAGOGIQUE :
                    On n'affiche PAS de lien "Espace admin" séparé.
                    L'admin utilise le même formulaire.
                    Le système détecte son rôle en base de données
                    et demande automatiquement le code OTP ensuite.
                    Séparer les formulaires est une mauvaise pratique
                    car ça donne une information aux attaquants.
                -->

            <?php else: ?>
            <!-- ════════════════════════════════════════════
                 ÉTAPE 2 — OTP (uniquement pour les admins)

                 Pourquoi cette étape existe ?
                 L'admin a accès à toutes les données des citoyens.
                 Si son mot de passe est volé, le code OTP envoyé
                 par SMS bloque quand même l'accès à l'intrus.
                 C'est la double authentification (2FA).

                 En production : le code est envoyé par SMS via
                 une API (Twilio, Vonage, etc.).
                 En développement : le code de test est 1234.
                 ════════════════════════════════════════════ -->

                <div class="otp-entete">
                    <div class="otp-icone">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h1 class="auth-titre">Vérification administrateur</h1>
                    <p class="auth-sous-titre">
                        Un code de sécurité a été envoyé par SMS
                        sur votre téléphone professionnel.
                        Saisissez-le ci-dessous pour accéder au tableau de bord.
                    </p>
                </div>

                <?php if ($erreur): ?>
                <div class="auth-alerte" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=auth&action=login" novalidate>
                    <input type="hidden" name="action" value="otp">

                    <div class="champ-groupe">
                        <label class="champ-label" for="code">Code de vérification (4 chiffres)</label>
                        <input
                            type="text"
                            id="code"
                            name="code"
                            required
                            autofocus
                            inputmode="numeric"
                            maxlength="4"
                            placeholder="· · · ·"
                            class="input-otp"
                            autocomplete="one-time-code"
                        >
                        <!-- En développement, on affiche le code de test clairement -->
                        <span class="champ-aide otp-aide">
                            <i class="fa-solid fa-flask"></i>
                            Mode développement — code de test : <strong>1234</strong>
                        </span>
                    </div>

                    <button type="submit" class="btn-connexion">
                        <i class="fa-solid fa-check-shield"></i>
                        Valider et accéder au tableau de bord
                    </button>
                </form>

                <div class="auth-liens-bas">
                    <a href="index.php?page=auth&action=login">
                        <i class="fa-solid fa-rotate-left"></i>
                        Ce n'est pas moi — recommencer
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

<script>
// Afficher / masquer le mot de passe
function toggleMdp() {
    var input = document.getElementById('password');
    var icone = document.getElementById('icone-oeil');
    if (input.type === 'password') {
        input.type = 'text';
        icone.className = 'fa-regular fa-eye-slash';
    } else {
        input.type = 'password';
        icone.className = 'fa-regular fa-eye';
    }
}

// OTP : espacement visuel automatique (· · · ·)
var inputOtp = document.getElementById('code');
if (inputOtp) {
    inputOtp.addEventListener('input', function () {
        // Ne garde que les chiffres
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
    });
}
</script>

</body>
</html>