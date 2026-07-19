<?php
// ============================================================
//  EspaceController.php
//  Mon espace citoyen — profil, signalements, mot de passe
// ============================================================
class EspaceController
{
    public function __construct(private PDO $pdo) {}

    // ── Page principale (onglets) ─────────────────────────────
    public function index(): void
    {
        requiertConnexion();

        $uid  = $_SESSION['utilisateur_id'];
        $user = getUtilisateur($this->pdo, $uid);

        // Ses signalements avec stats
        $mes_signalements = getSignalements($this->pdo, ['utilisateur_id' => $uid]);

        // Stats rapides
        $stats = [
            'total'    => count($mes_signalements),
            'nouveaux' => count(array_filter($mes_signalements, fn($s) => $s['statut'] === 'nouveau')),
            'en_cours' => count(array_filter($mes_signalements, fn($s) => $s['statut'] === 'en_cours')),
            'resolus'  => count(array_filter($mes_signalements, fn($s) => $s['statut'] === 'resolu')),
        ];

        // Onglet actif (par défaut : signalements)
        $onglet = $_GET['onglet'] ?? 'signalements';

        $succes_profil = $_SESSION['succes_profil'] ?? '';
        $erreur_profil = $_SESSION['erreur_profil'] ?? '';
        $succes_mdp    = $_SESSION['succes_mdp']    ?? '';
        $erreur_mdp    = $_SESSION['erreur_mdp']    ?? '';

        // Nettoyer les messages flash
        unset(
            $_SESSION['succes_profil'],
            $_SESSION['erreur_profil'],
            $_SESSION['succes_mdp'],
            $_SESSION['erreur_mdp']
        );

        require __DIR__ . '/../Views/espace/index.php';
    }

    // ── Modifier le profil ────────────────────────────────────
    public function majProfil(): void
    {
        requiertConnexion();

        $uid      = $_SESSION['utilisateur_id'];
        $nom      = trim($_POST['nom_complet'] ?? '');
        $email    = trim($_POST['email']       ?? '') ?: null;
        $commune  = trim($_POST['commune']     ?? '') ?: null;
        $quartier = trim($_POST['quartier']    ?? '') ?: null;

        if (empty($nom)) {
            $_SESSION['erreur_profil'] = 'Le nom complet est obligatoire.';
        } else {
            // Vérifier que l'email n'est pas déjà pris par quelqu'un d'autre
            if ($email) {
                $stmt = $this->pdo->prepare("
                    SELECT id FROM utilisateurs
                    WHERE email = :email AND id != :id
                ");
                $stmt->execute([':email' => $email, ':id' => $uid]);
                if ($stmt->fetch()) {
                    $_SESSION['erreur_profil'] = 'Cet email est déjà utilisé par un autre compte.';
                    redirect('index.php?page=espace&onglet=profil');
                    return;
                }
            }

            majUtilisateur($this->pdo, $uid, [
                'nom_complet' => $nom,
                'email'       => $email,
                'commune'     => $commune,
                'quartier'    => $quartier,
            ]);

            // Mettre à jour le nom en session
            $_SESSION['utilisateur_nom'] = $nom;
            $_SESSION['succes_profil']   = 'Profil mis à jour avec succès.';
        }

        redirect('index.php?page=espace&onglet=profil');
    }

    // ── Changer le mot de passe ───────────────────────────────
    public function majMotDePasse(): void
    {
        requiertConnexion();

        $uid     = $_SESSION['utilisateur_id'];
        $actuel  = $_POST['mot_de_passe_actuel'] ?? '';
        $nouveau = $_POST['nouveau_mot_de_passe'] ?? '';
        $confirm = $_POST['confirmer_mot_de_passe'] ?? '';

        $user = getUtilisateur($this->pdo, $uid);

        if (!password_verify($actuel, $user['mot_de_passe'])) {
            $_SESSION['erreur_mdp'] = 'Mot de passe actuel incorrect.';
        } elseif (strlen($nouveau) < 6) {
            $_SESSION['erreur_mdp'] = 'Le nouveau mot de passe doit faire au moins 6 caractères.';
        } elseif ($nouveau !== $confirm) {
            $_SESSION['erreur_mdp'] = 'Les mots de passe ne correspondent pas.';
        } else {
            $stmt = $this->pdo->prepare("
                UPDATE utilisateurs SET mot_de_passe = :pwd WHERE id = :id
            ");
            $stmt->execute([
                ':pwd' => password_hash($nouveau, PASSWORD_BCRYPT),
                ':id'  => $uid,
            ]);
            $_SESSION['succes_mdp'] = 'Mot de passe modifié avec succès.';
        }

        redirect('index.php?page=espace&onglet=profil');
    }
}