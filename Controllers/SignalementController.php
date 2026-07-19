<?php
// ============================================================
//  SignalementController.php
//  Toute la logique est ici — les vues n'utilisent pas $pdo
// ============================================================
class SignalementController
{

    public function __construct(private PDO $pdo) {}

    // ── Liste des signalements ────────────────────────────────
    public function index(): void
    {
        $connecte       = estConnecte();
        $utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

        // Gestion du vote (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $connecte) {
            $sig_id = (int)($_POST['signalement_id'] ?? 0);
            if ($sig_id) {
                toggleVote($this->pdo, $utilisateur_id, $sig_id);
            }
            $qs = $_SERVER['QUERY_STRING'] ?? '';
            redirect('index.php?page=signalements' . ($qs ? '&' . $qs : ''));
        }

        $categories   = getCategories($this->pdo);

        // Filtres
        $statut_filtre    = $_GET['statut']       ?? '';
        $categorie_filtre = $_GET['categorie_id'] ?? '';
        $q_filtre         = $_GET['q']            ?? '';

        $filtres = [];
        if ($statut_filtre)    $filtres['statut']       = $statut_filtre;
        if ($categorie_filtre) {
            $categorie_ids = categorieSignalementIdsPourFiltre($categories, (int)$categorie_filtre);
            $filtres['categorie_id'] = $categorie_ids;
        }
        if ($q_filtre)         $filtres['q']            = $q_filtre;

        $signalements = getSignalements($this->pdo, $filtres);

        // Votes de l'utilisateur — calculé ici, pas dans la vue
        $votes_utilisateur = [];
        if ($connecte) {
            foreach ($signalements as $s) {
                $votes_utilisateur[$s['id']] = aDejaVote($this->pdo, $utilisateur_id, $s['id']);
            }
        }

        require __DIR__ . '/../Views/signalements/index.php';
    }

    // ── Détail d'un signalement ───────────────────────────────
    public function voir(): void
    {
        $connecte       = estConnecte();
        $utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
        $id             = (int)($_GET['id'] ?? 0);
        $signalement    = getSignalement($this->pdo, $id);

        if (!$signalement) {
            redirect('index.php?page=signalements');
        }

        // Gestion vote
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_vote']) && $connecte) {
            toggleVote($this->pdo, $utilisateur_id, $id);
            redirect('index.php?page=signalements&action=voir&id=' . $id);
        }

        // Gestion commentaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_commenter']) && $connecte) {
            $contenu = trim($_POST['contenu'] ?? '');
            if (!empty($contenu)) {
                ajouterCommentaire($this->pdo, $id, $utilisateur_id, $contenu, true);
            }
            redirect('index.php?page=signalements&action=voir&id=' . $id);
        }

        $commentaires = getCommentaires($this->pdo, $id);
        $nb_votes     = countVotes($this->pdo, $id);
        $deja_vote    = $connecte ? aDejaVote($this->pdo, $utilisateur_id, $id) : false;

        require __DIR__ . '/../Views/signalements/detail.php';
    }

    // ── Créer un signalement ──────────────────────────────────
    public function creer(): void
    {
        requiertConnexion();

        $erreur     = '';
        $categories = getCategories($this->pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = trim($_POST['description']  ?? '');
            $categorie   = (int)($_POST['categorie_id'] ?? 0);
            $commune     = trim($_POST['commune']       ?? '');
            $quartier    = trim($_POST['quartier']      ?? '');
            $priorite    = $_POST['priorite']           ?? 'normale';

            // Génération automatique du titre (catégorie + quartier)
            $cat_nom = '';
            foreach ($categories as $cat) {
                if ((int)$cat['id'] === $categorie) {
                    $cat_nom = $cat['nom'];
                    break;
                }
            }
            $titre = $cat_nom && $quartier ? $cat_nom . ' — ' . $quartier : '';

            if (!$categorie || empty($commune) || empty($quartier)) {
                $erreur = 'Veuillez choisir un type de problème et indiquer votre quartier.';
            } else {
                $photo_url = null;
                if (!empty($_FILES['photo']['name'])) {
                    $photo_url = $this->uploadPhoto($_FILES['photo']);
                    if (!$photo_url) {
                        $erreur = 'Format de photo invalide (jpg, png, webp — max 5Mo).';
                    }
                }

                if (!$erreur) {
                    $new_id = creerSignalement($this->pdo, [
                        'titre'          => $titre,
                        'description'    => $description,
                        'photo_url'      => $photo_url,
                        'quartier'       => $quartier,
                        'commune'        => $commune,
                        'priorite'       => $priorite,
                        'utilisateur_id' => $_SESSION['utilisateur_id'],
                        'categorie_id'   => $categorie,
                    ]);
                    redirect('index.php?page=signalements&action=voir&id=' . $new_id);
                }
            }
        }

        require __DIR__ . '/../Views/signalements/creer.php';
    }

    // ── Supprimer ─────────────────────────────────────────────
    public function supprimer(): void
    {
        requiertConnexion();
        $id          = (int)($_POST['id'] ?? 0);
        $return_to   = $_POST['return_to'] ?? '';
        $signalement = getSignalement($this->pdo, $id);

        if ($signalement) {
            $est_proprio = $signalement['utilisateur_id'] === $_SESSION['utilisateur_id'];
            if ($est_proprio || estPersonnel()) {
                supprimerSignalement($this->pdo, $id);
            }
        }

        if ($return_to === 'admin_dashboard') {
            redirect('index.php?page=admin&action=dashboard');
        }

        redirect('index.php?page=signalements');
    }

    // ── Upload photo ──────────────────────────────────────────
    private function uploadPhoto(array $fichier): string|false
    {
        $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensions_ok)) return false;
        if ($fichier['size'] > 5 * 1024 * 1024) return false;

        $dossier = __DIR__ . '/../uploads/signalements/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);

        $nom = uniqid('sig_', true) . '.' . $ext;
        move_uploaded_file($fichier['tmp_name'], $dossier . $nom);
        return 'uploads/signalements/' . $nom;
    }
}
