<?php
// Views/admin/creer_agent.php
$erreur   = $erreur   ?? '';
$succes   = $succes   ?? '';
$services = $services ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un agent — Mia Dzra Do</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/admin.css">
    <link rel="stylesheet" href="Public/Css/creer_agent.css">
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">MDD</div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">Mia Dzra Do</span>
                <span class="sidebar-brand-role"><i class="fa-solid fa-crown"></i> Super Admin</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php?page=admin&action=dashboard" class="nav-item">
                <i class="fa-solid fa-gauge-high"></i><span>Tableau de bord</span>
            </a>
            <a href="index.php?page=admin&action=utilisateurs" class="nav-item">
                <i class="fa-solid fa-users"></i><span>Utilisateurs</span>
            </a>
            <a href="index.php?page=admin&action=creerAgent" class="nav-item active">
                <i class="fa-solid fa-user-plus"></i><span>Créer un agent</span>
            </a>
            <a href="index.php?page=signalements" class="nav-item">
                <i class="fa-solid fa-globe"></i><span>Site public</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['utilisateur_nom'] ?? 'A', 0, 1)) ?></div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-nom"><?= h($_SESSION['utilisateur_nom'] ?? '') ?></span>
                    <span class="sidebar-user-role"><?= h($_SESSION['role'] ?? '') ?></span>
                </div>
            </div>
            <a href="index.php?page=auth&action=logout" class="sidebar-logout" title="Déconnexion">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <div class="main-wrap">

        <header class="page-header">
            <div>
                <h1 class="page-titre">Créer un agent</h1>
                <p class="page-sous">Le compte sera immédiatement actif après création</p>
            </div>
            <span class="header-date">
                <i class="fa-regular fa-calendar"></i>
                <?= date('d M Y') ?>
            </span>
        </header>

        <a href="index.php?page=admin&action=utilisateurs" class="ca-back">
            <i class="fa-solid fa-arrow-left"></i> Retour aux utilisateurs
        </a>

        <div class="ca-card">

            <div class="ca-card-header">
                <div class="ca-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <div class="ca-card-title">Nouveau compte agent</div>
                    <div class="ca-card-sub">Accès limité au service assigné uniquement</div>
                </div>
            </div>

            <div class="ca-card-body">

                <?php if ($erreur): ?>
                    <div class="ca-alert ca-alert-err">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= h($erreur) ?>
                    </div>
                <?php endif; ?>

                <?php if ($succes): ?>
                    <div class="ca-alert ca-alert-ok">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= h($succes) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=admin&action=creerAgent" class="ca-form">

                    <fieldset class="ca-fieldset">
                        <legend class="ca-legend">Identité de l'agent</legend>

                        <div class="ca-champ ca-full">
                            <label for="ca-nom">
                                <i class="fa-solid fa-user"></i> Nom complet *
                            </label>
                            <input type="text" id="ca-nom" name="nom_complet"
                                placeholder="Prénom Nom de l'agent"
                                value="<?= h($_POST['nom_complet'] ?? '') ?>"
                                required autocomplete="off">
                        </div>

                        <div class="ca-row">
                            <div class="ca-champ">
                                <label for="ca-tel">
                                    <i class="fa-solid fa-phone"></i> Téléphone *
                                </label>
                                <div class="ca-input-prefix">
                                    <span class="ca-prefix">+228</span>
                                    <input type="text" id="ca-tel" name="telephone"
                                        placeholder="90123456" maxlength="8"
                                        value="<?= h($_POST['telephone'] ?? '') ?>"
                                        required autocomplete="off">
                                </div>
                                <span class="ca-hint">8 chiffres sans indicatif</span>
                            </div>
                            <div class="ca-champ">
                                <label for="ca-email">
                                    <i class="fa-solid fa-envelope"></i> Email
                                    <span class="ca-opt">— facultatif</span>
                                </label>
                                <input type="email" id="ca-email" name="email"
                                    placeholder="agent@mairie.tg"
                                    value="<?= h($_POST['email'] ?? '') ?>"
                                    autocomplete="off">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="ca-fieldset">
                        <legend class="ca-legend">Service assigné</legend>

                        <div class="ca-champ ca-full">
                            <label for="ca-service">
                                <i class="fa-solid fa-hard-hat"></i> Service *
                            </label>
                            <select id="ca-service" name="service" required>
                                <option value="">— Choisir le service —</option>
                                <?php foreach ($services as $key => $data): ?>
                                    <option value="<?= h($key) ?>"
                                        <?= ($_POST['service'] ?? '') === $key ? 'selected' : '' ?>>
                                        <?= h($data['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="ca-hint">
                                L'agent ne verra que les signalements de ce service.
                                La valeur entre parenthèses est celle stockée en base.
                            </span>
                        </div>

                        <!-- Grille services visuels — générée depuis $services BDD -->
                        <?php if (!empty($services)): ?>
                            <div class="ca-services-grid">
                                <?php foreach ($services as $key => $data): ?>
                                    <div class="ca-service-card" data-service="<?= h($key) ?>">
                                        <i class="fa-solid <?= h($data['icone']) ?>"></i>
                                        <span><?= h($key) ?></span>
                                        <small><?= h(implode(', ', $data['categories'])) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="ca-fieldset">
                        <legend class="ca-legend">Sécurité du compte</legend>

                        <div class="ca-row">
                            <div class="ca-champ">
                                <label for="ca-pwd">
                                    <i class="fa-solid fa-lock"></i> Mot de passe *
                                </label>
                                <div class="ca-pwd-wrap">
                                    <input type="password" id="ca-pwd" name="password"
                                        placeholder="Min. 8 caractères"
                                        required autocomplete="new-password">
                                    <button type="button" class="ca-eye" onclick="togglePwd('ca-pwd',this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="ca-champ">
                                <label for="ca-confirm">
                                    <i class="fa-solid fa-lock"></i> Confirmation *
                                </label>
                                <div class="ca-pwd-wrap">
                                    <input type="password" id="ca-confirm" name="confirm"
                                        placeholder="Répéter le mot de passe"
                                        required autocomplete="new-password">
                                    <button type="button" class="ca-eye" onclick="togglePwd('ca-confirm',this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="ca-info">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>
                            L'agent se connecte avec son numéro de téléphone.
                            Un code OTP est demandé à chaque connexion.
                        </span>
                    </div>

                    <button type="submit" class="ca-submit">
                        <i class="fa-solid fa-user-plus"></i>
                        Créer le compte agent
                    </button>

                </form>
            </div>
        </div>

    </div>

    <script>
        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        // Clic sur une carte service → sélectionne dans le select
        document.querySelectorAll('.ca-service-card').forEach(card => {
            card.addEventListener('click', () => {
                const val = card.dataset.service;
                const select = document.getElementById('ca-service');
                select.value = val;
                document.querySelectorAll('.ca-service-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            });
        });

        // Pré-sélectionner la carte si déjà choisi (après erreur de validation)
        const selectedService = '<?= h($_POST['service'] ?? '') ?>';
        if (selectedService) {
            const card = document.querySelector(`.ca-service-card[data-service="${selectedService}"]`);
            if (card) card.classList.add('selected');
        }
    </script>
</body>

</html>