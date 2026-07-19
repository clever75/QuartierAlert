<?php
// ============================================================
//  Model : signalement.php
//  Colonnes : id, titre, description, photo_url,
//             photo_resolution, quartier, commune, statut,
//             priorite, date_creation, updated_at, resolu_at,
//             utilisateur_id, categorie_id, agent_id, note_interne
// ============================================================

/**
 * Récupérer les signalements avec filtres.
 *
 * Filtre 'service' ajouté pour le modèle agent :
 *   Un agent ne voit que les signalements dont la catégorie
 *   correspond à son service (colonne service_responsable).
 *
 * Exemple :
 *   Agent voirie  → filtres['service'] = 'voirie'
 *   Super_admin   → pas de filtre service → voit tout
 */
function getSignalements(PDO $pdo, array $filtres = []): array
{
    $where  = "1";
    $params = [];

    if (!empty($filtres['statut'])) {
        $where .= " AND s.statut = :statut";
        $params[':statut'] = $filtres['statut'];
    }
    if (!empty($filtres['categorie_id'])) {
        if (is_array($filtres['categorie_id'])) {
            $ids = array_map('intval', $filtres['categorie_id']);
            $placeholders = [];
            foreach ($ids as $index => $id) {
                $placeholder = ':categorie_id_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $id;
            }
            if (!empty($placeholders)) {
                $where .= ' AND s.categorie_id IN (' . implode(', ', $placeholders) . ')';
            }
        } else {
            $where .= " AND s.categorie_id = :categorie_id";
            $params[':categorie_id'] = (int)$filtres['categorie_id'];
        }
    }
    if (!empty($filtres['commune'])) {
        $where .= " AND s.commune = :commune";
        $params[':commune'] = $filtres['commune'];
    }
    if (!empty($filtres['priorite'])) {
        $where .= " AND s.priorite = :priorite";
        $params[':priorite'] = $filtres['priorite'];
    }
    if (!empty($filtres['q'])) {
        $where .= " AND (s.titre LIKE :q OR s.description LIKE :q)";
        $params[':q'] = '%' . $filtres['q'] . '%';
    }
    if (!empty($filtres['utilisateur_id'])) {
        $where .= " AND s.utilisateur_id = :utilisateur_id";
        $params[':utilisateur_id'] = (int)$filtres['utilisateur_id'];
    }

    // ── Filtre service (pour les agents) ─────────────────────
    // C'est la colonne service_responsable de la table categories
    // qui détermine quel service gère ce type de signalement.
    if (!empty($filtres['service'])) {
        $where .= " AND c.service_responsable = :service";
        $params[':service'] = $filtres['service'];
    }

    $stmt = $pdo->prepare("
        SELECT
            s.*,
            c.nom                 AS categorie_nom,
            c.icone               AS categorie_icone,
            c.couleur             AS categorie_couleur,
            c.service_responsable AS service_responsable,
            u.nom_complet         AS citoyen_nom,
            u.telephone           AS citoyen_telephone,
            COUNT(DISTINCT v.id)  AS nb_votes,
            COUNT(DISTINCT cm.id) AS nb_commentaires
        FROM signalements s
        JOIN  categories   c  ON c.id = s.categorie_id
        JOIN  utilisateurs u  ON u.id = s.utilisateur_id
        LEFT JOIN votes        v  ON v.signalement_id  = s.id
        LEFT JOIN commentaires cm ON cm.signalement_id = s.id AND cm.est_public = 1
        WHERE $where
        GROUP BY s.id
        ORDER BY s.date_creation DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSignalement(PDO $pdo, int $id): array|false
{
    $stmt = $pdo->prepare("
        SELECT
            s.*,
            c.nom                 AS categorie_nom,
            c.icone               AS categorie_icone,
            c.couleur             AS categorie_couleur,
            c.service_responsable AS service_responsable,
            u.nom_complet         AS citoyen_nom,
            u.telephone           AS citoyen_telephone,
            a.nom_complet         AS agent_nom
        FROM signalements s
        JOIN  categories   c  ON c.id = s.categorie_id
        JOIN  utilisateurs u  ON u.id = s.utilisateur_id
        LEFT JOIN utilisateurs a ON a.id = s.agent_id
        WHERE s.id = :id
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function creerSignalement(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare("
        INSERT INTO signalements
            (titre, description, photo_url, quartier, commune,
             statut, priorite, date_creation, utilisateur_id, categorie_id)
        VALUES
            (:titre, :description, :photo_url, :quartier, :commune,
             'nouveau', :priorite, NOW(), :utilisateur_id, :categorie_id)
    ");
    $stmt->execute([
        ':titre'          => $data['titre'],
        ':description'    => $data['description']  ?? '',
        ':photo_url'      => $data['photo_url']    ?? null,
        ':quartier'       => $data['quartier'],
        ':commune'        => $data['commune'],
        ':priorite'       => $data['priorite']     ?? 'normale',
        ':utilisateur_id' => $data['utilisateur_id'],
        ':categorie_id'   => $data['categorie_id'],
    ]);
    return (int) $pdo->lastInsertId();
}

function majSignalement(PDO $pdo, int $id, string $statut, string $note_interne = '', int $agent_id = null, string $photo_resolution = null): bool
{
    if ($photo_resolution !== null) {
        $stmt = $pdo->prepare("
            UPDATE signalements
            SET statut           = :statut,
                note_interne     = :note_interne,
                agent_id         = :agent_id,
                photo_resolution = :photo_resolution,
                updated_at       = NOW(),
                resolu_at        = IF(:statut2 = 'resolu', NOW(), resolu_at)
            WHERE id = :id
        ");
        return $stmt->execute([
            ':statut'           => $statut,
            ':statut2'          => $statut,
            ':note_interne'     => $note_interne,
            ':agent_id'         => $agent_id,
            ':photo_resolution' => $photo_resolution,
            ':id'               => $id,
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE signalements
            SET statut       = :statut,
                note_interne = :note_interne,
                agent_id     = :agent_id,
                updated_at   = NOW(),
                resolu_at    = IF(:statut2 = 'resolu', NOW(), resolu_at)
            WHERE id = :id
        ");
        return $stmt->execute([
            ':statut'       => $statut,
            ':statut2'      => $statut,
            ':note_interne' => $note_interne,
            ':agent_id'     => $agent_id,
            ':id'           => $id,
        ]);
    }
}

function supprimerSignalement(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("DELETE FROM signalements WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

/**
 * Statistiques globales ou filtrées par service.
 *
 * Le super_admin appelle statsSignalements($pdo) → tout
 * L'agent appelle statsSignalements($pdo, null, 'voirie') → son service
 */
function statsSignalements(PDO $pdo, string $commune = null, string $service = null): array
{
    $where  = "1";
    $params = [];

    if ($commune) {
        $where .= " AND s.commune = :commune";
        $params[':commune'] = $commune;
    }
    if ($service) {
        $where .= " AND c.service_responsable = :service";
        $params[':service'] = $service;
    }

    $jointure = $service
        ? "FROM signalements s JOIN categories c ON c.id = s.categorie_id"
        : "FROM signalements s";

    $stmt = $pdo->prepare("
        SELECT
            COUNT(*)                        AS total,
            SUM(s.statut = 'nouveau')       AS nouveaux,
            SUM(s.statut = 'en_cours')      AS en_cours,
            SUM(s.statut = 'resolu')        AS resolus,
            SUM(s.statut = 'rejete')        AS rejetes,
            SUM(s.priorite = 'haute')       AS haute_priorite
        $jointure
        WHERE $where
    ");
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
