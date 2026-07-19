<?php
// Views/signalements/detail.php
// Variables depuis SignalementController->voir() :
// $signalement, $commentaires, $nb_votes, $deja_vote, $connecte

// Valeurs par défaut
$signalement  = $signalement  ?? [];
$commentaires = $commentaires ?? [];
$nb_votes     = $nb_votes     ?? 0;
$deja_vote    = $deja_vote    ?? false;
$connecte     = $connecte     ?? false;

// ── Helpers ───────────────────────────────────────────────────
function badgeDetail(string $s): array
{
    return match ($s) {
        'nouveau'  => ['label' => 'Nouveau',  'class' => 'badge-nouveau'],
        'en_cours' => ['label' => 'En cours', 'class' => 'badge-encours'],
        'resolu'   => ['label' => 'Résolu',   'class' => 'badge-resolu'],
        'rejete'   => ['label' => 'Rejeté',   'class' => 'badge-rejete'],
        default    => ['label' => $s,         'class' => ''],
    };
}

function dateDetail(string $date): string
{
    $d = time() - strtotime($date);
    if ($d < 60)     return 'À l\'instant';
    if ($d < 3600)   return 'Il y a ' . floor($d / 60) . ' min';
    if ($d < 86400)  return 'Il y a ' . floor($d / 3600) . ' h';
    if ($d < 604800) return 'Il y a ' . floor($d / 86400) . ' j';
    return date('d/m/Y', strtotime($date));
}

// ── Données calculées ─────────────────────────────────────────
$badge = badgeStatut($signalement['statut'] ?? 'nouveau');

// Étapes de suivi selon le statut
$statut = $signalement['statut'] ?? 'nouveau';
$etapes = [
    ['label' => 'Signalement envoyé',     'icone' => 'fa-paper-plane',  'fait' => true],
    ['label' => 'Reçu par la mairie',     'icone' => 'fa-circle-check', 'fait' => in_array($statut, ['en_cours', 'resolu', 'rejete'])],
    ['label' => 'En cours de traitement', 'icone' => 'fa-gears',        'fait' => in_array($statut, ['en_cours', 'resolu'])],
    ['label' => 'Problème résolu',        'icone' => 'fa-check-double', 'fait' => $statut === 'resolu'],
];

// Lien WhatsApp pour partager
$lien_partage = urlencode(
    'Problème signalé à ' . ($signalement['quartier'] ?? '') .
        ' : ' . ($signalement['titre'] ?? '') .
        ' — ' . 'http://' . ($_SERVER['HTTP_HOST'] ?? '') .
        '/index.php?page=signalements&action=voir&id=' . ($signalement['id'] ?? '')
);

