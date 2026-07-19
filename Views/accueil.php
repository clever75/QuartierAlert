<?php
// Views/accueil.php
// Variables depuis AccueilController->index() : $categories, $derniers, $stats

$derniers   = $derniers   ?? [];
$stats      = $stats      ?? [];
$categories = $categories ?? [];

// Catégories simplifiées avec mapping BDD
$cats_accueil = [
    ['nom' => 'Voirie et routes',       'icone' => 'fa-road',          'id' => null, 'canon' => 'Voirie / Routes',         'noms_bdd' => ['Voirie / Routes']],
    ['nom' => 'Éclairage public',       'icone' => 'fa-lightbulb',    'id' => null, 'canon' => 'Éclairage public',       'noms_bdd' => ['Éclairage public', 'Électricité']],
    ['nom' => 'Collecte des ordures',   'icone' => 'fa-trash-can',    'id' => null, 'canon' => 'Déchets / Hygiène',       'noms_bdd' => ['Déchets / Hygiène']],
    ['nom' => 'Inondations',            'icone' => 'fa-water',        'id' => null, 'canon' => 'Assainissement',          'noms_bdd' => ['Assainissement']],
    ['nom' => 'Espaces verts',          'icone' => 'fa-leaf',         'id' => null, 'canon' => 'Espaces verts',           'noms_bdd' => ['Espaces verts']],
    ['nom' => 'Divers',                 'icone' => 'fa-ellipsis-h',   'id' => null, 'canon' => 'Autre',                   'noms_bdd' => ['Autre', 'Bâtiments publics']],
    ['nom' => 'Vandalisme',             'icone' => 'fa-shield-halved', 'id' => null, 'canon' => 'Sécurité',               'noms_bdd' => ['Sécurité']],
    ['nom' => 'Bruit et nuisances',     'icone' => 'fa-volume-high',  'id' => null, 'canon' => 'Sécurité',               'noms_bdd' => ['Sécurité']],
    ['nom' => 'Eau et assainissement', 'icone' => 'fa-droplet',      'id' => null, 'canon' => 'Eau potable',            'noms_bdd' => ['Eau potable']],
];
foreach ($cats_accueil as &$ca) {
    foreach ($categories as $cat) {
        if ($cat['nom'] === $ca['canon']) {
            $ca['id'] = $cat['id'];
            break;
        }
    }
    if ($ca['id'] === null) {
        foreach ($categories as $cat) {
            if (in_array($cat['nom'], $ca['noms_bdd'], true)) {
                $ca['id'] = $cat['id'];
                break;
            }
        }
    }
}
unset($ca);


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mia Dzra Do — Signaler un problème à Lomé</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/accueil.css">
</head>

