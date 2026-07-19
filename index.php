<?php
// ============================================================
//  index.php — Routeur principal
// ============================================================
session_start();

date_default_timezone_set('Africa/Lome');

require_once __DIR__ . '/Config/database.php';

// ── Helpers en PREMIER ───────────────────────────────────────
require_once __DIR__ . '/Helpers/helpers.php';

// ── Modèles ───────────────────────────────────────────────────
require_once __DIR__ . '/Models/utilisateur.php';
require_once __DIR__ . '/Models/signalement.php';
require_once __DIR__ . '/Models/commentaire.php';
require_once __DIR__ . '/Models/notification.php';
require_once __DIR__ . '/Models/vote.php';
require_once __DIR__ . '/Models/categorie.php';

// ── Contrôleurs ───────────────────────────────────────────────
require_once __DIR__ . '/Controllers/AccueilController.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/SignalementController.php';
require_once __DIR__ . '/Controllers/CommentaireController.php';
require_once __DIR__ . '/Controllers/VoteController.php';
require_once __DIR__ . '/Controllers/NotificationController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/EspaceController.php'; // ← AJOUTÉ

// ── Compteur de notifications ─────────────────────────────────
if (estConnecte()) {
    $_SESSION['nb_notifs'] = countNotificationsNonLues($pdo, $_SESSION['utilisateur_id']);
}

// ── Routeur ───────────────────────────────────────────────────
$page   = $_GET['page']   ?? 'accueil';
$action = $_GET['action'] ?? 'index';

switch ($page) {

    case 'accueil':
        $ctrl = new AccueilController($pdo);
        $ctrl->index();
        break;

    case 'auth':
        $ctrl = new AuthController($pdo);
        match ($action) {
            'login'    => $ctrl->login(),
            'logout'   => $ctrl->logout(),
            'register' => $ctrl->register(),
            default    => redirect('index.php?page=auth&action=login'),
        };
        break;

    case 'signalements':
        $ctrl = new SignalementController($pdo);
        match ($action) {
            'index'     => $ctrl->index(),
            'voir'      => $ctrl->voir(),
            'creer'     => $ctrl->creer(),
            'supprimer' => $ctrl->supprimer(),
            default     => $ctrl->index(),
        };
        break;

    case 'commentaires':
        $ctrl = new CommentaireController($pdo);
        match ($action) {
            'ajouter'   => $ctrl->ajouter(),
            'supprimer' => $ctrl->supprimer(),
            default     => redirect('index.php?page=signalements'),
        };
        break;

    case 'votes':
        $ctrl = new VoteController($pdo);
        match ($action) {
            'toggle' => $ctrl->toggle(),
            default  => redirect('index.php?page=signalements'),
        };
        break;

    case 'notifications':
        $ctrl = new NotificationController($pdo);
        match ($action) {
            'index'       => $ctrl->index(),
            'marquerLues' => $ctrl->marquerLues(),
            default       => $ctrl->index(),
        };
        break;

    case 'espace': // ← AJOUTÉ
        $ctrl = new EspaceController($pdo);
        match ($action) {
            'index'         => $ctrl->index(),
            'majProfil'     => $ctrl->majProfil(),
            'majMotDePasse' => $ctrl->majMotDePasse(),
            default         => $ctrl->index(),
        };
        break;

    case 'admin':
        $ctrl = new AdminController($pdo);
        match ($action) {
            'dashboard'        => $ctrl->dashboard(),
            'voirSignalement'  => $ctrl->voirSignalement(),
            'majStatut'        => $ctrl->majStatut(),
            'utilisateurs'     => $ctrl->utilisateurs(),
            'creerAgent'       => $ctrl->creerAgent(),
            'desactiverCompte' => $ctrl->desactiverCompte(),
            'reactiverCompte'  => $ctrl->reactiverCompte(),
            default            => $ctrl->dashboard(),
        };
        break;

    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="fr"><head>
            <meta charset="UTF-8"><title>404 — Mia Dzra Do</title>
            <style>
                body { font-family: sans-serif; text-align: center;
                       padding: 100px 20px; background: #f4f6f4; color: #1a2e22; }
                h1   { font-size: 4rem; color: #1B4332; margin-bottom: 8px; }
                p    { color: #5a7364; margin-bottom: 24px; }
                a    { color: #2D6A4F; font-weight: 600; text-decoration: none; }
            </style>
        </head><body>
            <h1>404</h1>
            <p>Cette page n\'existe pas.</p>
            <a href="index.php">← Retour à l\'accueil</a>
        </body></html>';
        break;
}
