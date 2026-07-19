<?php
// Views/signalements/index.php
// Variables depuis SignalementController->index() :
// $signalements, $categories, $votes_utilisateur, $connecte
// $statut_filtre, $categorie_filtre, $q_filtre

// Valeurs par défaut
$signalements      = $signalements      ?? [];
$categories        = $categories        ?? [];
$votes_utilisateur = $votes_utilisateur ?? [];
$connecte          = $connecte          ?? false;
$statut_filtre     = $statut_filtre     ?? '';
$categorie_filtre  = $categorie_filtre  ?? '';
$q_filtre          = $q_filtre          ?? '';

$categories_simplifiees = categoriesSignalementDisponibles($categories);
$ids_simplifies = array_column($categories_simplifiees, 'id');
if ($categorie_filtre !== '' && !in_array($categorie_filtre, $ids_simplifies, true)) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $categorie_filtre) {
            $categories_simplifiees[] = ['id' => $cat['id'], 'nom' => $cat['nom'], 'icone' => 'fa-tag'];
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signalements — Mia Dzra Do</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/signalements.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-signalements">
        <div class="conteneur">

            <!-- ══ EN-TÊTE ══ -->
            <div class="page-entete">
                <div>
                    <h1>Signalements</h1>
                    <p>
                        <?= count($signalements) ?> signalement<?= count($signalements) > 1 ? 's' : '' ?>
                        <?= $statut_filtre || $categorie_filtre || $q_filtre ? 'trouvé(s)' : 'au total' ?>
                    </p>
                </div>
                <?php if ($connecte): ?>
                    <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                        <i class="fa-solid fa-plus"></i> Signaler un problème
                    </a>
                <?php else: ?>
                    <a href="index.php?page=auth&action=login" class="btn-signaler btn-signaler-outline">
                        <i class="fa-solid fa-right-to-bracket"></i> Connexion pour signaler
                    </a>
                <?php endif; ?>
            </div>

            <!-- ══ FILTRES ══ -->
            <div class="barre-filtres">

                <!-- Filtres par statut -->
                <div class="filtres-statut">
                    <a href="index.php?page=signalements<?= $q_filtre ? '&q=' . urlencode($q_filtre) : '' ?><?= $categorie_filtre ? '&categorie_id=' . urlencode($categorie_filtre) : '' ?>"
                        class="filtre-statut <?= $statut_filtre === '' ? 'actif' : '' ?>">
                        Tous
                    </a>
                    <a href="index.php?page=signalements&statut=nouveau<?= $q_filtre ? '&q=' . urlencode($q_filtre) : '' ?><?= $categorie_filtre ? '&categorie_id=' . urlencode($categorie_filtre) : '' ?>"
                        class="filtre-statut <?= $statut_filtre === 'nouveau' ? 'actif' : '' ?>">
                        <span class="dot dot-rouge"></span> Nouveaux
                    </a>
                    <a href="index.php?page=signalements&statut=en_cours<?= $q_filtre ? '&q=' . urlencode($q_filtre) : '' ?><?= $categorie_filtre ? '&categorie_id=' . urlencode($categorie_filtre) : '' ?>"
                        class="filtre-statut <?= $statut_filtre === 'en_cours' ? 'actif' : '' ?>">
                        <span class="dot dot-orange"></span> En cours
                    </a>
                    <a href="index.php?page=signalements&statut=resolu<?= $q_filtre ? '&q=' . urlencode($q_filtre) : '' ?><?= $categorie_filtre ? '&categorie_id=' . urlencode($categorie_filtre) : '' ?>"
                        class="filtre-statut <?= $statut_filtre === 'resolu' ? 'actif' : '' ?>">
                        <span class="dot dot-vert"></span> Résolus
                    </a>
                </div>

                <!-- Recherche + catégorie -->
                <form method="GET" action="index.php" class="filtres-recherche">
                    <input type="hidden" name="page" value="signalements">
                    <?php if ($statut_filtre): ?>
                        <input type="hidden" name="statut" value="<?= htmlspecialchars($statut_filtre) ?>">
                    <?php endif; ?>

                    <div class="champ-recherche">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="q"
                            placeholder="Rechercher un signalement…"
                            value="<?= htmlspecialchars($q_filtre) ?>">
                    </div>

                    <select name="categorie_id">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories_simplifiees as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $categorie_filtre == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-filtrer">Filtrer</button>

                    <?php if ($statut_filtre || $categorie_filtre || $q_filtre): ?>
                        <a href="index.php?page=signalements" class="btn-reset" title="Effacer les filtres">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ══ RÉSULTATS ══ -->
            <?php if (empty($signalements)): ?>

                <div class="etat-vide">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>Aucun signalement trouvé</h3>
                    <p>
                        <?php if ($statut_filtre || $categorie_filtre || $q_filtre): ?>
                            Essayez de modifier vos filtres ou
                            <a href="index.php?page=signalements">voir tous les signalements</a>.
                        <?php else: ?>
                            Il n'y a encore aucun signalement. Soyez le premier à agir !
                        <?php endif; ?>
                    </p>
                    <?php if ($connecte): ?>
                        <a href="index.php?page=signalements&action=creer" class="btn-signaler">
                            <i class="fa-solid fa-plus"></i> Signaler un problème
                        </a>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <div class="liste-sig">
                    <?php foreach ($signalements as $s):
                        $badge     = badgeStatut($s['statut']);
                        $deja_vote = $votes_utilisateur[$s['id']] ?? false;
                        $desc      = trim($s['description'] ?? '');
                    ?>
                        <article class="carte-sig">

                            <!-- Zone cliquable → détail -->
                            <a href="index.php?page=signalements&action=voir&id=<?= $s['id'] ?>"
                                class="carte-lien">

                                <!-- Photo ou icône -->
                                <div class="carte-media">
                                    <?php if (!empty($s['photo_url'])): ?>
                                        <img src="<?= htmlspecialchars($s['photo_url']) ?>"
                                            alt="Photo du signalement">
                                    <?php else: ?>
                                        <?php $cat_simple = categorieSimplifiee($s['categorie_nom'] ?? ''); ?>
                                        <div class="carte-media-vide">
                                            <i class="fa-solid <?= htmlspecialchars($cat_simple['icone']) ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Contenu -->
                                <div class="carte-corps">
                                    <?php $cat_simple = categorieSimplifiee($s['categorie_nom'] ?? ''); ?>
                                    <div class="carte-top">
                                        <span class="carte-cat">
                                            <i class="fa-solid <?= htmlspecialchars($cat_simple['icone']) ?>"></i>
                                            <?= htmlspecialchars($cat_simple['nom']) ?>
                                        </span>

                                        <span class="badge <?= $badge['classe'] ?>"
                                            style="background-color: <?= $badge['couleur'] ?>1A;
             color: <?= $badge['couleur'] ?>;
             border: 1px solid <?= $badge['couleur'] ?>40;">
                                            <?= $badge['label'] ?>
                                        </span>
                                    </div>

                                    <h2 class="carte-titre"><?= htmlspecialchars($s['titre']) ?></h2>

                                    <?php if (!empty($desc)): ?>
                                        <p class="carte-desc">
                                            <?= htmlspecialchars(mb_substr($desc, 0, 110)) ?><?= mb_strlen($desc) > 110 ? '…' : '' ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="carte-meta">
                                        <span>
                                            <i class="fa-solid fa-location-dot"></i>
                                            <?= htmlspecialchars($s['quartier']) ?>, <?= htmlspecialchars($s['commune']) ?>
                                        </span>
                                        <span>
                                            <i class="fa-regular fa-clock"></i>
                                            <?= tempsRelatif($s['date_creation']) ?>
                                        </span>
                                        <span>
                                            <i class="fa-regular fa-comment"></i>
                                            <?= (int)($s['nb_commentaires'] ?? 0) ?> commentaire<?= (int)($s['nb_commentaires'] ?? 0) > 1 ? 's' : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </a>

                            <!-- Bouton vote — séparé du lien pour éviter les conflits -->
                            <div class="carte-vote">
                                <?php if ($connecte): ?>
                                    <form method="POST"
                                        action="index.php?page=signalements<?= $statut_filtre ? '&statut=' . urlencode($statut_filtre) : '' ?><?= $categorie_filtre ? '&categorie_id=' . urlencode($categorie_filtre) : '' ?><?= $q_filtre ? '&q=' . urlencode($q_filtre) : '' ?>">
                                        <input type="hidden" name="signalement_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn-vote <?= $deja_vote ? 'vote-actif' : '' ?>"
                                            title="<?= $deja_vote ? 'Retirer votre soutien' : 'Soutenir ce signalement' ?>">
                                            <i class="fa-solid fa-thumbs-up"></i>
                                            <span><?= (int)($s['nb_votes'] ?? 0) ?></span>
                                            <span class="vote-label"><?= $deja_vote ? 'Soutenu' : 'Soutenir' ?></span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="index.php?page=auth&action=login" class="btn-vote"
                                        title="Connectez-vous pour soutenir">
                                        <i class="fa-solid fa-thumbs-up"></i>
                                        <span><?= (int)($s['nb_votes'] ?? 0) ?></span>
                                        <span class="vote-label">Soutenir</span>
                                    </a>
                                <?php endif; ?>
                            </div>

                        </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

</body>

</html>