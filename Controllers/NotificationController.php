<?php
// ============================================================
//  NotificationController.php
//  Actions : index, marquerLues
// ============================================================
class NotificationController
{

    public function __construct(private PDO $pdo) {}

    // ── GET index.php?page=notifications ──────────────────────
    public function index(): void
    {
        requiertConnexion();

        $notifications = getNotifications($this->pdo, $_SESSION['utilisateur_id']);
        marquerToutesLues($this->pdo, $_SESSION['utilisateur_id']);

        require __DIR__ . '/../Views/notifications/index.php';
    }

    // ── POST index.php?page=notifications&action=marquerLues ──
    public function marquerLues(): void
    {
        requiertConnexion();
        marquerToutesLues($this->pdo, $_SESSION['utilisateur_id']);
        redirect('index.php?page=notifications');
    }
}
