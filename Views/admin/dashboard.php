<?php
// ============================================================
//  Views/admin/dashboard.php
// ============================================================
$signalements = $signalements ?? [];
$categories   = $categories   ?? [];
$stats        = $stats        ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord — Mia Dzra Do</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/admin.css">
</head>

<body>

    <!-- ══ SIDEBAR ══════════════════════════════════════════════════ -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">MDD</div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">Mia Dzra Do</span>
                <span class="sidebar-brand-role">
                    <?php if (estSuperAdmin()): ?>
                        <i class="fa-solid fa-crown"></i> Super Admin
                    <?php else: ?>
                        <i class="fa-solid fa-user-tie"></i> Agent — <?= h(libelleService($_SESSION['service'] ?? '')) ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php?page=admin&action=dashboard" class="nav-item active">
                <i class="fa-solid fa-gauge-high"></i><span>Tableau de bord</span>
            </a>
            <?php if (estSuperAdmin()): ?>
                <a href="index.php?page=admin&action=utilisateurs" class="nav-item">
                    <i class="fa-solid fa-users"></i><span>Utilisateurs</span>
                </a>
                <a href="index.php?page=admin&action=creerAgent" class="nav-item">
                    <i class="fa-solid fa-user-plus"></i><span>Créer un agent</span>
                </a>
            <?php endif; ?>
            <a href="index.php?page=signalements" class="nav-item">
                <i class="fa-solid fa-globe"></i><span>Site public</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr($_SESSION['utilisateur_nom'] ?? 'A', 0, 1)) ?>
                </div>
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

    <!-- ══ CONTENU PRINCIPAL ════════════════════════════════════════ -->
    <div class="main-wrap">

        <header class="page-header">
            <div>
                <h1 class="page-titre">Tableau de bord</h1>
                <p class="page-sous">
                    <?php if (estSuperAdmin()): ?>
                        Vue globale de tous les signalements
                    <?php else: ?>
                        Signalements — <?= h(libelleService($_SESSION['service'] ?? '')) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="page-header-actions">
                <span class="header-date">
                    <i class="fa-regular fa-calendar"></i>
                    <?= date('d M Y') ?>
                </span>
            </div>
        </header>

        <!-- KPIs -->
        <div class="kpi-grille">
            <div class="kpi kpi-nouveau">
                <div class="kpi-icone"><i class="fa-solid fa-circle-dot"></i></div>
                <div class="kpi-corps">
                    <div class="kpi-chiffre"><?= (int)($stats['nouveaux'] ?? 0) ?></div>
                    <div class="kpi-label">Nouveaux</div>
                </div>
                <div class="kpi-barre"></div>
            </div>
            <div class="kpi kpi-encours">
                <div class="kpi-icone"><i class="fa-solid fa-arrows-spin"></i></div>
                <div class="kpi-corps">
                    <div class="kpi-chiffre"><?= (int)($stats['en_cours'] ?? 0) ?></div>
                    <div class="kpi-label">En cours</div>
                </div>
                <div class="kpi-barre"></div>
            </div>
            <div class="kpi kpi-resolu">
                <div class="kpi-icone"><i class="fa-solid fa-circle-check"></i></div>
                <div class="kpi-corps">
                    <div class="kpi-chiffre"><?= (int)($stats['resolus'] ?? 0) ?></div>
                    <div class="kpi-label">Résolus</div>
                </div>
                <div class="kpi-barre"></div>
            </div>
            <div class="kpi kpi-total">
                <div class="kpi-icone"><i class="fa-solid fa-layer-group"></i></div>
                <div class="kpi-corps">
                    <div class="kpi-chiffre"><?= (int)($stats['total'] ?? 0) ?></div>
                    <div class="kpi-label">Total</div>
                </div>
                <div class="kpi-barre"></div>
            </div>
        </div>

        <!-- FILTRES -->
        <section class="section-card">
            <div class="section-card-header">
                <h2><i class="fa-solid fa-sliders"></i> Filtres</h2>
            </div>
            <form method="GET" action="index.php" class="filtres-form">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="action" value="dashboard">

                <div class="filtre-groupe">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="">Tous</option>
                        <option value="nouveau" <?= ($_GET['statut'] ?? '') === 'nouveau'  ? 'selected' : '' ?>>Nouveau</option>
                        <option value="en_cours" <?= ($_GET['statut'] ?? '') === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="resolu" <?= ($_GET['statut'] ?? '') === 'resolu'   ? 'selected' : '' ?>>Résolu</option>
                        <option value="rejete" <?= ($_GET['statut'] ?? '') === 'rejete'   ? 'selected' : '' ?>>Rejeté</option>
                    </select>
                </div>

                <?php if (estSuperAdmin()): ?>
                    <div class="filtre-groupe">
                        <label>Catégorie</label>
                        <select name="categorie_id">
                            <option value="">Toutes</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"
                                    <?= ($_GET['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= h($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="filtre-groupe">
                    <label>Priorité</label>
                    <select name="priorite">
                        <option value="">Toutes</option>
                        <option value="urgente" <?= ($_GET['priorite'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                        <option value="haute" <?= ($_GET['priorite'] ?? '') === 'haute'   ? 'selected' : '' ?>>Haute</option>
                        <option value="normale" <?= ($_GET['priorite'] ?? '') === 'normale' ? 'selected' : '' ?>>Normale</option>
                        <option value="basse" <?= ($_GET['priorite'] ?? '') === 'basse'   ? 'selected' : '' ?>>Basse</option>
                    </select>
                </div>

                <div class="filtre-groupe filtre-recherche">
                    <label>Recherche</label>
                    <div class="input-avec-icone">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="q"
                            placeholder="Titre, quartier…"
                            value="<?= h($_GET['q'] ?? '') ?>">
                    </div>
                </div>

                <div class="filtre-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-magnifying-glass"></i> Filtrer
                    </button>
                    <a href="index.php?page=admin&action=dashboard" class="btn-ghost">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                </div>
            </form>
        </section>

        <!-- TABLEAU -->
        <section class="section-card">
            <div class="section-card-header">
                <h2>
                    <i class="fa-solid fa-list-check"></i> Signalements
                    <span class="count-badge"><?= count($signalements) ?></span>
                </h2>
            </div>

            <div class="tableau-wrap">
                <table class="tableau">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Signalement</th>
                            <th>Catégorie</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Votes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($signalements)): ?>
                            <tr>
                                <td colspan="8" class="td-vide">
                                    <i class="fa-solid fa-inbox"></i>
                                    <span>Aucun signalement trouvé</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($signalements as $s): ?>
                                <tr>
                                    <td class="td-date">
                                        <span><?= date('d/m/Y', strtotime($s['date_creation'])) ?></span>
                                        <small><?= date('H:i', strtotime($s['date_creation'])) ?></small>
                                    </td>
                                    <td class="td-lieu">
                                        <span><?= h($s['quartier']) ?></span>
                                        <small><?= h($s['commune']) ?></small>
                                    </td>
                                    <td class="td-titre">
                                        <div class="titre-wrap">
                                            <?php if (!empty($s['photo_url'])): ?>
                                                <img src="<?= h($s['photo_url']) ?>" alt="" class="vignette">
                                            <?php else: ?>
                                                <div class="vignette-vide">
                                                    <i class="fa-solid <?= h($s['categorie_icone'] ?? 'fa-circle-exclamation') ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            <!-- ✅ Lien vers la page détail (plus de modal) -->
                                            <a href="index.php?page=admin&action=voirSignalement&id=<?= $s['id'] ?>" class="titre-lien">
                                                <?= h(mb_substr($s['titre'], 0, 45)) ?><?= mb_strlen($s['titre']) > 45 ? '…' : '' ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="td-cat">
                                        <i class="fa-solid <?= h($s['categorie_icone'] ?? 'fa-tag') ?>"></i>
                                        <?= h($s['categorie_nom'] ?? '') ?>
                                    </td>
                                    <td>
                                        <span class="badge-prio prio-<?= h($s['priorite']) ?>">
                                            <?= ucfirst(h($s['priorite'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $badge = badgeStatut($s['statut']); ?>
                                        <span class="badge badge-statut <?= $badge['classe'] ?>">
                                            <?= $badge['label'] ?>
                                        </span>
                                    </td>
                                    <td class="td-votes">
                                        <i class="fa-solid fa-bolt"></i> <?= (int)($s['nb_votes'] ?? 0) ?>
                                    </td>
                                    <td class="td-actions">
                                        <!-- ✅ Bouton voir/modifier → page détail -->
                                        <a href="index.php?page=admin&action=voirSignalement&id=<?= $s['id'] ?>"
                                            class="btn-action btn-edit" title="Voir et modifier">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <!-- Bouton supprimer → modal confirmation -->
                                        <a href="#supprimer-<?= $s['id'] ?>" class="btn-action btn-del" title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div><!-- /main-wrap -->

    <!-- ══ MODALS SUPPRESSION UNIQUEMENT ════════════════════════════ -->
    <?php foreach ($signalements as $s): ?>
        <div class="modal-fond" id="supprimer-<?= $s['id'] ?>">
            <div class="modal modal-danger">
                <div class="modal-header">
                    <div class="modal-header-left">
                        <span class="modal-ref">#<?= $s['id'] ?></span>
                        <h3>Confirmer la suppression</h3>
                    </div>
                    <a href="#" class="modal-close"><i class="fa-solid fa-xmark"></i></a>
                </div>
                <div class="modal-body">
                    <div class="alerte-danger">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>Action irréversible.</strong>
                            <p>Le signalement, ses commentaires et ses votes seront définitivement supprimés.</p>
                        </div>
                    </div>
                    <div class="signalement-resume"><?= h($s['titre']) ?></div>
                </div>
                <form method="POST" action="index.php?page=signalements&action=supprimer">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <input type="hidden" name="return_to" value="admin_dashboard">
                    <div class="modal-footer">
                        <a href="#" class="btn-ghost">Annuler</a>
                        <button type="submit" class="btn-danger">
                            <i class="fa-solid fa-trash-can"></i> Supprimer définitivement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

</body>

</html>