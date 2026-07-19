<?php
// Views/admin/utilisateurs.php
$utilisateurs = $utilisateurs ?? [];
$role_filtre  = $role_filtre  ?? null;
$roles_labels = ['citoyen' => 'Citoyen', 'agent' => 'Agent', 'super_admin' => 'Super Admin'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs — Mia Dzra Do</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/admin.css">
    <link rel="stylesheet" href="Public/Css/utilisateurs.css">
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
        <a href="index.php?page=admin&action=utilisateurs" class="nav-item active">
            <i class="fa-solid fa-users"></i><span>Utilisateurs</span>
        </a>
        <a href="index.php?page=admin&action=creerAgent" class="nav-item">
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
            <h1 class="page-titre">Utilisateurs</h1>
            <p class="page-sous">Gestion des comptes — citoyens, agents et admins</p>
        </div>
        <a href="index.php?page=admin&action=creerAgent" class="btn-primary">
            <i class="fa-solid fa-user-plus"></i>
            <span>Nouvel agent</span>
        </a>
    </header>

    <section class="section-card">

        <!-- Onglets filtres -->
        <div class="u-tabs">
            <a href="index.php?page=admin&action=utilisateurs"
               class="u-tab <?= !$role_filtre ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group"></i> Tous
                <span class="u-tab-count"><?= count($utilisateurs) ?></span>
            </a>
            <a href="index.php?page=admin&action=utilisateurs&role=citoyen"
               class="u-tab <?= $role_filtre === 'citoyen' ? 'active' : '' ?>">
                <i class="fa-solid fa-user"></i> Citoyens
            </a>
            <a href="index.php?page=admin&action=utilisateurs&role=agent"
               class="u-tab <?= $role_filtre === 'agent' ? 'active' : '' ?>">
                <i class="fa-solid fa-hard-hat"></i> Agents
            </a>
            <a href="index.php?page=admin&action=utilisateurs&role=super_admin"
               class="u-tab <?= $role_filtre === 'super_admin' ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> Admins
            </a>
        </div>

        <!-- Tableau desktop -->
        <div class="tableau-wrap">
            <table class="tableau u-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Contact</th>
                        <th>Rôle</th>
                        <th>Service</th>
                        <th>Commune</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($utilisateurs)): ?>
                    <tr>
                        <td colspan="7" class="td-vide">
                            <i class="fa-solid fa-users-slash"></i>
                            <span>Aucun utilisateur trouvé</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($utilisateurs as $u): ?>
                    <tr class="<?= !$u['actif'] ? 'row-inactif' : '' ?>">
                        <td>
                            <div class="u-cell">
                                <div class="u-avatar"><?= strtoupper(substr($u['nom_complet'], 0, 1)) ?></div>
                                <div>
                                    <div class="u-nom"><?= h($u['nom_complet']) ?></div>
                                    <div class="u-date">
                                        <i class="fa-regular fa-clock"></i>
                                        <?= date('d M Y', strtotime($u['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="u-tel"><?= h($u['telephone']) ?></div>
                            <?php if ($u['email']): ?>
                            <div class="u-email"><?= h($u['email']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="u-role-badge role-<?= h($u['role']) ?>">
                                <?= $roles_labels[$u['role']] ?? h($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($u['service'])): ?>
                                <span class="u-service"><?= h(libelleService($u['service'])) ?></span>
                            <?php else: ?>
                                <span class="u-nil">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="u-commune">
                            <?= $u['commune'] ? h($u['commune']) : '<span class="u-nil">—</span>' ?>
                        </td>
                        <td>
                            <span class="u-actif actif-<?= $u['actif'] ? 'oui' : 'non' ?>">
                                <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                            </span>
                        </td>
                        <td class="td-actions">
                            <?php if ($u['id'] !== (int)$_SESSION['utilisateur_id']): ?>
                                <?php if ($u['actif']): ?>
                                    <a href="#desac-<?= $u['id'] ?>" class="btn-action btn-del" title="Désactiver">
                                        <i class="fa-solid fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <form method="POST" action="index.php?page=admin&action=reactiverCompte">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn-action btn-reac" title="Réactiver">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="u-vous">vous</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Cards mobile -->
        <div class="u-cards">
            <?php if (empty($utilisateurs)): ?>
                <div class="u-card-vide">
                    <i class="fa-solid fa-users-slash"></i>
                    Aucun utilisateur trouvé
                </div>
            <?php else: ?>
                <?php foreach ($utilisateurs as $u): ?>
                <div class="u-card <?= !$u['actif'] ? 'u-card-inactif' : '' ?>">
                    <div class="u-card-top">
                        <div class="u-cell">
                            <div class="u-avatar"><?= strtoupper(substr($u['nom_complet'], 0, 1)) ?></div>
                            <div>
                                <div class="u-nom"><?= h($u['nom_complet']) ?></div>
                                <div class="u-tel"><?= h($u['telephone']) ?></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <span class="u-actif actif-<?= $u['actif'] ? 'oui' : 'non' ?>">
                                <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                            </span>
                            <?php if ($u['id'] !== (int)$_SESSION['utilisateur_id']): ?>
                                <?php if ($u['actif']): ?>
                                    <a href="#desac-<?= $u['id'] ?>" class="btn-action btn-del">
                                        <i class="fa-solid fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <form method="POST" action="index.php?page=admin&action=reactiverCompte">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn-action btn-reac">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="u-card-meta">
                        <span class="u-role-badge role-<?= h($u['role']) ?>">
                            <?= $roles_labels[$u['role']] ?? h($u['role']) ?>
                        </span>
                        <?php if (!empty($u['service'])): ?>
                            <span class="u-service"><?= h(libelleService($u['service'])) ?></span>
                        <?php endif; ?>
                        <?php if ($u['commune']): ?>
                            <span class="u-commune-tag">
                                <i class="fa-solid fa-location-dot"></i> <?= h($u['commune']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </section>
</div>

<!-- Modals désactivation -->
<?php foreach ($utilisateurs as $u): ?>
<?php if ($u['actif'] && $u['id'] !== (int)$_SESSION['utilisateur_id']): ?>
<div class="modal-fond" id="desac-<?= $u['id'] ?>">
    <div class="modal modal-danger">
        <div class="modal-header">
            <div class="modal-header-left">
                <span class="modal-ref">#<?= $u['id'] ?></span>
                <h3>Désactiver le compte</h3>
            </div>
            <a href="#" class="modal-close"><i class="fa-solid fa-xmark"></i></a>
        </div>
        <div class="modal-body">
            <div class="alerte-danger">
                <i class="fa-solid fa-ban"></i>
                <div>
                    <strong><?= h($u['nom_complet']) ?></strong>
                    <p>Ne pourra plus se connecter. Ses données sont conservées.</p>
                </div>
            </div>
        </div>
        <form method="POST" action="index.php?page=admin&action=desactiverCompte">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <div class="modal-footer">
                <a href="#" class="btn-ghost">Annuler</a>
                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-ban"></i> Désactiver
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

</body>
</html>