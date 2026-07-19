<?php
// Views/auth/inscription.php
// Ce fichier ne contient QUE l'affichage HTML
// Toute la logique est dans AuthController.php->register()
// Variables disponibles : $erreur, $etape, $nom, $tel, $email, $commune, $quartier

// ✅ Valeurs par défaut (sécurité si jamais le contrôleur oublie une variable)
$etape   = $etape   ?? 1;
$erreur  = $erreur  ?? '';
$nom     = $nom     ?? '';
$tel     = $tel     ?? '';
$email   = $email   ?? '';
$commune = $commune ?? '';
$quartier= $quartier?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — MonQuartier TG</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/inscription.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-auth">
        <div class="carte-auth">

            <!-- Logo -->
            <div class="auth-logo">
                <i class="fa-solid fa-map-location-dot"></i>
                <span><strong>MIA</strong> DZRA DO</span>
            </div>

            <h1 class="auth-titre">Créer un compte</h1>
            <p class="auth-sous-titre">
                Déjà inscrit ?
                <a href="index.php?page=auth&action=login">Se connecter</a>
            </p>

            <!-- Indicateur étapes -->
            <div class="etapes">
                <div class="etape <?= $etape === 1 ? 'etape-active' : 'etape-faite' ?>">
                    <div class="etape-num">
                        <?php if ($etape > 1): ?>
                            <i class="fa-solid fa-check"></i>
                        <?php else: ?>
                            1
                        <?php endif; ?>
                    </div>
                    <span>Identité</span>
                </div>
                <div class="etape-ligne <?= $etape > 1 ? 'ligne-faite' : '' ?>"></div>
                <div class="etape <?= $etape === 2 ? 'etape-active' : '' ?>">
                    <div class="etape-num">2</div>
                    <span>Localisation</span>
                </div>
            </div>

            <!-- Erreur -->
            <?php if ($erreur): ?>
                <div class="alerte-erreur">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <!-- ══ ÉTAPE 1 ══ -->
            <?php if ($etape === 1): ?>
                <form method="POST" action="index.php?page=auth&action=register">
                    <input type="hidden" name="action" value="aller_etape2">

                    <div class="champ">
                        <label><i class="fa-solid fa-user"></i> Nom complet *</label>
                        <input type="text" name="nom_complet" required
                            placeholder="Ex : Koffi Mensah"
                            value="<?= htmlspecialchars($nom) ?>">
                    </div>

                    <div class="champ">
                        <label><i class="fa-solid fa-phone"></i> Téléphone *</label>
                        <div class="tel-groupe">
                            <span class="tel-indicatif">🇹🇬 +228</span>
                            <input type="tel" name="telephone"
                                placeholder="90 00 00 00"
                                value="<?= htmlspecialchars($tel) ?>">
                        </div>
                    </div>

                    <div class="champ">
                        <label>
                            <i class="fa-solid fa-envelope"></i> Email
                            <span class="optionnel">(optionnel)</span>
                        </label>
                        <input type="email" name="email"
                            placeholder="exemple@gmail.com"
                            value="<?= htmlspecialchars($email) ?>">
                    </div>

                    <button type="submit" class="btn-submit">
                        Suivant <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <!-- ══ ÉTAPE 2 ══ -->
            <?php else: ?>
                <form method="POST" action="index.php?page=auth&action=register">
                    <input type="hidden" name="action" value="creer_compte">
                    <input type="hidden" name="nom_complet" value="<?= htmlspecialchars($nom) ?>">
                    <input type="hidden" name="telephone" value="<?= htmlspecialchars($tel) ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <div class="champ">
                        <label><i class="fa-solid fa-house"></i> Quartier</label>
                        <input type="text" name="quartier"
                            id="ins-quartier"
                            list="ins-liste-quartiers"
                            autocomplete="off"
                            placeholder="Ex : Adidogomé, Bè Kpota, Totsi…"
                            value="<?= htmlspecialchars($quartier) ?>"
                            oninput="insRemplirCommune(this.value)">
                        <datalist id="ins-liste-quartiers">
                            <option value="Adidogomé" data-commune="Golfe 1">
                            <option value="Agbalépédogan" data-commune="Golfe 1">
                            <option value="Bè Kpota" data-commune="Golfe 1">
                            <option value="Bè Gbadago" data-commune="Golfe 1">
                            <option value="Nyékonakpoè" data-commune="Golfe 1">
                            <option value="Tokoin" data-commune="Golfe 1">
                            <option value="Hanoukopé" data-commune="Golfe 1">
                            <option value="Hédzranawoé" data-commune="Golfe 1">
                            <option value="Akodesséwa" data-commune="Golfe 2">
                            <option value="Bè Afrikoko" data-commune="Golfe 2">
                            <option value="Gbossimé" data-commune="Golfe 2">
                            <option value="Djidjolé" data-commune="Golfe 2">
                            <option value="Agoe Nyivé" data-commune="Golfe 3">
                            <option value="Démakpoé" data-commune="Golfe 3">
                            <option value="Kégué" data-commune="Golfe 3">
                            <option value="Totsi" data-commune="Golfe 4">
                            <option value="Agoè" data-commune="Golfe 4">
                            <option value="Cacavéli" data-commune="Golfe 4">
                            <option value="Lomé 2000" data-commune="Golfe 4">
                            <option value="Baguida" data-commune="Golfe 5">
                            <option value="Kodjoviakopé" data-commune="Golfe 5">
                            <option value="Agoè Assiyéyé" data-commune="Agoè Nyivé 1">
                            <option value="Agoè Zongo" data-commune="Agoè Nyivé 1">
                            <option value="Vakpossito" data-commune="Agoè Nyivé 2">
                            <option value="Gbénvié" data-commune="Agoè Nyivé 3">
                            <option value="Zanguéra" data-commune="Agoè Nyivé 3">
                            <option value="Anfamé" data-commune="Agoè Nyivé 5">
                            <option value="Kpogan" data-commune="Agoè Nyivé 5">
                        </datalist>
                        <span class="champ-aide">
                            <i class="fa-solid fa-lightbulb"></i>
                            Tapez votre quartier — la commune se remplira automatiquement
                        </span>
                    </div>

                    <div class="champ">
                        <label><i class="fa-solid fa-map-location-dot"></i> Commune *</label>
                        <select name="commune" id="ins-commune" required>
                            <option value="">Choisir votre commune…</option>
                            <optgroup label="Grand Lomé">
                                <option value="Golfe 1" <?= $commune === 'Golfe 1'      ? 'selected' : '' ?>>Golfe 1</option>
                                <option value="Golfe 2" <?= $commune === 'Golfe 2'      ? 'selected' : '' ?>>Golfe 2</option>
                                <option value="Golfe 3" <?= $commune === 'Golfe 3'      ? 'selected' : '' ?>>Golfe 3</option>
                                <option value="Golfe 4" <?= $commune === 'Golfe 4'      ? 'selected' : '' ?>>Golfe 4</option>
                                <option value="Golfe 5" <?= $commune === 'Golfe 5'      ? 'selected' : '' ?>>Golfe 5</option>
                                <option value="Agoè Nyivé 1" <?= $commune === 'Agoè Nyivé 1' ? 'selected' : '' ?>>Agoè Nyivé 1</option>
                                <option value="Agoè Nyivé 2" <?= $commune === 'Agoè Nyivé 2' ? 'selected' : '' ?>>Agoè Nyivé 2</option>
                                <option value="Agoè Nyivé 3" <?= $commune === 'Agoè Nyivé 3' ? 'selected' : '' ?>>Agoè Nyivé 3</option>
                                <option value="Agoè Nyivé 4" <?= $commune === 'Agoè Nyivé 4' ? 'selected' : '' ?>>Agoè Nyivé 4</option>
                                <option value="Agoè Nyivé 5" <?= $commune === 'Agoè Nyivé 5' ? 'selected' : '' ?>>Agoè Nyivé 5</option>
                                <option value="Agoè Nyivé 6" <?= $commune === 'Agoè Nyivé 6' ? 'selected' : '' ?>>Agoè Nyivé 6</option>
                            </optgroup>
                            <optgroup label="Autres villes">
                                <option value="Tchaoudjo 1" <?= $commune === 'Tchaoudjo 1' ? 'selected' : '' ?>>Tchaoudjo 1 (Sokodé)</option>
                                <option value="Tchaoudjo 2" <?= $commune === 'Tchaoudjo 2' ? 'selected' : '' ?>>Tchaoudjo 2 (Sokodé)</option>
                                <option value="Kozah 1" <?= $commune === 'Kozah 1'     ? 'selected' : '' ?>>Kozah 1 (Kara)</option>
                                <option value="Kozah 2" <?= $commune === 'Kozah 2'     ? 'selected' : '' ?>>Kozah 2 (Kara)</option>
                                <option value="Ogou 1" <?= $commune === 'Ogou 1'      ? 'selected' : '' ?>>Ogou 1 (Atakpamé)</option>
                                <option value="Ogou 2" <?= $commune === 'Ogou 2'      ? 'selected' : '' ?>>Ogou 2 (Atakpamé)</option>
                            </optgroup>
                        </select>
                        <span class="champ-aide">
                            Si votre quartier n'est pas reconnu, choisissez directement votre commune ici.
                        </span>
                    </div>

                    <div class="champ">
                        <label><i class="fa-solid fa-lock"></i> Mot de passe *</label>
                        <input type="password" name="password" required
                            placeholder="Minimum 6 caractères">
                    </div>

                    <div class="champ">
                        <label><i class="fa-solid fa-lock"></i> Confirmer le mot de passe *</label>
                        <input type="password" name="confirm" required
                            placeholder="••••••••">
                    </div>

                    <label class="cgu">
                        <input type="checkbox" name="cgu" value="1">
                        J'accepte les
                        <a href="#">conditions d'utilisation</a>
                        et la protection de mes données.
                    </label>

                    <div class="btns-nav">
                        <a href="index.php?page=auth&action=register" class="btn-retour">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-user-plus"></i> Créer mon compte
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="auth-liens">
                <a href="index.php?page=accueil">← Retour au site</a>
            </div>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

    <script>
        var insQuartierCommune = {
            'Adidogomé': 'Golfe 1',
            'Agbalépédogan': 'Golfe 1',
            'Bè Kpota': 'Golfe 1',
            'Bè Gbadago': 'Golfe 1',
            'Nyékonakpoè': 'Golfe 1',
            'Tokoin': 'Golfe 1',
            'Hanoukopé': 'Golfe 1',
            'Hédzranawoé': 'Golfe 1',
            'Akodesséwa': 'Golfe 2',
            'Bè Afrikoko': 'Golfe 2',
            'Gbossimé': 'Golfe 2',
            'Djidjolé': 'Golfe 2',
            'Agoe Nyivé': 'Golfe 3',
            'Démakpoé': 'Golfe 3',
            'Kégué': 'Golfe 3',
            'Totsi': 'Golfe 4',
            'Agoè': 'Golfe 4',
            'Cacavéli': 'Golfe 4',
            'Lomé 2000': 'Golfe 4',
            'Baguida': 'Golfe 5',
            'Kodjoviakopé': 'Golfe 5',
            'Agoè Assiyéyé': 'Agoè Nyivé 1',
            'Agoè Zongo': 'Agoè Nyivé 1',
            'Vakpossito': 'Agoè Nyivé 2',
            'Gbénvié': 'Agoè Nyivé 3',
            'Zanguéra': 'Agoè Nyivé 3',
            'Anfamé': 'Agoè Nyivé 5',
            'Kpogan': 'Agoè Nyivé 5',
        };

        function insRemplirCommune(valeur) {
            var select = document.getElementById('ins-commune');
            if (!select) return;
            if (insQuartierCommune[valeur]) {
                // Sélectionner automatiquement la bonne commune
                for (var i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === insQuartierCommune[valeur]) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
        }
    </script>