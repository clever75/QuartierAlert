<?php
// Views/espace/index.php
$user             = $user             ?? [];
$mes_signalements = $mes_signalements ?? [];
$stats            = $stats            ?? [];
$onglet           = $onglet           ?? 'signalements';
$succes_profil    = $succes_profil    ?? '';
$erreur_profil    = $erreur_profil    ?? '';
$succes_mdp       = $succes_mdp       ?? '';
$erreur_mdp       = $erreur_mdp       ?? '';

// Communes disponibles (cohérent avec l'inscription)
$communes = [
    'Golfe 1',
    'Golfe 2',
    'Golfe 3',
    'Golfe 4',
    'Golfe 5',
    'Golfe 6',
    'Agoè-Nyivé 1',
    'Agoè-Nyivé 2',
    'Agoè-Nyivé 3',
    'Agoè-Nyivé 4',
    'Agoè-Nyivé 5',
    'Agoè-Nyivé 6',
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace — Mia Dzra Do</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <style>
        :root {
            --vert: #006A4E;
            --vert-clair: #e8f5f0;
            --vert-fonce: #1B4332;
            --orange: #FF9F1C;
            --texte: #0d1f17;
            --gris: #6b7280;
            --fond: #F4F6F4;
            --blanc: #ffffff;
            --bordure: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        .page-espace {
            background: var(--fond);
            min-height: 80vh;
            padding: 40px 0 64px;
        }

        .conteneur {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ── En-tête profil ── */
        .profil-header {
            background: var(--blanc);
            border-radius: 16px;
            border: 1.5px solid var(--bordure);
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .profil-avatar {
            width: 72px;
            height: 72px;
            background: var(--vert);
            color: var(--blanc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            flex-shrink: 0;
        }

        .profil-info {
            flex: 1;
        }

        .profil-nom {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--texte);
            margin-bottom: 4px;
        }

        .profil-tel {
            font-size: 0.85rem;
            color: var(--gris);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .profil-stats {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .pstat {
            text-align: center;
        }

        .pstat-chiffre {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--texte);
            line-height: 1;
        }

        .pstat-label {
            font-size: 0.72rem;
            color: var(--gris);
            margin-top: 2px;
        }

        .pstat-resolu .pstat-chiffre {
            color: var(--vert);
        }

        .pstat-encours .pstat-chiffre {
            color: #d97706;
        }

        .btn-signaler-header {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            background: var(--orange);
            color: var(--blanc);
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
        }

        .btn-signaler-header:hover {
            background: #e08800;
        }

        /* ── Onglets ── */
        .onglets {
            display: flex;
            gap: 4px;
            background: var(--blanc);
            border-radius: 12px;
            border: 1.5px solid var(--bordure);
            padding: 6px;
            margin-bottom: 20px;
        }

        .onglet {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 16px;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gris);
            text-decoration: none;
            transition: all 0.15s;
        }

        .onglet:hover {
            background: var(--fond);
            color: var(--texte);
        }

        .onglet.actif {
            background: var(--vert);
            color: var(--blanc);
        }

        /* ── Carte section ── */
        .card {
            background: var(--blanc);
            border-radius: 14px;
            border: 1.5px solid var(--bordure);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--texte);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header h2 i {
            color: var(--vert);
        }

        .card-body {
            padding: 20px;
        }

        /* ── Alertes ── */
        .alerte {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .alerte-ok {
            background: #dcfce7;
            color: #059669;
            border: 1px solid #bbf7d0;
        }

        .alerte-err {
            background: #fde8e8;
            color: #c0392b;
            border: 1px solid #fca5a5;
        }

        /* ── Liste signalements ── */
        .liste-sig {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sig-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            border-radius: 10px;
            border: 1.5px solid var(--bordure);
            text-decoration: none;
            color: var(--texte);
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .sig-item:hover {
            border-color: var(--vert);
            box-shadow: 0 2px 12px rgba(0, 106, 78, 0.08);
        }

        .sig-vignette {
            width: 56px;
            height: 56px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--vert-clair);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vert);
            font-size: 1.3rem;
        }

        .sig-vignette img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sig-corps {
            flex: 1;
            min-width: 0;
        }

        .sig-titre-item {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--texte);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .sig-meta-item {
            font-size: 0.75rem;
            color: var(--gris);
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sig-meta-item span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sig-droite {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }

        /* Badges statut */
        .badge {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 3px 10px;
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

        .sig-votes {
            font-size: 0.75rem;
            color: var(--gris);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .etat-vide {
            text-align: center;
            padding: 48px 24px;
            color: var(--gris);
        }

        .etat-vide i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 12px;
            opacity: 0.25;
        }

        .etat-vide p {
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .etat-vide a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            background: var(--orange);
            color: var(--blanc);
            border-radius: 10px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.85rem;
            text-decoration: none;
        }

        /* ── Formulaire profil ── */
        .form-grille {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-grille.full {
            grid-template-columns: 1fr;
        }

        .champ {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .champ label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gris);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .champ input,
        .champ select {
            padding: 10px 14px;
            border: 1.5px solid var(--bordure);
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-size: 0.88rem;
            color: var(--texte);
            background: var(--blanc);
            outline: none;
            transition: border-color 0.15s;
        }

        .champ input:focus,
        .champ select:focus {
            border-color: var(--vert);
        }

        .champ input:disabled {
            background: #f9fafb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .champ-hint {
            font-size: 0.72rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .separateur {
            border: none;
            border-top: 1.5px solid var(--bordure);
            margin: 24px 0;
        }

        .section-titre {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--texte);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-titre i {
            color: var(--vert);
        }

        .pwd-wrap {
            position: relative;
        }

        .pwd-wrap input {
            width: 100%;
            padding-right: 40px;
        }

        .btn-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            font-size: 0.9rem;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 11px 24px;
            background: var(--vert);
            color: var(--blanc);
            border: none;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-submit:hover {
            background: var(--vert-fonce);
        }

        /* ── Notifications redirect ── */
        .notif-redirect {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            text-align: center;
            gap: 12px;
        }

        .notif-redirect i {
            font-size: 2.5rem;
            color: var(--vert);
            opacity: 0.7;
        }

        .notif-redirect p {
            font-size: 0.88rem;
            color: var(--gris);
        }

        .btn-notifs {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: var(--vert);
            color: var(--blanc);
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-notifs:hover {
            background: var(--vert-fonce);
        }

        @media (max-width: 640px) {
            .form-grille {
                grid-template-columns: 1fr;
            }

            .profil-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .onglets {
                flex-direction: column;
            }

            .onglet {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-espace">
        <div class="conteneur">

            <!-- ── En-tête profil ── -->
            <div class="profil-header">
                <div class="profil-avatar">
                    <?= mb_strtoupper(mb_substr($user['nom_complet'] ?? 'C', 0, 1)) ?>
                </div>
                <div class="profil-info">
                    <div class="profil-nom"><?= h($user['nom_complet'] ?? '') ?></div>
                    <div class="profil-tel">
                        <i class="fa-solid fa-phone"></i>
                        <?= h($user['telephone'] ?? '') ?>
                        <?php if (!empty($user['commune'])): ?>
                            &nbsp;·&nbsp;
                            <i class="fa-solid fa-location-dot"></i>
                            <?= h($user['commune']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="profil-stats">
                        <div class="pstat">
                            <div class="pstat-chiffre"><?= $stats['total'] ?></div>
                            <div class="pstat-label">Signalements</div>
                        </div>
                        <div class="pstat pstat-encours">
                            <div class="pstat-chiffre"><?= $stats['en_cours'] ?></div>
                            <div class="pstat-label">En cours</div>
                        </div>
                        <div class="pstat pstat-resolu">
                            <div class="pstat-chiffre"><?= $stats['resolus'] ?></div>
                            <div class="pstat-label">Résolus</div>
                        </div>
                    </div>
                </div>
                <a href="index.php?page=signalements&action=creer" class="btn-signaler-header">
                    <i class="fa-solid fa-plus"></i> Nouveau signalement
                </a>
            </div>

            <!-- ── Onglets ── -->
            <div class="onglets">
                <a href="index.php?page=espace&onglet=signalements"
                    class="onglet <?= $onglet === 'signalements' ? 'actif' : '' ?>">
                    <i class="fa-solid fa-list"></i> Mes signalements
                </a>
                <a href="index.php?page=espace&onglet=profil"
                    class="onglet <?= $onglet === 'profil' ? 'actif' : '' ?>">
                    <i class="fa-solid fa-user-pen"></i> Mon profil
                </a>
                <a href="index.php?page=notifications"
                    class="onglet">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
            </div>

            <!-- ════════════════════════════════════
             ONGLET 1 — MES SIGNALEMENTS
        ════════════════════════════════════ -->
            <?php if ($onglet === 'signalements'): ?>

                <div class="card">
                    <div class="card-header">
                        <h2>
                            <i class="fa-solid fa-list-check"></i>
                            Mes signalements
                        </h2>
                        <span style="font-size:0.8rem;color:var(--gris)">
                            <?= $stats['total'] ?> au total
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($mes_signalements)): ?>
                            <div class="etat-vide">
                                <i class="fa-solid fa-inbox"></i>
                                <p>Vous n'avez encore rien signalé.<br>Aidez votre quartier en signalant un problème !</p>
                                <a href="index.php?page=signalements&action=creer">
                                    <i class="fa-solid fa-plus"></i> Signaler un problème
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="liste-sig">
                                <?php foreach ($mes_signalements as $s):
                                    $b = badgeStatut($s['statut']);
                                    $d = time() - strtotime($s['date_creation']);
                                    if ($d < 3600)        $temps = 'Il y a ' . floor($d / 60) . ' min';
                                    elseif ($d < 86400)   $temps = 'Il y a ' . floor($d / 3600) . ' h';
                                    elseif ($d < 604800)  $temps = 'Il y a ' . floor($d / 86400) . ' j';
                                    else                  $temps = date('d/m/Y', strtotime($s['date_creation']));
                                ?>
                                    <a href="index.php?page=signalements&action=voir&id=<?= $s['id'] ?>"
                                        class="sig-item">
                                        <div class="sig-vignette">
                                            <?php if (!empty($s['photo_url'])): ?>
                                                <img src="<?= h($s['photo_url']) ?>" alt="">
                                            <?php else: ?>
                                                <i class="fa-solid <?= h($s['categorie_icone'] ?? 'fa-tag') ?>"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="sig-corps">
                                            <div class="sig-titre-item"><?= h($s['titre']) ?></div>
                                            <div class="sig-meta-item">
                                                <span><i class="fa-solid fa-location-dot"></i> <?= h($s['quartier']) ?></span>
                                                <span><i class="fa-regular fa-clock"></i> <?= $temps ?></span>
                                                <span><i class="fa-regular fa-comment"></i> <?= (int)$s['nb_commentaires'] ?></span>
                                            </div>
                                        </div>
                                        <div class="sig-droite">
                                            <span class="badge badge-statut <?= $b['classe'] ?>"><?= $b['label'] ?></span>
                                            <span class="sig-votes">
                                                <i class="fa-solid fa-bolt"></i> <?= (int)$s['nb_votes'] ?>
                                            </span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ════════════════════════════════════
             ONGLET 2 — MON PROFIL
        ════════════════════════════════════ -->
            <?php elseif ($onglet === 'profil'): ?>

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-user-pen"></i> Mes informations</h2>
                    </div>
                    <div class="card-body">

                        <?php if ($succes_profil): ?>
                            <div class="alerte alerte-ok">
                                <i class="fa-solid fa-circle-check"></i> <?= h($succes_profil) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($erreur_profil): ?>
                            <div class="alerte alerte-err">
                                <i class="fa-solid fa-circle-exclamation"></i> <?= h($erreur_profil) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=espace&action=majProfil">

                            <div class="form-grille">
                                <div class="champ" style="grid-column: 1/-1">
                                    <label>Nom complet *</label>
                                    <input type="text" name="nom_complet"
                                        value="<?= h($user['nom_complet'] ?? '') ?>"
                                        required>
                                </div>
                                <div class="champ">
                                    <label>Téléphone</label>
                                    <input type="text"
                                        value="<?= h($user['telephone'] ?? '') ?>"
                                        disabled>
                                    <span class="champ-hint">Non modifiable — c'est votre identifiant de connexion</span>
                                </div>
                                <div class="champ">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        value="<?= h($user['email'] ?? '') ?>"
                                        placeholder="votre@email.com">
                                </div>
                                <div class="champ">
                                    <label>Commune</label>
                                    <select name="commune">
                                        <option value="">— Choisir —</option>
                                        <?php foreach ($communes as $c): ?>
                                            <option value="<?= h($c) ?>"
                                                <?= ($user['commune'] ?? '') === $c ? 'selected' : '' ?>>
                                                <?= h($c) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="champ">
                                    <label>Quartier</label>
                                    <input type="text" name="quartier"
                                        value="<?= h($user['quartier'] ?? '') ?>"
                                        placeholder="Ex : Tokoin, Bè Kpota…">
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                            </button>
                        </form>

                        <hr class="separateur">

                        <!-- Changer mot de passe -->
                        <div class="section-titre">
                            <i class="fa-solid fa-lock"></i> Changer le mot de passe
                        </div>

                        <?php if ($succes_mdp): ?>
                            <div class="alerte alerte-ok">
                                <i class="fa-solid fa-circle-check"></i> <?= h($succes_mdp) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($erreur_mdp): ?>
                            <div class="alerte alerte-err">
                                <i class="fa-solid fa-circle-exclamation"></i> <?= h($erreur_mdp) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=espace&action=majMotDePasse">
                            <div class="form-grille">
                                <div class="champ" style="grid-column: 1/-1">
                                    <label>Mot de passe actuel *</label>
                                    <div class="pwd-wrap">
                                        <input type="password" name="mot_de_passe_actuel"
                                            id="pwd-actuel" required
                                            placeholder="Votre mot de passe actuel">
                                        <button type="button" class="btn-eye"
                                            onclick="togglePwd('pwd-actuel', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="champ">
                                    <label>Nouveau mot de passe *</label>
                                    <div class="pwd-wrap">
                                        <input type="password" name="nouveau_mot_de_passe"
                                            id="pwd-new" required
                                            placeholder="Min. 6 caractères">
                                        <button type="button" class="btn-eye"
                                            onclick="togglePwd('pwd-new', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="champ">
                                    <label>Confirmer *</label>
                                    <div class="pwd-wrap">
                                        <input type="password" name="confirmer_mot_de_passe"
                                            id="pwd-confirm" required
                                            placeholder="Répéter le mot de passe">
                                        <button type="button" class="btn-eye"
                                            onclick="togglePwd('pwd-confirm', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-key"></i> Changer le mot de passe
                            </button>
                        </form>

                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

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
    </script>
</body>

</html>