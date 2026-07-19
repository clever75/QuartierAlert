<?php
// ============================================================
//  helpers.php — Fonctions utilitaires globales
//  À inclure UNE SEULE FOIS dans index.php avant tout contrôleur
//
//  RÔLES DU SYSTÈME :
//    'citoyen'     — le grand public, signale des problèmes
//    'agent'       — agent municipal, traite UN service précis
//    'super_admin' — coordinateur, voit tout, gère les agents
//
//  HIÉRARCHIE :
//    super_admin > agent > citoyen
// ============================================================


// ============================================================
//  1. VÉRIFICATIONS DE RÔLE
// ============================================================

/**
 * L'utilisateur est-il connecté ?
 * On vérifie uniquement la présence de 'utilisateur_id' en session.
 * Cette clé n'est écrite que par AuthController après connexion réussie.
 */
function estConnecte(): bool
{
    return isset($_SESSION['utilisateur_id']);
}

/**
 * Est-ce un super_admin ?
 * Le super_admin voit TOUS les signalements de TOUTES les catégories
 * et peut créer/désactiver des comptes agents.
 */
function estSuperAdmin(): bool
{
    return estConnecte()
        && isset($_SESSION['role'])
        && $_SESSION['role'] === 'super_admin';
}

/**
 * Est-ce un agent ?
 * Un agent ne voit que les signalements de SON service.
 * Son service est stocké dans $_SESSION['service'].
 */
function estAgent(): bool
{
    return estConnecte()
        && isset($_SESSION['role'])
        && $_SESSION['role'] === 'agent';
}

/**
 * Est-ce un membre du personnel interne (agent OU super_admin) ?
 * Utilisé pour accorder l'accès au dashboard en général.
 */
function estPersonnel(): bool
{
    return estSuperAdmin() || estAgent();
}

/**
 * Quel est le service de l'agent connecté ?
 * Retourne null si l'utilisateur n'est pas un agent.
 */
function serviceAgent(): ?string
{
    return estAgent() ? ($_SESSION['service'] ?? null) : null;
}


// ============================================================
//  2. GARDIENS D'ACCÈS (à appeler en tête de chaque méthode)
// ============================================================

/**
 * Bloque si pas connecté.
 * Mémorise l'URL pour y revenir après connexion.
 */
function requiertConnexion(): void
{
    if (!estConnecte()) {
        $_SESSION['redirect_apres_login'] = $_SERVER['REQUEST_URI'];
        redirect('index.php?page=auth&action=login');
        exit;
    }
}

/**
 * Bloque si pas personnel interne (ni agent ni super_admin).
 * Utilisé pour les pages du dashboard accessibles aux deux.
 */
function requiertPersonnel(): void
{
    requiertConnexion();

    if (!estPersonnel()) {
        afficherErreur403();
    }
}

/**
 * Bloque si pas super_admin.
 * Utilisé pour les actions réservées au coordinateur :
 * créer des agents, voir tous les signalements, statistiques globales.
 */
function requiertSuperAdmin(): void
{
    requiertConnexion();

    if (!estSuperAdmin()) {
        afficherErreur403();
    }
}

/**
 * Affiche une page d'erreur 403 propre et stoppe l'exécution.
 * Fonction interne — appelée par les gardiens ci-dessus.
 */
