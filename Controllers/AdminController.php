<?php
class AdminController
{
    public function __construct(private PDO $pdo) {}

    // ── Dashboard ─────────────────────────────────────────────
    public function dashboard(): void
    {
        $service = $_SESSION['service'] ?? null;

        $filtres = [];
        if ($service) {
            $filtres['service'] = $service;
        }
        if (!empty($_GET['statut']))       $filtres['statut']       = $_GET['statut'];
        if (!empty($_GET['categorie_id'])) $filtres['categorie_id'] = $_GET['categorie_id'];
        if (!empty($_GET['priorite']))     $filtres['priorite']     = $_GET['priorite'];
        if (!empty($_GET['q']))            $filtres['q']            = $_GET['q'];

        $stats        = statsSignalements($this->pdo, null, $service ?: null);
        $signalements = getSignalements($this->pdo, $filtres);
        $categories   = getCategories($this->pdo);

        extract(get_defined_vars());
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // ── Voir un signalement ───────────────────────────────────
    public function voirSignalement(): void
    {
        $id          = (int)($_GET['id'] ?? 0);
        $signalement = getSignalement($this->pdo, $id);

        if (!$signalement) {
            redirect('index.php?page=admin&action=dashboard');
        }

        $service = $_SESSION['service'] ?? null;
        if ($service && $signalement['service_responsable'] !== $service) {
            redirect('index.php?page=admin&action=dashboard');
        }

        $commentaires = getCommentairesAdmin($this->pdo, $id);
        $nb_votes     = countVotes($this->pdo, $id);
        $categories   = getCategories($this->pdo);

        extract(get_defined_vars());
        require __DIR__ . '/../Views/admin/signalement.php';
    }

    // ── Modifier statut ───────────────────────────────────────
    public function majStatut(): void
    {
        $id           = (int)($_POST['id']         ?? 0);
        $statut       = $_POST['statut']            ?? '';
        $note_interne = trim($_POST['note_interne'] ?? '');

        $statuts_ok = ['nouveau', 'en_cours', 'resolu', 'rejete'];
        if (!$id || !in_array($statut, $statuts_ok)) {
            redirect('index.php?page=admin&action=dashboard');
        }

        $signalement = getSignalement($this->pdo, $id);
        if (!$signalement) {
            redirect('index.php?page=admin&action=dashboard');
        }
        $service = $_SESSION['service'] ?? null;
        if ($service && $signalement['service_responsable'] !== $service) {
            redirect('index.php?page=admin&action=dashboard');
        }

        $photo_resolution = null;
        if (!empty($_FILES['photo_resolution']['name'])) {
            $ext_ok = ['jpg', 'jpeg', 'png', 'webp'];
            $ext    = strtolower(pathinfo($_FILES['photo_resolution']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $ext_ok) && $_FILES['photo_resolution']['size'] <= 5 * 1024 * 1024) {
                $dossier = __DIR__ . '/../uploads/resolutions/';
                if (!is_dir($dossier)) mkdir($dossier, 0755, true);
                $nom = 'res_' . $id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['photo_resolution']['tmp_name'], $dossier . $nom);
                $photo_resolution = 'uploads/resolutions/' . $nom;
            }
        }

        majSignalement($this->pdo, $id, $statut, $note_interne, $_SESSION['utilisateur_id'], $photo_resolution);

        $messages = [
            'en_cours' => 'Votre signalement est en cours de traitement.',
            'resolu'   => 'Votre signalement a été résolu. Merci !',
            'rejete'   => 'Votre signalement a été examiné et clôturé.',
            'nouveau'  => 'Le statut de votre signalement a été mis à jour.',
        ];
        notifierCitoyen($this->pdo, $id, $messages[$statut], 'statut_change');

        // Retour à la page détail plutôt qu'au dashboard
        redirect('index.php?page=admin&action=voirSignalement&id=' . $id);
    }

    // ── Liste des utilisateurs ────────────────────────────────
    public function utilisateurs(): void
    {
        requiertSuperAdmin();

        $role         = $_GET['role'] ?? null;
        $utilisateurs = getUtilisateurs($this->pdo, $role);

        extract(get_defined_vars());
        require __DIR__ . '/../Views/admin/utilisateurs.php';
    }

    // ── Désactiver un compte ──────────────────────────────────
    // Réservé au super_admin — empêche un utilisateur de se connecter
    // sans supprimer ses données (les signalements restent visibles)
    public function desactiverCompte(): void
    {
        requiertSuperAdmin();

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            redirect('index.php?page=admin&action=utilisateurs');
        }

        // Sécurité : on ne peut pas désactiver le super_admin lui-même
        if ($id === (int)$_SESSION['utilisateur_id']) {
            redirect('index.php?page=admin&action=utilisateurs');
        }

        desactiverUtilisateur($this->pdo, $id);
        redirect('index.php?page=admin&action=utilisateurs');
    }

    // ── Réactiver un compte ───────────────────────────────────
    // Remet actif = 1 pour permettre la reconnexion
    public function reactiverCompte(): void
    {
        requiertSuperAdmin();

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            redirect('index.php?page=admin&action=utilisateurs');
        }

        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET actif = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        redirect('index.php?page=admin&action=utilisateurs');
    }

    // ── Créer un agent ────────────────────────────────────────
    public function creerAgent(): void
    {
        requiertSuperAdmin();

        $erreur = '';
        $succes = '';

        $services = servicesOfficiels();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom      = trim($_POST['nom_complet'] ?? '');
            $tel      = trim($_POST['telephone']   ?? '');
            $email    = trim($_POST['email']        ?? '') ?: null;
            $service  = trim($_POST['service']      ?? '');
            $password = $_POST['password']           ?? '';
            $confirm  = $_POST['confirm']            ?? '';

            if (empty($nom)) {
                $erreur = 'Le nom complet est obligatoire.';
            } elseif (!preg_match('/^[0-9]{8}$/', $tel)) {
                $erreur = 'Numéro invalide — 8 chiffres sans le +228.';
            } elseif (empty($service) || !array_key_exists($service, $services)) {
                $erreur = 'Veuillez choisir un service valide.';
            } elseif (strlen($password) < 8) {
                $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
            } elseif ($password !== $confirm) {
                $erreur = 'Les mots de passe ne correspondent pas.';
            } else {
                $stmt = $this->pdo->prepare("
                    SELECT id FROM utilisateurs
                    WHERE telephone = :tel
                       OR (email = :email AND email IS NOT NULL)
                ");
                $stmt->execute([':tel' => '+228' . $tel, ':email' => $email]);

                if ($stmt->fetch()) {
                    $erreur = 'Ce numéro ou cet email est déjà utilisé.';
                } else {
                    $stmt = $this->pdo->prepare("
                        INSERT INTO utilisateurs
                            (nom_complet, telephone, email, mot_de_passe, role, service, actif)
                        VALUES
                            (:nom, :tel, :email, :pwd, 'agent', :service, 1)
                    ");
                    $stmt->execute([
                        ':nom'     => $nom,
                        ':tel'     => '+228' . $tel,
                        ':email'   => $email,
                        ':pwd'     => password_hash($password, PASSWORD_BCRYPT),
                        ':service' => $service,
                    ]);
$succes = "Agent « $nom » créé avec succès pour le service {$services[$service]['label']}.";                    $_POST  = [];
                }
            }
        }

        extract(get_defined_vars());
        require __DIR__ . '/../Views/admin/creer_agent.php';
    }
}
