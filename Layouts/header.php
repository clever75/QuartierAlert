<?php
// ============================================================
//  Layouts/header.php
// ============================================================

$connecte    = estConnecte();
$est_perso   = estPersonnel();
$nom_user    = $_SESSION['utilisateur_nom'] ?? '';
$page_active = $_GET['page'] ?? 'accueil';
$nb_notifs   = $_SESSION['nb_notifs'] ?? 0;

if (estSuperAdmin()) {
    $libelle_role = 'Super Admin';
} elseif (estAgent()) {
    $libelle_role = 'Agent — ' . libelleService(serviceAgent() ?? '');
} else {
    $libelle_role = 'Citoyen';
}
?>
<header class="header" id="header">
    <div class="header-inner">

        <!-- Logo -->
        <a href="index.php?page=accueil" class="logo">
            <i class="fa-solid fa-map-location-dot"></i>
            <span><strong>MIA</strong> DZRA DO</span>
        </a>

        <!-- Nav desktop -->
        <nav class="nav">
            <a href="index.php?page=accueil"
                class="nav-link <?= $page_active === 'accueil' ? 'actif' : '' ?>">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
            <a href="index.php?page=signalements"
                class="nav-link <?= $page_active === 'signalements' ? 'actif' : '' ?>">
                <i class="fa-solid fa-list"></i> Signalements
            </a>

            <?php if ($connecte): ?>

                <!-- Cloche notifications -->
                <a href="index.php?page=notifications" class="nav-link notif-wrap">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($nb_notifs > 0): ?>
                        <span class="notif-badge"><?= $nb_notifs ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($est_perso): ?>
                    <a href="index.php?page=admin&action=dashboard"
                        class="nav-link nav-dashboard <?= $page_active === 'admin' ? 'actif' : '' ?>">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                <?php endif; ?>

                <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                    <i class="fa-solid fa-plus"></i> Signaler
                </a>

                <!-- Menu profil -->
                <div class="profil-wrap">
                    <button class="btn-profil" onclick="toggleProfil()">
                        <i class="fa-solid fa-circle-user"></i>
                        <i class="fa-solid fa-chevron-down chevron-ico"></i>
                    </button>
                    <div class="profil-menu" id="profil-menu">
                        <div class="profil-nom"><?= h($nom_user) ?></div>
                        <div class="profil-role"><?= h($libelle_role) ?></div>
                        <hr class="profil-sep">
                        <?php if ($est_perso): ?>
                            <a href="index.php?page=admin&action=dashboard">
                                <i class="fa-solid fa-gauge"></i> Mon tableau de bord
                            </a>
                        <?php endif; ?>
                        <?php if (!$est_perso): ?>
                            <!-- Lien Mon espace — uniquement pour les citoyens -->
                            <a href="index.php?page=espace"
                                class="<?= $page_active === 'espace' ? 'actif' : '' ?>">
                                <i class="fa-solid fa-user-circle"></i> Mon espace
                            </a>
                        <?php endif; ?>
                        <a href="index.php?page=signalements&action=creer">
                            <i class="fa-solid fa-plus"></i> Nouveau signalement
                        </a>
                        <a href="index.php?page=notifications">
                            <i class="fa-solid fa-bell"></i> Notifications
                            <?php if ($nb_notifs > 0): ?>
                                <span class="menu-badge"><?= $nb_notifs ?></span>
                            <?php endif; ?>
                        </a>
                        <hr class="profil-sep">
                        <a href="index.php?page=auth&action=logout" class="lien-logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <a href="index.php?page=auth&action=login" class="nav-link">Connexion</a>
                <a href="index.php?page=auth&action=register" class="btn-inscrire">S'inscrire</a>
            <?php endif; ?>
        </nav>

        <!-- Burger mobile -->
        <button class="burger" id="burger" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Menu mobile -->
    <div class="mobile-menu" id="mobile-menu">
        <a href="index.php?page=accueil"><i class="fa-solid fa-house"></i> Accueil</a>
        <a href="index.php?page=signalements"><i class="fa-solid fa-list"></i> Signalements</a>
        <?php if ($connecte): ?>
            <?php if ($est_perso): ?>
                <a href="index.php?page=admin&action=dashboard">
                    <i class="fa-solid fa-gauge"></i> Mon tableau de bord
                </a>
            <?php else: ?>
                <!-- Mon espace — citoyens uniquement -->
                <a href="index.php?page=espace">
                    <i class="fa-solid fa-user-circle"></i> Mon espace
                </a>
            <?php endif; ?>
            <a href="index.php?page=signalements&action=creer">
                <i class="fa-solid fa-plus"></i> Signaler un problème
            </a>
            <a href="index.php?page=notifications">
                <i class="fa-solid fa-bell"></i> Notifications
                <?php if ($nb_notifs > 0): ?>(<?= $nb_notifs ?>)<?php endif; ?>
            </a>
            <a href="index.php?page=auth&action=logout" class="lien-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        <?php else: ?>
            <a href="index.php?page=auth&action=login">
                <i class="fa-solid fa-right-to-bracket"></i> Connexion
            </a>
            <a href="index.php?page=auth&action=register">
                <i class="fa-solid fa-user-plus"></i> S'inscrire
            </a>
        <?php endif; ?>
    </div>
</header>

<script>
    function toggleProfil() {
        var menu = document.getElementById('profil-menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.profil-wrap')) {
            var menu = document.getElementById('profil-menu');
            if (menu) menu.style.display = 'none';
        }
    });

    function toggleMenu() {
        document.getElementById('mobile-menu').classList.toggle('ouvert');
        document.getElementById('burger').classList.toggle('actif');
    }
    window.addEventListener('scroll', function() {
        document.getElementById('header').classList.toggle('fixe', window.scrollY > 40);
    });
</script>