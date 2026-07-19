<?php

// ============================================================
//  Model : notifications.php
//  Table : notifications
//  Colonnes : id, message, est_lu, date_envoi,
//             utilisateur_id, signalement_id, type
// ============================================================

function creerNotification(PDO $pdo, int $utilisateur_id, string $message, string $type, int $signalement_id = null): bool {
    $stmt = $pdo->prepare("
        INSERT INTO notifications (message, est_lu, date_envoi, utilisateur_id, signalement_id, type)
        VALUES (:message, 0, NOW(), :utilisateur_id, :signalement_id, :type)
    ");
    return $stmt->execute([
        ':message'        => $message,
        ':utilisateur_id' => $utilisateur_id,
        ':signalement_id' => $signalement_id,
        ':type'           => $type,
    ]);
}

// Notifie le citoyen auteur du signalement
function notifierCitoyen(PDO $pdo, int $signalement_id, string $message, string $type): bool {
    $stmt = $pdo->prepare("SELECT utilisateur_id FROM signalements WHERE id = :id");
    $stmt->execute([':id' => $signalement_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return false;

    return creerNotification($pdo, $row['utilisateur_id'], $message, $type, $signalement_id);
}

function getNotifications(PDO $pdo, int $utilisateur_id): array {
    $stmt = $pdo->prepare("
        SELECT
            n.id,
            n.message,
            n.est_lu,
            n.date_envoi,
            n.type,
            s.titre AS signalement_titre
        FROM notifications n
        LEFT JOIN signalements s ON s.id = n.signalement_id
        WHERE n.utilisateur_id = :uid
        ORDER BY n.date_envoi DESC
        LIMIT 20
    ");
    $stmt->execute([':uid' => $utilisateur_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countNotificationsNonLues(PDO $pdo, int $utilisateur_id): int {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM notifications
        WHERE utilisateur_id = :uid AND est_lu = 0
    ");
    $stmt->execute([':uid' => $utilisateur_id]);
    return (int) $stmt->fetchColumn();
}

function marquerToutesLues(PDO $pdo, int $utilisateur_id): bool {
    $stmt = $pdo->prepare("
        UPDATE notifications SET est_lu = 1
        WHERE utilisateur_id = :uid
    ");
    return $stmt->execute([':uid' => $utilisateur_id]);
}

function marquerUneLue(PDO $pdo, int $notification_id): bool {
    $stmt = $pdo->prepare("UPDATE notifications SET est_lu = 1 WHERE id = :id");
    return $stmt->execute([':id' => $notification_id]);
}