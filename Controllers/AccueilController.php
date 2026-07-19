<?php
// ============================================================
//  AccueilController.php
// ============================================================
class AccueilController {

    public function __construct(private PDO $pdo) {}

    public function index(): void {
        $categories   = getCategories($this->pdo);
        $signalements = getSignalements($this->pdo, []);
        $derniers     = array_slice($signalements, 0, 3);
        $stats        = statsSignalements($this->pdo);

        require __DIR__ . '/../Views/accueil.php';
    }
}