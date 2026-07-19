<?php
// ============================================================
//  Views/admin/signalement.php
//  Variables : $signalement, $commentaires, $nb_votes, $categories
// ============================================================
$signalement  = $signalement  ?? [];
$commentaires = $commentaires ?? [];
$nb_votes     = $nb_votes     ?? 0;

if (empty($signalement)) {
    redirect('index.php?page=admin&action=dashboard');
}

$statut_info   = badgeStatut($signalement['statut']);
$labels_priorite = [
    'urgente' => ['label' => 'Urgente', 'classe' => 'prio-urgente'],
    'haute'   => ['label' => 'Haute',   'classe' => 'prio-haute'],
    'normale' => ['label' => 'Normale', 'classe' => 'prio-normale'],
    'basse'   => ['label' => 'Basse',   'classe' => 'prio-basse'],
];
$priorite_info = $labels_priorite[$signalement['priorite']] ?? ['label' => $signalement['priorite'], 'classe' => ''];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($signalement['titre']) ?> — Mia Dzra Do</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/admin.css">
    <style>
        /* ═══════════════════════════════════════════
       PAGE DÉTAIL SIGNALEMENT — ADMIN
    ═══════════════════════════════════════════ */

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            align-items: start;
        }

        /* ── Carte générique ── */
        .card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d1f17;
            margin: 0;
        }

        .card-header i {
            color: #10b981;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 20px;
        }

        /* ── En-tête page ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-header-left {
            flex: 1;
            min-width: 0;
        }

        .page-titre {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0d1f17;
            margin: 0 0 6px;
            line-height: 1.3;
        }

        .page-sous {
            font-size: 0.83rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .page-sous span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-retour {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            background: #f4f6f4;
            color: #374151;
            border-radius: 8px;
            font-size: 0.83rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
        }

        .btn-retour:hover {
            background: #e5e7eb;
        }

        /* ── Infos principales ── */
        .sig-description {
            font-size: 0.9rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .sig-description:empty {
            display: none;
        }

        .infos-grille {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info-item {
            background: #f9fafb;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .info-item-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .info-item-val {
            font-size: 0.88rem;
            font-weight: 600;
            color: #0d1f17;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-item-val i {
            color: #10b981;
            font-size: 0.8rem;
        }

        /* ── Photo signalement ── */
        .photo-sig {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
            max-height: 320px;
        }

        .photo-sig img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            cursor: pointer;
            transition: opacity 0.15s;
        }

        .photo-sig img:hover {
            opacity: 0.9;
        }

        /* ── Photo résolution ── */
        .photo-resolution {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .photo-resolution-label {
            padding: 8px 14px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #059669;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
        }

        .photo-resolution img {
            width: 100%;
            display: block;
            max-height: 200px;
            object-fit: cover;
            cursor: pointer;
        }

        /* ── Badges ── */
        .badge-statut {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .badge-nouveau {
            background: #fde8e8;
            color: #c0392b;
        }

        .badge-encours {
            background: #fff4e0;
            color: #d97706;
        }

        .badge-resolu {
            background: #dcfce7;
            color: #059669;
        }

        .badge-rejete {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-prio {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .prio-urgente {
            background: #fee2e2;
            color: #dc2626;
        }

        .prio-haute {
            background: #ffedd5;
            color: #ea580c;
        }

        .prio-normale {
            background: #f0fdf4;
            color: #16a34a;
        }

        .prio-basse {
            background: #f9fafb;
            color: #9ca3af;
        }

        /* ── Commentaires ── */
        .liste-commentaires {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .commentaire-item {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .commentaire-item.note-interne {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .commentaire-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
            gap: 8px;
        }

        .commentaire-auteur {
            font-size: 0.78rem;
            font-weight: 700;
            color: #0d1f17;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .commentaire-auteur .role-badge {
            font-size: 0.65rem;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 600;
        }

        .role-agent {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .role-admin {
            background: #ede9fe;
            color: #7c3aed;
        }

        .role-citoyen {
            background: #f3f4f6;
            color: #6b7280;
        }

        .role-note {
            background: #fef3c7;
            color: #92400e;
        }

        .commentaire-date {
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .commentaire-texte {
            font-size: 0.85rem;
            color: #374151;
            line-height: 1.6;
        }

        .vide-commentaires {
            text-align: center;
            padding: 32px;
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .vide-commentaires i {
            display: block;
            font-size: 1.8rem;
            margin-bottom: 8px;
            opacity: 0.3;
        }

        /* ── Formulaire commentaire ── */
        .form-commentaire textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            color: #0d1f17;
            resize: vertical;
            min-height: 90px;
            outline: none;
            transition: border-color 0.15s;
            box-sizing: border-box;
        }

        .form-commentaire textarea:focus {
            border-color: #10b981;
        }

        .form-commentaire-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .note-checkbox {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            color: #6b7280;
            cursor: pointer;
        }

        .note-checkbox input {
            cursor: pointer;
            accent-color: #f59e0b;
        }

        /* ── Sidebar actions ── */
        .sidebar-actions {
            position: sticky;
            top: 20px;
        }

        /* ── Formulaire statut ── */
        .form-statut select,
        .form-statut textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            color: #0d1f17;
            background: #fff;
            outline: none;
            transition: border-color 0.15s;
            box-sizing: border-box;
            margin-bottom: 12px;
        }

        .form-statut select:focus,
        .form-statut textarea:focus {
            border-color: #10b981;
        }

        .form-statut textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-statut label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: block;
            margin-bottom: 5px;
        }

        .form-groupe {
            margin-bottom: 14px;
        }

        /* Upload photo */
        .upload-zone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 16px;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            text-align: center;
        }

        .upload-zone:hover {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .upload-zone i {
            font-size: 1.4rem;
            color: #9ca3af;
        }

        .upload-zone span {
            font-size: 0.78rem;
            color: #6b7280;
            font-weight: 600;
        }

        .upload-zone small {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        /* Boutons */
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 11px;
            background: #0d1f17;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-primary:hover {
            background: #1a3a2a;
        }

        .btn-comment {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.83rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-comment:hover {
            background: #059669;
        }

        /* ── Citoyen info ── */
        .citoyen-bloc {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 10px;
        }

        .citoyen-avatar {
            width: 42px;
            height: 42px;
            background: #0d1f17;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .citoyen-nom {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0d1f17;
        }

        .citoyen-tel {
            font-size: 0.78rem;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Votes ── */
        .votes-bloc {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 10px;
        }

        .votes-chiffre {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: #0d1f17;
            line-height: 1;
        }

        .votes-label {
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* ── Timeline statut ── */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .tl-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding-bottom: 16px;
            position: relative;
        }

        .tl-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 24px;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        .tl-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            z-index: 1;
        }

        .tl-dot.actif {
            background: #0d1f17;
            color: #fff;
        }

        .tl-dot.inactif {
            background: #f3f4f6;
            color: #d1d5db;
            border: 2px solid #e5e7eb;
        }

        .tl-texte {
            padding-top: 2px;
        }

        .tl-nom {
            font-size: 0.82rem;
            font-weight: 700;
            color: #0d1f17;
        }

        .tl-desc {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 1px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .sidebar-actions {
                position: static;
            }
        }
    </style>
</head>

<body>

    <!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">MDD</div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">Mia Dzra Do</span>
                <span class="sidebar-brand-role">
                    <?php if (estSuperAdmin()): ?>
                        <i class="fa-solid fa-crown"></i> Super Admin
                    <?php else: ?>
                        <i class="fa-solid fa-user-tie"></i>
                        Agent — <?= h($_SESSION['service'] ?? '') ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php?page=admin&action=dashboard" class="nav-item">
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

    <!-- ══ CONTENU PRINCIPAL ════════════════════════════════════ -->
    <div class="main-wrap">

        <!-- En-tête -->
        <header class="page-header">
            <div class="page-header-left">
                <h1 class="page-titre"><?= h($signalement['titre']) ?></h1>
                <div class="page-sous">
                    <span><i class="fa-solid fa-location-dot"></i> <?= h($signalement['quartier']) ?>, <?= h($signalement['commune']) ?></span>
                    <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y à H:i', strtotime($signalement['date_creation'])) ?></span>
                    <span>
                        <span class="badge-statut <?= $statut_info['classe'] ?>"><?= $statut_info['label'] ?></span>
                    </span>
                    <span>
                        <span class="badge-prio <?= $priorite_info['classe'] ?>"><?= $priorite_info['label'] ?></span>
                    </span>
                </div>
            </div>
            <a href="index.php?page=admin&action=dashboard" class="btn-retour">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </header>

        <!-- Grille principale -->
        <div class="detail-grid">

            <!-- ══ COLONNE GAUCHE ══════════════════════════════ -->
            <div class="col-principale">

                <!-- Infos du signalement -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-info"></i>
                        <h2>Détails du signalement</h2>
                    </div>
                    <div class="card-body">

                        <!-- Photo signalement -->
                        <?php if (!empty($signalement['photo_url'])): ?>
                            <div class="photo-sig">
                                <img src="<?= h($signalement['photo_url']) ?>"
                                    alt="Photo du signalement"
                                    onclick="ouvrirPhoto('<?= h($signalement['photo_url']) ?>')">
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <?php if (!empty($signalement['description'])): ?>
                            <p class="sig-description"><?= nl2br(h($signalement['description'])) ?></p>
                        <?php endif; ?>

                        <!-- Grille d'infos -->
                        <div class="infos-grille">
                            <div class="info-item">
                                <div class="info-item-label">Catégorie</div>
                                <div class="info-item-val">
                                    <i class="fa-solid <?= h($signalement['categorie_icone'] ?? 'fa-tag') ?>"></i>
                                    <?= h($signalement['categorie_nom']) ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Service</div>
                                <div class="info-item-val">
                                    <i class="fa-solid fa-building"></i>
                                    <?= h($signalement['service_responsable'] ?? '—') ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Quartier</div>
                                <div class="info-item-val">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= h($signalement['quartier']) ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Commune</div>
                                <div class="info-item-val">
                                    <i class="fa-solid fa-map"></i>
                                    <?= h($signalement['commune']) ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-label">Signalé le</div>
                                <div class="info-item-val">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($signalement['date_creation'])) ?>
                                </div>
                            </div>
                            <?php if (!empty($signalement['resolu_at'])): ?>
                                <div class="info-item">
                                    <div class="info-item-label">Résolu le</div>
                                    <div class="info-item-val">
                                        <i class="fa-solid fa-circle-check" style="color:#059669"></i>
                                        <?= date('d/m/Y', strtotime($signalement['resolu_at'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <!-- Photo de résolution -->
                <?php if (!empty($signalement['photo_resolution'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-circle-check" style="color:#059669"></i>
                            <h2>Photo de résolution</h2>
                        </div>
                        <div class="card-body" style="padding:0">
                            <div class="photo-resolution">
                                <div class="photo-resolution-label">
                                    <i class="fa-solid fa-image"></i>
                                    Preuve de résolution fournie par l'agent
                                </div>
                                <img src="<?= h($signalement['photo_resolution']) ?>"
                                    alt="Photo de résolution"
                                    onclick="ouvrirPhoto('<?= h($signalement['photo_resolution']) ?>')">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Commentaires -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-comments"></i>
                        <h2>Commentaires & notes internes</h2>
                    </div>
                    <div class="card-body">

                        <!-- Liste des commentaires -->
                        <?php if (empty($commentaires)): ?>
                            <div class="vide-commentaires">
                                <i class="fa-regular fa-comment"></i>
                                Aucun commentaire pour l'instant.
                            </div>
                        <?php else: ?>
                            <div class="liste-commentaires">
                                <?php foreach ($commentaires as $c):
                                    $est_note = !(bool)$c['est_public'];
                                    $role = $c['auteur_role'] ?? 'citoyen';
                                    $role_classe = match ($role) {
                                        'agent'        => 'role-agent',
                                        'super_admin',
                                        'admin_mairie' => 'role-admin',
                                        default        => 'role-citoyen',
                                    };
                                    $role_label = match ($role) {
                                        'agent'        => 'Agent',
                                        'super_admin',
                                        'admin_mairie' => 'Admin',
                                        default        => 'Citoyen',
                                    };
                                    // Temps relatif
                                    $d = time() - strtotime($c['date_pub']);
                                    if ($d < 60)        $temps = 'À l\'instant';
                                    elseif ($d < 3600)  $temps = 'Il y a ' . floor($d / 60) . ' min';
                                    elseif ($d < 86400) $temps = 'Il y a ' . floor($d / 3600) . ' h';
                                    else                $temps = date('d/m/Y', strtotime($c['date_pub']));
                                ?>
                                    <div class="commentaire-item <?= $est_note ? 'note-interne' : '' ?>">
                                        <div class="commentaire-meta">
                                            <div class="commentaire-auteur">
                                                <?= h($c['auteur_nom']) ?>
                                                <span class="role-badge <?= $est_note ? 'role-note' : $role_classe ?>">
                                                    <?= $est_note ? '🔒 Note interne' : $role_label ?>
                                                </span>
                                            </div>
                                            <span class="commentaire-date"><?= $temps ?></span>
                                        </div>
                                        <div class="commentaire-texte"><?= nl2br(h($c['contenu'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulaire ajouter commentaire -->
                        <form method="POST"
                            action="index.php?page=commentaires&action=ajouter"
                            class="form-commentaire">
                            <input type="hidden" name="signalement_id" value="<?= (int)$signalement['id'] ?>">
                            <textarea name="contenu"
                                placeholder="Écrire un commentaire ou une note de suivi…"
                                required></textarea>
                            <div class="form-commentaire-actions">
                                <label class="note-checkbox">
                                    <input type="checkbox" name="note_interne" value="1">
                                    <i class="fa-solid fa-lock" style="color:#f59e0b;font-size:0.8rem"></i>
                                    Note interne (non visible par le citoyen)
                                </label>
                                <button type="submit" class="btn-comment">
                                    <i class="fa-solid fa-paper-plane"></i> Envoyer
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div><!-- /col-principale -->

            <!-- ══ COLONNE DROITE (SIDEBAR ACTIONS) ═══════════ -->
            <div class="sidebar-actions">

                <!-- Citoyen -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-user"></i>
                        <h2>Citoyen</h2>
                    </div>
                    <div class="card-body">
                        <div class="citoyen-bloc">
                            <div class="citoyen-avatar">
                                <?= strtoupper(substr($signalement['citoyen_nom'] ?? 'C', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="citoyen-nom"><?= h($signalement['citoyen_nom']) ?></div>
                                <div class="citoyen-tel">
                                    <i class="fa-solid fa-phone" style="font-size:0.7rem"></i>
                                    <?= h($signalement['citoyen_telephone'] ?? '—') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Votes -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-bolt"></i>
                        <h2>Soutiens citoyens</h2>
                    </div>
                    <div class="card-body">
                        <div class="votes-bloc">
                            <div class="votes-chiffre"><?= (int)$nb_votes ?></div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:600;color:#0d1f17">vote<?= $nb_votes > 1 ? 's' : '' ?></div>
                                <div class="votes-label">citoyen<?= $nb_votes > 1 ? 's' : '' ?> soutien<?= $nb_votes > 1 ? 'nent' : 't' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modifier le statut -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <h2>Modifier le statut</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="index.php?page=admin&action=majStatut"
                            enctype="multipart/form-data"
                            class="form-statut">
                            <input type="hidden" name="id" value="<?= (int)$signalement['id'] ?>">

                            <div class="form-groupe">
                                <label>Nouveau statut</label>
                                <select name="statut" required>
                                    <option value="nouveau" <?= $signalement['statut'] === 'nouveau'  ? 'selected' : '' ?>>🔴 Nouveau</option>
                                    <option value="en_cours" <?= $signalement['statut'] === 'en_cours' ? 'selected' : '' ?>>🟠 En cours</option>
                                    <option value="resolu" <?= $signalement['statut'] === 'resolu'   ? 'selected' : '' ?>>🟢 Résolu</option>
                                    <option value="rejete" <?= $signalement['statut'] === 'rejete'   ? 'selected' : '' ?>>⚫ Rejeté</option>
                                </select>
                            </div>

                            <div class="form-groupe">
                                <label>Note interne <span style="font-weight:400;text-transform:none">(optionnel)</span></label>
                                <textarea name="note_interne"
                                    placeholder="Ex : Agent déployé, intervention prévue demain…"><?= h($signalement['note_interne'] ?? '') ?></textarea>
                            </div>

                            <div class="form-groupe">
                                <label>Photo preuve <span style="font-weight:400;text-transform:none">(optionnel)</span></label>
                                <label class="upload-zone" for="photo_res">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span>Cliquer pour ajouter</span>
                                    <small>JPG, PNG, WEBP — max 5 Mo</small>
                                </label>
                                <input type="file" id="photo_res" name="photo_resolution"
                                    accept="image/jpeg,image/png,image/webp"
                                    style="display:none"
                                    onchange="previewUpload(this)">
                                <div id="preview-upload" style="display:none;margin-top:8px">
                                    <img id="preview-img" style="width:100%;border-radius:8px;max-height:120px;object-fit:cover">
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="fa-solid fa-check"></i> Enregistrer
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Timeline statut -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-timeline"></i>
                        <h2>Progression</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        $etapes = [
                            'nouveau'  => ['Signalement reçu',     'En attente de traitement'],
                            'en_cours' => ['Prise en charge',       'Agent assigné, intervention en cours'],
                            'resolu'   => ['Problème résolu',       'Intervention terminée'],
                        ];
                        $ordre  = ['nouveau', 'en_cours', 'resolu'];
                        $statut_actuel = $signalement['statut'];
                        $pos_actuel    = array_search($statut_actuel, $ordre);
                        ?>
                        <div class="timeline">
                            <?php foreach ($etapes as $key => [$nom, $desc]):
                                $pos   = array_search($key, $ordre);
                                $actif = ($pos !== false && $pos <= $pos_actuel && $statut_actuel !== 'rejete');
                            ?>
                                <div class="tl-item">
                                    <div class="tl-dot <?= $actif ? 'actif' : 'inactif' ?>">
                                        <i class="fa-solid <?= $actif ? 'fa-check' : 'fa-circle' ?>"></i>
                                    </div>
                                    <div class="tl-texte">
                                        <div class="tl-nom" style="color:<?= $actif ? '#0d1f17' : '#9ca3af' ?>"><?= $nom ?></div>
                                        <div class="tl-desc"><?= $desc ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($statut_actuel === 'rejete'): ?>
                                <div class="tl-item">
                                    <div class="tl-dot actif" style="background:#6b7280">
                                        <i class="fa-solid fa-xmark"></i>
                                    </div>
                                    <div class="tl-texte">
                                        <div class="tl-nom">Signalement rejeté</div>
                                        <div class="tl-desc">Examiné et clôturé</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div><!-- /sidebar-actions -->

        </div><!-- /detail-grid -->

    </div><!-- /main-wrap -->

    <!-- ══ LIGHTBOX PHOTO ═══════════════════════════════════════ -->
    <div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;cursor:pointer;align-items:center;justify-content:center"
        onclick="this.style.display='none'">
        <img id="lightbox-img" style="max-width:90vw;max-height:90vh;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.5)">
    </div>

    <script>
        function ouvrirPhoto(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').style.display = 'flex';
        }

        function previewUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('preview-upload').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>