// Service responsable
$services_info = [
    'CEET'                  => ['icone' => 'fa-bolt',            'couleur' => '#F59E0B', 'nom_complet' => 'CEET — Compagnie Énergie Électrique du Togo',                          'tel' => '22 20 82 20'],
    'TDE'                   => ['icone' => 'fa-droplet',          'couleur' => '#3B82F6', 'nom_complet' => 'TdE — Togolaise des Eaux',                                             'tel' => '80 00 30 00'],
    'ANASAP'                => ['icone' => 'fa-trash-can',        'couleur' => '#8B5CF6', 'nom_complet' => 'ANASAP — Agence Nationale d\'Assainissement',                         'tel' => null],
    'Mairie'                => ['icone' => 'fa-building-columns', 'couleur' => '#006A4E', 'nom_complet' => 'Mairie de ' . ($signalement['commune'] ?? ''),                         'tel' => null],
    'DGTP'                  => ['icone' => 'fa-road',            'couleur' => '#1F8A70', 'nom_complet' => 'DGTP — Direction Générale des Travaux Publics',                        'tel' => null],
    'AGETUR'                => ['icone' => 'fa-city',            'couleur' => '#9333EA', 'nom_complet' => 'AGETUR — Agence d\'Exécution des Travaux Urbains',                   'tel' => null],
    'DGSCGC'                => ['icone' => 'fa-shield-halved',   'couleur' => '#D97706', 'nom_complet' => 'DGSCGC — Direction Générale de la Sécurité Civile et de la Gestion des Crises', 'tel' => null],
    'Police_Nationale_Gendarmerie' => ['icone' => 'fa-shield',  'couleur' => '#111827', 'nom_complet' => 'Police Nationale / Gendarmerie',                                      'tel' => null],
    'Police Nationale'      => ['icone' => 'fa-shield',          'couleur' => '#111827', 'nom_complet' => 'Police Nationale',                                                      'tel' => null],
    'Gendarmerie Nationale' => ['icone' => 'fa-shield',          'couleur' => '#1E293B', 'nom_complet' => 'Gendarmerie Nationale',                                                  'tel' => null],
];
$service_key  = $signalement['service_responsable'] ?? null;
$service_info = $services_info[$service_key] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($signalement['titre'] ?? 'Signalement') ?> — Mia Dzra Do</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/detail.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-detail">
        <div class="conteneur">

            <!-- Retour -->
            <a href="index.php?page=signalements" class="lien-retour">
                <i class="fa-solid fa-arrow-left"></i> Retour aux signalements
            </a>

            <div class="detail-grille">

                <!-- ══════════════════════════════════════
                 COLONNE PRINCIPALE (gauche)
            ══════════════════════════════════════ -->
                <div class="col-principale">

                    <!-- 1. Photo ou icône -->
                    <?php $cat_simple = categorieSimplifiee($signalement['categorie_nom'] ?? ''); ?>
                    <div class="bloc-media">
                        <?php if (!empty($signalement['photo_url'])): ?>
                            <img src="<?= htmlspecialchars($signalement['photo_url']) ?>"
                                alt="Photo du signalement"
                                class="media-photo">
                        <?php else: ?>
                            <div class="media-vide">
                                <i class="fa-solid <?= htmlspecialchars($cat_simple['icone']) ?>"></i>
                            </div>
                        <?php endif; ?>
                        <!-- Badge statut superposé -->
                        <span class="badge badge-superpose <?= $badge['classe'] ?>">
                            <?= $badge['label'] ?>
                        </span>
                    </div>

                    <!-- 2. Titre + métadonnées -->
                    <div class="bloc">
                        <div class="sig-cat">
                            <i class="fa-solid <?= htmlspecialchars($cat_simple['icone']) ?>"></i>
                            <?= htmlspecialchars($cat_simple['nom']) ?>
                        </div>
                        <h1 class="sig-titre"><?= htmlspecialchars($signalement['titre'] ?? '') ?></h1>
                        <div class="sig-meta">
                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                <?= htmlspecialchars($signalement['quartier'] ?? '') ?>,
                                <?= htmlspecialchars($signalement['commune'] ?? '') ?>
                            </span>
                            <span>
                                <i class="fa-regular fa-clock"></i>
                                <?= dateDetail($signalement['date_creation'] ?? 'now') ?>
                            </span>
                            <span>
                                <i class="fa-solid fa-user"></i>
                                <?= htmlspecialchars($signalement['citoyen_nom'] ?? '') ?>
                            </span>
                        </div>

                        <!-- Description -->
                        <?php if (!empty($signalement['description'])): ?>
                            <p class="sig-description">
                                <?= nl2br(htmlspecialchars($signalement['description'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Service responsable -->
                    <?php if ($service_info): ?>
                        <div class="bloc">
                            <h2 class="bloc-titre"><i class="fa-solid fa-building"></i> Service responsable</h2>
                            <div class="service-card"
                                style="border-left: 3px solid <?= $service_info['couleur'] ?>">
                                <div class="service-icone"
                                    style="background: <?= $service_info['couleur'] ?>20; color: <?= $service_info['couleur'] ?>">
                                    <i class="fa-solid <?= $service_info['icone'] ?>"></i>
                                </div>
                                <div>
                                    <div class="service-nom"><?= htmlspecialchars($service_info['nom_complet']) ?></div>
                                    <?php if ($service_info['tel']): ?>
                                        <a href="tel:<?= preg_replace('/\s/', '', $service_info['tel']) ?>"
                                            class="service-tel">
                                            <i class="fa-solid fa-phone"></i> <?= $service_info['tel'] ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 4. Photo de résolution (si problème résolu) -->
                    <?php if (!empty($signalement['photo_resolution'])): ?>
                        <div class="bloc bloc-resolu">
                            <div class="resolu-header">
                                <i class="fa-solid fa-circle-check"></i>
                                Problème résolu — voici la preuve
                            </div>
                            <img src="<?= htmlspecialchars($signalement['photo_resolution']) ?>"
                                alt="Preuve de résolution"
                                class="resolu-photo">
                        </div>
                    <?php endif; ?>

                    <!-- 5. Commentaires -->
                    <div class="bloc">
                        <h2 class="bloc-titre">
                            <i class="fa-solid fa-comments"></i>
                            Commentaires
                            <span class="nb-badge"><?= count($commentaires) ?></span>
                        </h2>

                        <?php if (empty($commentaires)): ?>
                            <p class="vide-msg">Aucun commentaire pour l'instant.</p>
                        <?php else: ?>
                            <div class="liste-commentaires">
                                <?php foreach ($commentaires as $c): ?>
                                    <div class="commentaire <?= $c['auteur_role'] === 'admin_mairie' ? 'com-officiel' : '' ?>">
                                        <div class="com-avatar">
                                            <?= mb_strtoupper(mb_substr($c['auteur_nom'], 0, 1)) ?>
                                        </div>
                                        <div class="com-corps">
                                            <div class="com-haut">
                                                <strong><?= htmlspecialchars($c['auteur_nom']) ?></strong>
                                                <?php if ($c['auteur_role'] === 'admin_mairie'): ?>
                                                    <span class="badge-mairie">
                                                        <i class="fa-solid fa-shield-halved"></i> Mairie
                                                    </span>
                                                <?php endif; ?>
                                                <span class="com-date"><?= dateDetail($c['date_pub']) ?></span>
                                            </div>
                                            <p><?= nl2br(htmlspecialchars($c['contenu'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulaire commentaire -->
                        <?php if ($connecte): ?>
                            <form method="POST"
                                action="index.php?page=signalements&action=voir&id=<?= $signalement['id'] ?>"
                                class="form-com">
                                <input type="hidden" name="action_commenter" value="1">
                                <textarea name="contenu" rows="3" required
                                    placeholder="Ajoutez une précision ou une observation…"></textarea>
                                <button type="submit" class="btn-envoyer">
                                    <i class="fa-solid fa-paper-plane"></i> Envoyer
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="invite-com">
                                <i class="fa-solid fa-lock"></i>
                                <a href="index.php?page=auth&action=login">Connectez-vous</a>
                                pour laisser un commentaire.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- ══════════════════════════════════════
                 COLONNE LATÉRALE (droite)
            ══════════════════════════════════════ -->
                <div class="col-laterale">

                    <!-- Soutenir -->
                    <div class="bloc text-centre">
                        <div class="vote-num"><?= $nb_votes ?></div>
                        <div class="vote-texte">
                            citoyen<?= $nb_votes > 1 ? 's' : '' ?> soutien<?= $nb_votes > 1 ? 'nent' : 't' ?> ce signalement
                        </div>
                        <?php if ($connecte): ?>
                            <form method="POST"
                                action="index.php?page=signalements&action=voir&id=<?= $signalement['id'] ?>">
                                <input type="hidden" name="action_vote" value="1">
                                <button type="submit"
                                    class="btn-vote <?= $deja_vote ? 'vote-actif' : '' ?>">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                    <?= $deja_vote ? 'Vous soutenez' : 'Je soutiens' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="index.php?page=auth&action=login" class="btn-vote">
                                <i class="fa-solid fa-thumbs-up"></i> Je soutiens
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Suivi du dossier -->
                    <div class="bloc">
                        <h2 class="bloc-titre"><i class="fa-solid fa-list-check"></i> Suivi</h2>
                        <div class="timeline">
                            <?php foreach ($etapes as $i => $etape): ?>
                                <div class="tl-item">
                                    <div class="tl-icone <?= $etape['fait'] ? 'tl-fait' : 'tl-attente' ?>">
                                        <i class="fa-solid <?= $etape['icone'] ?>"></i>
                                    </div>
                                    <?php if ($i < count($etapes) - 1): ?>
                                        <div class="tl-ligne <?= $etape['fait'] ? 'tl-ligne-faite' : '' ?>"></div>
                                    <?php endif; ?>
                                    <span class="tl-label <?= $etape['fait'] ? 'tl-label-fait' : '' ?>">
                                        <?= $etape['label'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Résumé rapide -->
                    <div class="bloc">
                        <h2 class="bloc-titre"><i class="fa-solid fa-circle-info"></i> Informations</h2>
                        <div class="resume">
                            <div class="resume-ligne">
                                <span>Statut</span>
                                <span class="badge <?= $badge['classe'] ?>"><?= $badge['label'] ?></span>
                            </div>
                            <div class="resume-ligne">
                                <span>Catégorie</span>
                                <span><?= htmlspecialchars($signalement['categorie_nom'] ?? '') ?></span>
                            </div>
                            <div class="resume-ligne">
                                <span>Commune</span>
                                <span><?= htmlspecialchars($signalement['commune'] ?? '') ?></span>
                            </div>
                            <?php if (!empty($signalement['resolu_at'])): ?>
                                <div class="resume-ligne">
                                    <span>Résolu le</span>
                                    <span><?= date('d/m/Y', strtotime($signalement['resolu_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Partager sur WhatsApp -->
                    <div class="bloc">
                        <h2 class="bloc-titre"><i class="fa-solid fa-share-nodes"></i> Partager</h2>
                        <a href="https://wa.me/?text=<?= $lien_partage ?>"
                            target="_blank" rel="noopener"
                            class="btn-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                            Partager sur WhatsApp
                        </a>
                        <p class="partager-aide">
                            Plus un signalement est partagé, plus vite il est traité.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

</body>

</html>