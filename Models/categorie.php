<?php

// ============================================================
//  Model : categories.php
//  Table : categories
//  Colonnes : id, nom, icone, couleur, service_responsable
// ============================================================

function getCategories(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM categories ORDER BY nom ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoriesByService(PDO $pdo, string $service): array
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE service_responsable = :service ORDER BY nom");
    $stmt->execute([':service' => $service]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getServicesResponsables(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT service_responsable,
                GROUP_CONCAT(nom ORDER BY nom SEPARATOR ' / ') AS categories
         FROM categories
         WHERE service_responsable IS NOT NULL
         GROUP BY service_responsable
         ORDER BY service_responsable"
    );
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function getCategorie(PDO $pdo, int $id): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
