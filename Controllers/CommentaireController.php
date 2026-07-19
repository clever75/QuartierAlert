<?php
// ============================================================
//  CommentaireController.php
//  Actions : ajouter, supprimer
// ============================================================
class CommentaireController
{

    public function __construct(private PDO $pdo) {}

    // ── POST index.php?page=commentaires&action=ajouter ───────
    public function ajouter(): void
    {
        requiertConnexion();

        $signalement_id = (int) ($_POST['signalement_id'] ?? 0);
        $contenu        = trim($_POST['contenu'] ?? '');
        $est_public     = !(estSuperAdmin() && isset($_POST['note_interne']));

        if ($signalement_id && !empty($contenu)) {
            ajouterCommentaire(
                $this->pdo,
                $signalement_id,
                $_SESSION['utilisateur_id'],
                $contenu,
                $est_public
            );

            // Notifier le citoyen si c'est un super_admin qui commente
            if (estSuperAdmin()) {
                notifierCitoyen(
                    $this->pdo,
                    $signalement_id,
                    'La mairie a répondu à votre signalement.',
                    'nouveau_commentaire'
                );
            }
        }

        redirect("index.php?page=signalements&action=voir&id=$signalement_id");
    }

    // ── POST index.php?page=commentaires&action=supprimer ─────
    public function supprimer(): void
    {
        requiertSuperAdmin();

        $id             = (int) ($_POST['id']             ?? 0);
        $signalement_id = (int) ($_POST['signalement_id'] ?? 0);

        supprimerCommentaire($this->pdo, $id);
        redirect("index.php?page=signalements&action=voir&id=$signalement_id");
    }
}
