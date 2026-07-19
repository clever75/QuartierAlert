<?php

// ============================================================
//  Model : votes.php
//  Table : votes
//  Colonnes : id, utilisateur_id, signalement_id, date_vote
//  Contrainte UNIQUE : (utilisateur_id, signalement_id)
// ============================================================

function voterSignalement(PDO $pdo, int $utilisateur_id, int $signalement_id): bool {
    // INSERT IGNORE respecte la contrainte UNIQUE sans erreur
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO votes (utilisateur_id, signalement_id, date_vote)
        VALUES (:utilisateur_id, :signalement_id, NOW())
    ");
    $stmt->execute([
        ':utilisateur_id' => $utilisateur_id,
        ':signalement_id' => $signalement_id,
    ]);
    return $stmt->rowCount() > 0; // true = vote ajouté, false = déjà voté
}

function retirerVote(PDO $pdo, int $utilisateur_id, int $signalement_id): bool {
    $stmt = $pdo->prepare("
        DELETE FROM votes
        WHERE utilisateur_id = :utilisateur_id
          AND signalement_id = :signalement_id
    ");
    return $stmt->execute([
        ':utilisateur_id' => $utilisateur_id,
        ':signalement_id' => $signalement_id,
    ]);
}

function aDejaVote(PDO $pdo, int $utilisateur_id, int $signalement_id): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM votes
        WHERE utilisateur_id = :utilisateur_id
          AND signalement_id = :signalement_id
    ");
    $stmt->execute([
        ':utilisateur_id' => $utilisateur_id,
        ':signalement_id' => $signalement_id,
    ]);
    return (int) $stmt->fetchColumn() > 0;
}

function countVotes(PDO $pdo, int $signalement_id): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE signalement_id = :id");
    $stmt->execute([':id' => $signalement_id]);
    return (int) $stmt->fetchColumn();
}

function toggleVote(PDO $pdo, int $utilisateur_id, int $signalement_id): array {
    if (aDejaVote($pdo, $utilisateur_id, $signalement_id)) {
        retirerVote($pdo, $utilisateur_id, $signalement_id);
        $action = 'retire';
    } else {
        voterSignalement($pdo, $utilisateur_id, $signalement_id);
        $action = 'ajoute';
    }
    return [
        'action'   => $action,
        'nb_votes' => countVotes($pdo, $signalement_id),
    ];
}