<?php
// ============================================================
//  Model : utilisateur.php
// ============================================================

function inscrireUtilisateur(PDO $pdo, array $data): int|false
{
    $stmt = $pdo->prepare("
        SELECT id FROM utilisateurs
        WHERE telephone = :telephone OR (email = :email AND email IS NOT NULL)
    ");
    $stmt->execute([':telephone' => $data['telephone'], ':email' => $data['email'] ?? null]);
    if ($stmt->fetch()) return false;

    $stmt = $pdo->prepare("
        INSERT INTO utilisateurs
            (nom_complet, telephone, email, mot_de_passe, role, commune, quartier)
        VALUES
            (:nom_complet, :telephone, :email, :mot_de_passe, 'citoyen', :commune, :quartier)
    ");
    $stmt->execute([
        ':nom_complet'  => $data['nom_complet'],
        ':telephone'    => $data['telephone'],
        ':email'        => $data['email']    ?? null,
        ':mot_de_passe' => password_hash($data['password'], PASSWORD_BCRYPT),
        ':commune'      => $data['commune']  ?? null,
        ':quartier'     => $data['quartier'] ?? null,
    ]);
    return (int) $pdo->lastInsertId();
}

// ── Créer un agent (super_admin uniquement) ───────────────────
function creerAgent(PDO $pdo, array $data): int|false
{
    // Vérifier unicité téléphone / email
    $stmt = $pdo->prepare("
        SELECT id FROM utilisateurs
        WHERE telephone = :telephone OR (email = :email AND email IS NOT NULL)
    ");
    $stmt->execute([':telephone' => $data['telephone'], ':email' => $data['email'] ?? null]);
    if ($stmt->fetch()) return false;

    $stmt = $pdo->prepare("
        INSERT INTO utilisateurs
            (nom_complet, telephone, email, mot_de_passe, role, service, actif)
        VALUES
            (:nom_complet, :telephone, :email, :mot_de_passe, 'agent', :service, 1)
    ");
    $stmt->execute([
        ':nom_complet'  => $data['nom_complet'],
        ':telephone'    => $data['telephone'],
        ':email'        => $data['email']    ?? null,
        ':mot_de_passe' => password_hash($data['password'], PASSWORD_BCRYPT),
        ':service'      => $data['service'],
    ]);
    return (int) $pdo->lastInsertId();
}

function loginUtilisateur(PDO $pdo, string $identifiant, string $password): array|false
{
    $tel = preg_match('/^[0-9]{8}$/', $identifiant)
        ? '+228' . $identifiant
        : $identifiant;

    $stmt = $pdo->prepare("
        SELECT * FROM utilisateurs
        WHERE (telephone = :tel OR email = :email)
          AND actif = 1
        LIMIT 1
    ");
    $stmt->execute([':tel' => $tel, ':email' => $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        return $user;
    }
    return false;
}

function getUtilisateur(PDO $pdo, int $id): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUtilisateurs(PDO $pdo, string $role = null): array
{
    if ($role) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE role = :role ORDER BY nom_complet");
        $stmt->execute([':role' => $role]);
    } else {
        $stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom_complet");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function majUtilisateur(PDO $pdo, int $id, array $data): bool
{
    $stmt = $pdo->prepare("
        UPDATE utilisateurs
        SET nom_complet = :nom_complet,
            email       = :email,
            commune     = :commune,
            quartier    = :quartier
        WHERE id = :id
    ");
    return $stmt->execute([
        ':nom_complet' => $data['nom_complet'],
        ':email'       => $data['email']    ?? null,
        ':commune'     => $data['commune']  ?? null,
        ':quartier'    => $data['quartier'] ?? null,
        ':id'          => $id,
    ]);
}

function desactiverUtilisateur(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = 0 WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}