<body>

    <?php require_once __DIR__ . '/../Layouts/header.php'; ?>

    <!-- ══ HERO ══════════════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-inner">

            <!-- Texte principal -->
            <div class="hero-texte">
                <span class="hero-tag">
                    <i class="fa-solid fa-location-dot"></i>&nbsp; Lomé, Togo
                </span>
                <h1>
                    Votre quartier<br>mérite mieux.
                </h1>
                <p>
                    Signalez un problème à la mairie en 1 minute.
                    Suivez son traitement. Voyez les résultats.
                </p>
                <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                    <i class="fa-solid fa-plus"></i>
                    Signaler maintenant
                </a>
            </div>

            <!-- Statistiques rapides -->
            <div class="hero-stats">
                <div class="stat-bloc">
                    <span class="stat-num"><?= (int)($stats['total'] ?? 0) ?></span>
                    <span class="stat-label">Signalements</span>
                </div>
                <div class="stat-sep"></div>
                <div class="stat-bloc">
                    <span class="stat-num"><?= (int)($stats['resolus'] ?? 0) ?></span>
                    <span class="stat-label">Résolus</span>
                </div>
                <div class="stat-sep"></div>
                <div class="stat-bloc">
                    <span class="stat-num"><?= (int)($stats['en_cours'] ?? 0) ?></span>
                    <span class="stat-label">En cours</span>
                </div>
            </div>

        </div>
    </section>

    <!-- ══ COMMENT ÇA MARCHE ═════════════════════════════════════ -->
    <section class="section-comment">
        <div class="conteneur">
            <h2 class="titre-section">Comment ça marche ?</h2>
            <p class="sous-titre">Simple, rapide, efficace</p>
            <div class="etapes">
                <div class="etape">
                    <div class="etape-icone"><i class="fa-solid fa-camera"></i></div>
                    <h3>1. Vous signalez</h3>
                    <p>Prenez une photo du problème, choisissez la catégorie et votre quartier. C'est tout.</p>
                </div>
                <div class="etape-fleche"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="etape">
                    <div class="etape-icone"><i class="fa-solid fa-paper-plane"></i></div>
                    <h3>2. La mairie reçoit</h3>
                    <p>Votre signalement est transmis aux services compétents (Mairie, CEET, TdE, DGTP, AGETUR, DGSCGC, Police Nationale / Gendarmerie).</p>
                </div>
                <div class="etape-fleche"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="etape">
                    <div class="etape-icone"><i class="fa-solid fa-circle-check"></i></div>
                    <h3>3. Vous suivez</h3>
                    <p>Vous recevez une notification à chaque mise à jour. Vous voyez quand le problème est résolu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ CATÉGORIES ════════════════════════════════════════════ -->
    <section class="section-categories">
        <div class="conteneur">
            <div class="entete-section">
                <div>
                    <h2 class="titre-section">Quel type de problème ?</h2>
                    <p class="sous-titre">Filtrez par catégorie</p>
                </div>
            </div>
            <div class="grille-cats">
                <?php foreach ($cats_accueil as $cat): ?>
                    <a href="index.php?page=signalements&categorie_id=<?= $cat['id'] ?>"
                        class="carte-cat">
                        <div class="cat-icone-wrap">
                            <i class="fa-solid <?= htmlspecialchars($cat['icone']) ?>"></i>
                        </div>
                        <span class="cat-nom"><?= htmlspecialchars($cat['nom']) ?></span>
                    </a>
                <?php endforeach; ?>
                <!-- Voir tous -->
                <a href="index.php?page=signalements" class="carte-cat carte-cat-tout">
                    <div class="cat-icone-wrap cat-icone-tout">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <span class="cat-nom">Voir tout</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ══ DERNIERS SIGNALEMENTS ═════════════════════════════════ -->
    <section class="section-signalements">
        <div class="conteneur">
            <div class="entete-section">
                <div>
                    <h2 class="titre-section">Signalements récents</h2>
                    <p class="sous-titre">Voyez ce que vos voisins ont signalé</p>
                </div>
                <a href="index.php?page=signalements" class="lien-tout">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($derniers)): ?>
                <div class="etat-vide">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Aucun signalement pour l'instant.<br>Soyez le premier à agir.</p>
                    <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                        <i class="fa-solid fa-plus"></i> Signaler un problème
                    </a>
                </div>
            <?php else: ?>
                <div class="liste-sig">
                    <?php foreach ($derniers as $s):
                        $b = badgeStatut($s['statut']);
                    ?>
                        <a href="index.php?page=signalements&action=voir&id=<?= $s['id'] ?>"
                            class="carte-sig">

                            <!-- Photo ou icône -->
                            <div class="sig-media">
                                <?php if (!empty($s['photo_url'])): ?>
                                    <img src="<?= htmlspecialchars($s['photo_url']) ?>" alt="photo du signalement">
                                <?php else: ?>
                                    <div class="sig-media-vide">
                                        <i class="fa-solid <?= htmlspecialchars($s['categorie_icone'] ?? 'fa-circle-exclamation') ?>"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Contenu -->
                            <div class="sig-contenu">
                                <div class="sig-top">
                                    <span class="sig-categorie">
                                        <i class="fa-solid <?= htmlspecialchars($s['categorie_icone'] ?? 'fa-tag') ?>"></i>
                                        <?= htmlspecialchars($s['categorie_nom'] ?? '') ?>
                                    </span>
                                    <span class="badge <?= $b['classe'] ?>"><?= $b['label'] ?></span>
                                </div>
                                <h3 class="sig-titre"><?= htmlspecialchars($s['titre']) ?></h3>
                                <p class="sig-lieu">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= htmlspecialchars($s['quartier']) ?>, <?= htmlspecialchars($s['commune']) ?>
                                </p>
                                <div class="sig-meta">
                                    <span><i class="fa-regular fa-clock"></i> <?= tempsRelatif($s['date_creation']) ?></span>
                                    <span><i class="fa-solid fa-thumbs-up"></i> <?= (int)($s['nb_votes'] ?? 0) ?></span>
                                    <span><i class="fa-regular fa-comment"></i> <?= (int)($s['nb_commentaires'] ?? 0) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="centre">
                    <a href="index.php?page=signalements" class="btn-contour">
                        Voir tous les signalements <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ══ APPEL À L'ACTION FINAL ════════════════════════════════ -->
    <section class="section-cta">
        <div class="conteneur">
            <div class="cta-bloc">
                <h2>Vous avez vu un problème dans votre quartier ?</h2>
                <p>Ne restez pas silencieux. Un signalement prend moins de 2 minutes et peut changer les choses pour tout un quartier.</p>
                <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                    <i class="fa-solid fa-plus"></i> Je signale maintenant
                </a>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../Layouts/footer.php'; ?>

</body>

</html>