function afficherErreur403(): never
{
    http_response_code(403);
    echo '<!DOCTYPE html><html lang="fr"><head>
        <meta charset="UTF-8">
        <title>Accès interdit — Mia Dzra Do</title>
        <style>
            body { font-family: sans-serif; text-align: center;
                   padding: 80px 20px; background: #f4f6f4; color: #1a2e22; }
            h1   { font-size: 4rem; color: #1B4332; margin-bottom: 8px; }
            p    { color: #5a7364; margin-bottom: 24px; font-size: 1.1rem; }
            a    { display: inline-block; padding: 12px 28px;
                   background: #1B4332; color: #fff; border-radius: 8px;
                   text-decoration: none; font-weight: 600; }
        </style>
    </head><body>
        <h1>403</h1>
        <p>Vous n\'avez pas les droits pour accéder à cette page.</p>
        <a href="index.php?page=accueil">← Retour à l\'accueil</a>
    </body></html>';
    exit;
}


// ============================================================
//  3. NAVIGATION
// ============================================================

/**
 * Redirige et stoppe PHP immédiatement.
 * Sans exit, PHP continuerait à exécuter le code suivant
 * même après avoir envoyé le header de redirection.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}


// ============================================================
//  4. HELPERS D'AFFICHAGE
// ============================================================

/**
 * Badge coloré selon le statut du signalement.
 * Centralisé ici pour ne pas dupliquer dans chaque vue.
 *
 * @return array{label: string, classe: string, couleur: string}
 */
function badgeStatut(string $statut): array
{
    return match ($statut) {
        'nouveau'  => ['label' => 'Nouveau',  'classe' => 'badge-nouveau',  'couleur' => '#E74C3C'],
        'en_cours' => ['label' => 'En cours', 'classe' => 'badge-encours',  'couleur' => '#FF9F1C'],
        'resolu'   => ['label' => 'Résolu',   'classe' => 'badge-resolu',   'couleur' => '#2D6A4F'],
        'rejete'   => ['label' => 'Rejeté',   'classe' => 'badge-rejete',   'couleur' => '#C0392B'],
        default    => ['label' => ucfirst($statut), 'classe' => 'badge-defaut', 'couleur' => '#888'],
    };
}

/**
 * Transforme une date SQL en texte relatif.
 * "il y a 3 minutes", "il y a 2 jours", "le 12 jan. 2025"
 */
function tempsRelatif(string $date_sql): string
{
    $diff = time() - strtotime($date_sql);

    if ($diff < 60)         return 'il y a quelques secondes';
    if ($diff < 3600)       return 'il y a ' . floor($diff / 60) . ' min';
    if ($diff < 86400)      return 'il y a ' . floor($diff / 3600) . ' h';
    if ($diff < 30 * 86400) return 'il y a ' . floor($diff / 86400) . ' jour' . (floor($diff / 86400) > 1 ? 's' : '');

    return 'le ' . date('d M Y', strtotime($date_sql));
}

function categorieSimplifiee(string $nom): array
{
    $groupes = [
        ['nom' => 'Voirie et routes',       'icone' => 'fa-road',          'noms' => ['Voirie / Routes']],
        ['nom' => 'Éclairage public',       'icone' => 'fa-lightbulb',    'noms' => ['Éclairage public', 'Électricité']],
        ['nom' => 'Collecte des ordures',   'icone' => 'fa-trash-can',    'noms' => ['Déchets / Hygiène']],
        ['nom' => 'Inondations', 'icone' => 'fa-water', 'noms' => ['Inondations', 'Catastrophe naturelle']],
        ['nom' => 'Espaces verts',          'icone' => 'fa-leaf',         'noms' => ['Espaces verts']],
        ['nom' => 'Divers',                 'icone' => 'fa-ellipsis-h',   'noms' => ['Autre', 'Bâtiments publics']],
        ['nom' => 'Vandalisme',             'icone' => 'fa-shield-halved', 'noms' => ['Sécurité']],
        ['nom' => 'Bruit et nuisances',     'icone' => 'fa-volume-high',  'noms' => ['Sécurité']],
        ['nom' => 'Eau et assainissement', 'icone' => 'fa-droplet',      'noms' => ['Eau potable']],
    ];

    foreach ($groupes as $groupe) {
        if ($nom === $groupe['nom'] || in_array($nom, $groupe['noms'], true)) {
            return ['nom' => $groupe['nom'], 'icone' => $groupe['icone']];
        }
    }

    return ['nom' => $nom, 'icone' => 'fa-tag'];
}

/**
 * Sécurise une chaîne contre les attaques XSS.
 * Toujours utiliser h() avant d'afficher une variable dans le HTML.
 *
 * XSS = un utilisateur malveillant soumet <script>volerCookies()</script>
 * comme contenu. Sans h(), ce script s'exécute dans le navigateur
 * de tous les visiteurs. Avec h(), il s'affiche comme texte inoffensif.
 */
function categoriesSignalementDisponibles(array $categories): array
{
    $groupes = [
        ['nom' => 'Voirie et routes',       'icone' => 'fa-road',          'canon' => 'Voirie / Routes',         'noms' => ['Voirie / Routes'], 'id' => null],
        ['nom' => 'Éclairage public',       'icone' => 'fa-lightbulb',    'canon' => 'Éclairage public',       'noms' => ['Éclairage public', 'Électricité'], 'id' => null],
        ['nom' => 'Collecte des ordures',   'icone' => 'fa-trash-can',    'canon' => 'Déchets / Hygiène',       'noms' => ['Déchets / Hygiène'], 'id' => null],
        ['nom' => 'Inondations', 'icone' => 'fa-water', 'canon' => 'Inondations', 'noms' => ['Inondations', 'Catastrophe naturelle'], 'id' => null],
        ['nom' => 'Espaces verts',          'icone' => 'fa-leaf',         'canon' => 'Espaces verts',           'noms' => ['Espaces verts'], 'id' => null],
        ['nom' => 'Divers',                 'icone' => 'fa-ellipsis-h',   'canon' => 'Autre',                   'noms' => ['Autre', 'Bâtiments publics'], 'id' => null],
        ['nom' => 'Vandalisme',             'icone' => 'fa-shield-halved', 'canon' => 'Sécurité',               'noms' => ['Sécurité'], 'id' => null],
        ['nom' => 'Bruit et nuisances',     'icone' => 'fa-volume-high',  'canon' => 'Sécurité',               'noms' => ['Sécurité'], 'id' => null],
        ['nom' => 'Eau et assainissement', 'icone' => 'fa-droplet',      'canon' => 'Eau potable',            'noms' => ['Eau potable'], 'id' => null],
    ];

    foreach ($groupes as &$groupe) {
        $groupe_ids = [];

        foreach ($categories as $cat) {
            if ($cat['nom'] === $groupe['canon'] || in_array($cat['nom'], $groupe['noms'], true)) {
                $groupe_ids[] = (int)$cat['id'];
                if ($cat['nom'] === $groupe['canon']) {
                    $groupe['id'] = $cat['id'];
                }
            }
        }

        if ($groupe['id'] === null && !empty($groupe_ids)) {
            $groupe['id'] = $groupe_ids[0];
        }
        $groupe['ids'] = $groupe_ids;
    }
    unset($groupe);

    $groupes = array_values(
        array_filter($groupes, function ($groupe) {
            return $groupe['id'] !== null;
        })
    );

    return $groupes;
}

function categorieSignalementIdsPourFiltre(array $categories, int $categorie_id): array
{
    $groupes = [
        ['canon' => 'Voirie / Routes',       'noms' => ['Voirie / Routes']],
        ['canon' => 'Éclairage public',       'noms' => ['Éclairage public', 'Électricité']],
        ['canon' => 'Déchets / Hygiène',       'noms' => ['Déchets / Hygiène']],
        ['canon' => 'Inondations', 'noms' => ['Inondations', 'Catastrophe naturelle']],
        ['canon' => 'Assainissement', 'noms' => ['Assainissement', 'Eau potable']],
        ['canon' => 'Espaces verts',           'noms' => ['Espaces verts']],
        ['canon' => 'Autre',                   'noms' => ['Autre', 'Bâtiments publics']],
        ['canon' => 'Sécurité',               'noms' => ['Sécurité']],
        ['canon' => 'Eau potable',            'noms' => ['Eau potable']],
    ];

    foreach ($groupes as $groupe) {
        $ids = [];
        foreach ($categories as $cat) {
            if ($cat['nom'] === $groupe['canon'] || in_array($cat['nom'], $groupe['noms'], true)) {
                $ids[] = (int)$cat['id'];
            }
        }

        if (in_array($categorie_id, $ids, true)) {
            return $ids;
        }
    }

    return [$categorie_id];
}

/**
 * Sécurise une chaîne contre les attaques XSS.
 * Toujours utiliser h() avant d'afficher une variable dans le HTML.
 *
 * XSS = un utilisateur malveillant soumet <script>volerCookies()</script>
 * comme contenu. Sans h(), ce script s'affiche dans le navigateur
 * de tous les visiteurs. Avec h(), il s'affiche comme texte inoffensif.
 */
function h(string $chaine): string
{
    return htmlspecialchars($chaine, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Libellé lisible du service d'un agent.
 * Utilisé dans les vues pour afficher "Voirie" plutôt que "voirie".
 */
function servicesOfficiels(): array
{
    return [
        'DGTP' => [
            'label'      => 'Direction Générale des Travaux Publics (DGTP)',
            'categories' => ['Voirie et routes'],
            'icone'      => 'fa-road',
        ],
        'CEET' => [
            'label'      => 'Compagnie Énergie Électrique du Togo (CEET)',
            'categories' => ['Éclairage public'],
            'icone'      => 'fa-bolt',
        ],
        'AGETUR' => [
            'label'      => "Agence d'Exécution des Travaux Urbains (AGETUR)",
            'categories' => ['Collecte des ordures', 'Espaces verts'],
            'icone'      => 'fa-city',
        ],
        'TDE' => [
            'label'      => 'Togolaise des Eaux (TdE)',
            'categories' => ['Eau et assainissement'],
            'icone'      => 'fa-droplet',
        ],
        'DGSCGC' => [
            'label'      => 'Direction Générale de la Sécurité Civile (DGSCGC)',
            'categories' => ['Inondations'],
            'icone'      => 'fa-shield-halved',
        ],
        'Police_Nationale' => [
            'label'      => 'Police Nationale',
            'categories' => ['Vandalisme', 'Bruit et nuisances'],
            'icone'      => 'fa-shield',
        ],
        'Mairie' => [
            'label'      => 'Mairie de Lomé',
            'categories' => ['Divers'],
            'icone'      => 'fa-building-columns',
        ],
    ];
}

function libelleService(string $service): string
{
    return match ($service) {
        'voirie'                         => 'Voirie / Routes',
        'hygiene'                        => 'Déchets / Hygiène',
        'eau'                            => 'Eau potable (TdE)',
        'electricite'                    => 'Électricité (CEET)',
        'eclairage'                      => 'Éclairage public',
        'securite'                       => 'Sécurité',
        'Mairie'                         => 'Mairie de Lomé',
        'CEET'                           => 'CEET — Compagnie Énergie Électrique du Togo',
        'TDE'                            => 'TdE — Togolaise des Eaux',
        'ANASAP'                         => 'ANASAP — Agence Nationale d\'Assainissement',
        'DGTP'                           => 'DGTP — Direction Générale des Travaux Publics',
        'AGETUR'                         => 'AGETUR — Agence d\'Exécution des Travaux Urbains',
        'DGSCGC'                         => 'DGSCGC — Direction Générale de la Sécurité Civile et de la Gestion des Crises',
        'Police_Nationale_Gendarmerie'   => 'Police Nationale / Gendarmerie',
        'Police_Nationale'               => 'Police Nationale',
        'Police'                         => 'Police Nationale',
        'Gendarmerie'                    => 'Gendarmerie Nationale',
        'Gendarmerie Nationale'          => 'Gendarmerie Nationale',
        default                          => ucfirst($service),
    };
}
