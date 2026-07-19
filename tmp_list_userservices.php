<?php
require 'config/database.php';
$stmt = $pdo->query('SELECT DISTINCT service FROM utilisateurs ORDER BY service');
foreach ($stmt as $row) {
    echo '[' . ($row['service'] ?? 'NULL') . ']\n';
}
