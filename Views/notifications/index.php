<?php
// Views/notifications/index.php
// Variables depuis NotificationController->index() :
// $notifications
$notifications = $notifications ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — MonQuartier TG</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/notifications.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-notifs">
        <div class="conteneur">

            <!-- En-tête -->
            <div class="page-entete">
                <div>
                    <h1><i class="fa-solid fa-bell"></i> Notifications</h1>
                    <p><?= count($notifications) ?> notification(s)</p>
                </div>
                <?php if (!empty($notifications)): ?>
                    <form method="POST" action="index.php?page=notifications&action=marquerLues">
                        <button type="submit" class="btn-tout-lire">
                            <i class="fa-solid fa-check-double"></i> Tout marquer comme lu
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Liste vide -->
            <?php if (empty($notifications)): ?>
                <div class="notifs-vide">
                    <i class="fa-regular fa-bell-slash"></i>
                    <h2>Aucune notification</h2>
                    <p>Vous serez alerté ici dès qu'il y a du nouveau sur vos signalements.</p>
                    <a href="index.php?page=signalements" class="btn-retour-sig">
                        <i class="fa-solid fa-list"></i> Voir les signalements
                    </a>
                </div>

            <?php else: ?>
                <div class="liste-notifs">
                    <?php foreach ($notifications as $n):

                        // Icône selon le type
                        $icones = [
                            'nouveau_signalement' => ['fa-flag',         'icone-bleu'],
                            'statut_change'       => ['fa-rotate',       'icone-orange'],
                            'nouveau_commentaire' => ['fa-comment',      'icone-vert'],
                            'resolu'              => ['fa-circle-check', 'icone-vert'],
                            'rejete'              => ['fa-circle-xmark', 'icone-rouge'],
                        ];
                        $icone = $icones[$n['type']] ?? ['fa-bell', 'icone-gris'];

                        // Temps relatif
                        $d = time() - strtotime($n['date_envoi']);
                        if ($d < 60)         $temps = 'À l\'instant';
                        elseif ($d < 3600)   $temps = 'Il y a ' . floor($d / 60) . ' min';
                        elseif ($d < 86400)  $temps = 'Il y a ' . floor($d / 3600) . ' h';
                        elseif ($d < 604800) $temps = 'Il y a ' . floor($d / 86400) . ' j';
                        else                 $temps = date('d/m/Y', strtotime($n['date_envoi']));

                    ?>
                        <div class="notif-item <?= (int)$n['est_lu'] === 0 ? 'non-lue' : '' ?>">

                            <!-- Icône type -->
                            <div class="notif-icone <?= $icone[1] ?>">
                                <i class="fa-solid <?= $icone[0] ?>"></i>
                            </div>

                            <!-- Contenu -->
                            <div class="notif-contenu">
                                <p class="notif-message"><?= htmlspecialchars($n['message']) ?></p>
                                <?php if (!empty($n['signalement_titre'])): ?>
                                    <p class="notif-sig">
                                        <i class="fa-solid fa-link"></i>
                                        <?= htmlspecialchars($n['signalement_titre']) ?>
                                    </p>
                                <?php endif; ?>
                                <span class="notif-temps">
                                    <i class="fa-regular fa-clock"></i>
                                    <?= $temps ?>
                                </span>
                            </div>

                            <!-- Lien vers le signalement -->
                            <?php if (!empty($n['signalement_id'])): ?>
                                <a href="index.php?page=signalements&action=voir&id=<?= (int)$n['signalement_id'] ?>"
                                    class="notif-lien">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Point non lu -->
                            <?php if ((int)$n['est_lu'] === 0): ?>
                                <span class="notif-point"></span>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>