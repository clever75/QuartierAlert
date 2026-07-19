<?php

// ============================================================
//  Model : commentaires.php
//  Table : commentaires
//  Colonnes : id, contenu, date_pub, signalement_id,
//             utilisateur_id, est_public
// ============================================================

function getCommentaires(PDO $pdo, int $signalement_id): array
{
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.contenu,
            c.date_pub,
            c.est_public,
            u.nom_complet   AS auteur_nom,
            u.role          AS auteur_role
        FROM commentaires c
        JOIN utilisateurs u ON u.id = c.utilisateur_id
        WHERE c.signalement_id = :id
          AND c.est_public = 1
        ORDER BY c.date_pub ASC
    ");
    $stmt->execute([':id' => $signalement_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupère aussi les notes internes (pour les admins)
function getCommentairesAdmin(PDO $pdo, int $signalement_id): array
{
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.contenu,
            c.date_pub,
            c.est_public,
            u.nom_complet   AS auteur_nom,
            u.role          AS auteur_role
        FROM commentaires c
        JOIN utilisateurs u ON u.id = c.utilisateur_id
        WHERE c.signalement_id = :id
        ORDER BY c.date_pub ASC
    ");
    $stmt->execute([':id' => $signalement_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterCommentaire(PDO $pdo, int $signalement_id, int $utilisateur_id, string $contenu, bool $est_public = true): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO commentaires (contenu, date_pub, signalement_id, utilisateur_id, est_public)
        VALUES (:contenu, :date_pub, :signalement_id, :utilisateur_id, :est_public)
    ");
    return $stmt->execute([
        ':contenu'        => $contenu,
        ':date_pub'       => date('Y-m-d H:i:s'),
        ':signalement_id' => $signalement_id,
        ':utilisateur_id' => $utilisateur_id,
        ':est_public'     => $est_public ? 1 : 0,
    ]);
}

function supprimerCommentaire(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("DELETE FROM commentaires WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}
