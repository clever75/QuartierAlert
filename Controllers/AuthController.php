<?php
// ============================================================
//  AuthController.php
// ============================================================
class AuthController
{
    public function __construct(private PDO $pdo) {}

    // ── Connexion ─────────────────────────────────────────────
    public function login(): void
    {
        if (estConnecte()) {
            redirect(estPersonnel()
                ? 'index.php?page=admin&action=dashboard'
                : 'index.php?page=accueil');
        }

        $erreur = '';
        $otp    = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'login';

            // ── Vérification OTP ──────────────────────────────
            if ($action === 'otp') {
                $code = trim($_POST['code'] ?? '');
                if (!isset($_SESSION['pending_id'])) {
                    $erreur = 'Session expirée. Reconnectez-vous.';
                } elseif ($code !== '1234') {
                    $erreur = 'Code incorrect. Réessayez.';
                    $otp    = true;
                } else {
                    $_SESSION['utilisateur_id']  = $_SESSION['pending_id'];
                    $_SESSION['utilisateur_nom'] = $_SESSION['pending_nom'];
                    $_SESSION['role']            = $_SESSION['pending_role'];
                    $_SESSION['service']         = $_SESSION['pending_service'] ?? null;
                    unset(
                        $_SESSION['pending_id'],
                        $_SESSION['pending_nom'],
                        $_SESSION['pending_role'],
                        $_SESSION['pending_service']
                    );
                    redirect('index.php?page=admin&action=dashboard');
                }

            // ── Connexion normale ─────────────────────────────
            } else {
                $identifiant = trim($_POST['identifiant'] ?? '');
                $password    = $_POST['password'] ?? '';

                if (empty($identifiant) || empty($password)) {
                    $erreur = 'Veuillez remplir tous les champs.';
                } else {
                    $user = loginUtilisateur($this->pdo, $identifiant, $password);
                    if ($user) {
                        $roles_personnel = ['super_admin', 'admin_mairie', 'agent'];

                        if (in_array($user['role'], $roles_personnel)) {
                            // Personnel → OTP requis
                            $_SESSION['pending_id']      = $user['id'];
                            $_SESSION['pending_nom']     = $user['nom_complet'];
                            $_SESSION['pending_role']    = $user['role'];
                            // La colonne s'appelle 'service' dans la BDD
                            $_SESSION['pending_service'] = $user['service'] ?? null;
                            $otp = true;
                        } else {
                            // Citoyen → connexion directe
                            $_SESSION['utilisateur_id']  = $user['id'];
                            $_SESSION['utilisateur_nom'] = $user['nom_complet'];
                            $_SESSION['role']            = $user['role'];
                            $_SESSION['service']         = null;

                            if (!empty($_SESSION['redirect_apres_login'])) {
                                $url = $_SESSION['redirect_apres_login'];
                                unset($_SESSION['redirect_apres_login']);
                                redirect($url);
                            } else {
                                redirect('index.php?page=accueil');
                            }
                        }
                    } else {
                        $erreur = 'Identifiant ou mot de passe incorrect.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    // ── Inscription ───────────────────────────────────────────
    public function register(): void
    {
        if (estConnecte()) {
            redirect('index.php?page=accueil');
        }

        $erreur   = '';
        $etape    = 1;
        $nom      = trim($_POST['nom_complet'] ?? '');
        $tel      = trim($_POST['telephone']   ?? '');
        $email    = trim($_POST['email']       ?? '');
        $commune  = trim($_POST['commune']     ?? '');
        $quartier = trim($_POST['quartier']    ?? '');
        $action   = $_POST['action']           ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($action === 'aller_etape2') {
                if (empty($nom)) {
                    $erreur = 'Le nom complet est obligatoire.';
                } elseif (empty($tel)) {
                    $erreur = 'Le numéro de téléphone est obligatoire.';
                } elseif (!preg_match('/^[0-9]{8}$/', $tel)) {
                    $erreur = 'Numéro invalide — 8 chiffres requis (sans le +228).';
                } else {
                    $etape = 2;
                }

            } elseif ($action === 'creer_compte') {
                $password = $_POST['password'] ?? '';
                $confirm  = $_POST['confirm']  ?? '';
                $cgu      = $_POST['cgu']      ?? '';
                $etape    = 2;

                if (empty($nom) || empty($tel)) {
                    $erreur = 'Données manquantes. Recommencez depuis le début.';
                    $etape  = 1;
                } elseif (empty($commune)) {
                    $erreur = 'Veuillez choisir votre commune.';
                } elseif (strlen($password) < 6) {
                    $erreur = 'Le mot de passe doit faire au moins 6 caractères.';
                } elseif ($password !== $confirm) {
                    $erreur = 'Les mots de passe ne correspondent pas.';
                } elseif (empty($cgu)) {
                    $erreur = 'Vous devez accepter les conditions d\'utilisation.';
                } else {
                    $resultat = inscrireUtilisateur($this->pdo, [
                        'nom_complet' => $nom,
                        'telephone'   => '+228' . $tel,
                        'email'       => $email ?: null,
                        'password'    => $password,
                        'commune'     => $commune,
                        'quartier'    => $quartier,
                    ]);

                    if ($resultat) {
                        $_SESSION['utilisateur_id']  = $resultat;
                        $_SESSION['utilisateur_nom'] = $nom;
                        $_SESSION['role']            = 'citoyen';
                        $_SESSION['service']         = null;

                        if (!empty($_SESSION['redirect_apres_login'])) {
                            $url = $_SESSION['redirect_apres_login'];
                            unset($_SESSION['redirect_apres_login']);
                            redirect($url);
                        } else {
                            redirect('index.php?page=accueil');
                        }
                    } else {
                        $erreur = 'Ce numéro ou cet email est déjà utilisé.';
                        $etape  = 1;
                    }
                }
            }
        }

        require __DIR__ . '/../Views/auth/inscription.php';
    }

    // ── Déconnexion ───────────────────────────────────────────
    public function logout(): void
    {
        session_destroy();
        redirect('index.php?page=auth&action=login');
    }
}