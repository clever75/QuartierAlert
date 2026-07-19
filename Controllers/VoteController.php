<?php
// ============================================================
//  VoteController.php
//  Actions : toggle
// ============================================================
class VoteController
{

    public function __construct(private PDO $pdo) {}

    // ── POST index.php?page=votes&action=toggle ───────────────
    // Supporte Ajax (retourne JSON) et formulaire classique
    public function toggle(): void
    {
        requiertConnexion();

        $signalement_id = (int) ($_POST['signalement_id'] ?? 0);

        if (!$signalement_id) {
            redirect('index.php?page=signalements');
        }

        $resultat = toggleVote($this->pdo, $_SESSION['utilisateur_id'], $signalement_id);

        // Réponse JSON si requête Ajax
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode($resultat);
            exit;
        }

        redirect("index.php?page=signalements&action=voir&id=$signalement_id");
    }
}
