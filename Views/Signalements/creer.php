<?php
// Views/signalements/creer.php
// Variables depuis SignalementController->creer() : $erreur, $categories

// Valeurs par défaut
$erreur     = $erreur     ?? '';
$categories = $categories ?? [];

// Correspondance quartier → commune
$quartiers_lome = [
    'Adidogomé' => 'Golfe 1',
    'Agbalépédogan' => 'Golfe 1',
    'Bè Kpota' => 'Golfe 1',
    'Bè Gbadago' => 'Golfe 1',
    'Bè Apeyémé' => 'Golfe 1',
    'Nyékonakpoè' => 'Golfe 1',
    'Tokoin' => 'Golfe 1',
    'Hanoukopé' => 'Golfe 1',
    'Hédzranawoé' => 'Golfe 1',
    'Akodesséwa' => 'Golfe 2',
    'Bè Afrikoko' => 'Golfe 2',
    'Gbossimé' => 'Golfe 2',
    'Djidjolé' => 'Golfe 2',
    'Agoe Nyivé' => 'Golfe 3',
    'Démakpoé' => 'Golfe 3',
    'Kégué' => 'Golfe 3',
    'Sanguéra' => 'Golfe 3',
    'Totsi' => 'Golfe 4',
    'Agoè' => 'Golfe 4',
    'Cacavéli' => 'Golfe 4',
    'Lomé 2000' => 'Golfe 4',
    'Wuiti' => 'Golfe 4',
    'Baguida' => 'Golfe 5',
    'Aflao Gakli' => 'Golfe 5',
    'Kodjoviakopé' => 'Golfe 5',
    'Dévime' => 'Golfe 5',
    'Agoè Assiyéyé' => 'Agoè Nyivé 1',
    'Agoè Zongo' => 'Agoè Nyivé 1',
    'Agoè Minamadou' => 'Agoè Nyivé 2',
    'Vakpossito' => 'Agoè Nyivé 2',
    'Gbénvié' => 'Agoè Nyivé 3',
    'Zanguéra' => 'Agoè Nyivé 3',
    'Agoè Togblékopé' => 'Agoè Nyivé 4',
    'Agoè Cacavi' => 'Agoè Nyivé 4',
    'Anfamé' => 'Agoè Nyivé 5',
    'Kpogan' => 'Agoè Nyivé 5',
    'Agoè Gakli' => 'Agoè Nyivé 6',
    'Kévé' => 'Agoè Nyivé 6',
];

// Trier les quartiers pour proposer une liste ordonnée
ksort($quartiers_lome, SORT_NATURAL | SORT_FLAG_CASE);

// Catégories simplifiées avec leurs IDs réels depuis la BDD
$cats_simplifiees = [
    ['id' => null, 'nom' => 'Voirie et routes',       'icone' => 'fa-road',          'canon' => 'Voirie / Routes',         'noms_bdd' => ['Voirie / Routes']],
    ['id' => null, 'nom' => 'Éclairage public',       'icone' => 'fa-lightbulb',    'canon' => 'Éclairage public',       'noms_bdd' => ['Éclairage public', 'Électricité']],
    ['id' => null, 'nom' => 'Collecte des ordures',   'icone' => 'fa-trash-can',    'canon' => 'Déchets / Hygiène',       'noms_bdd' => ['Déchets / Hygiène']],
    ['id' => null, 'nom' => 'Inondations', 'icone' => 'fa-water', 'canon' => 'Inondations', 'noms_bdd' => ['Inondations', 'Catastrophe naturelle']],
    ['id' => null, 'nom' => 'Espaces verts',          'icone' => 'fa-leaf',         'canon' => 'Espaces verts',           'noms_bdd' => ['Espaces verts']],
    ['id' => null, 'nom' => 'Divers',                 'icone' => 'fa-ellipsis-h',   'canon' => 'Autre',                   'noms_bdd' => ['Autre', 'Bâtiments publics']],
    ['id' => null, 'nom' => 'Vandalisme',             'icone' => 'fa-shield-halved', 'canon' => 'Sécurité',               'noms_bdd' => ['Sécurité']],
    ['id' => null, 'nom' => 'Bruit et nuisances',     'icone' => 'fa-volume-high',  'canon' => 'Sécurité',               'noms_bdd' => ['Sécurité']],
    ['id' => null, 'nom' => 'Eau et assainissement', 'icone' => 'fa-droplet', 'canon' => 'Assainissement', 'noms_bdd' => ['Assainissement', 'Eau potable']],
];

foreach ($cats_simplifiees as &$cs) {
    foreach ($categories as $cat) {
        if ($cat['nom'] === $cs['canon']) {
            $cs['id'] = $cat['id'];
            break;
        }
    }
    if ($cs['id'] === null) {
        foreach ($categories as $cat) {
            if (in_array($cat['nom'], $cs['noms_bdd'], true)) {
                $cs['id'] = $cat['id'];
                break;
            }
        }
    }
}
unset($cs);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un problème — MonQuartier TG</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Public/Css/header.css">
    <link rel="stylesheet" href="Public/Css/footer.css">
    <link rel="stylesheet" href="Public/Css/creer.css">
</head>

<body>

    <?php require_once __DIR__ . '/../../Layouts/header.php'; ?>

    <main class="page-creer">
        <div class="conteneur">

            <!-- En-tête -->
            <div class="page-entete">
                <a href="index.php?page=signalements" class="lien-retour">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <div>
                    <h1>Signaler un problème</h1>
                    <p>3 étapes — moins de 2 minutes</p>
                </div>
            </div>

            <!-- Message d'erreur -->
            <?php if (!empty($erreur)): ?>
                <div class="alerte-erreur">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                action="index.php?page=signalements&action=creer"
                enctype="multipart/form-data"
                class="formulaire">

                <!-- ══ ÉTAPE 1 : Quel type de problème ? ══ -->
                <div class="bloc-form">
                    <div class="bloc-titre">
                        <span class="bloc-num">1</span>
                        <div>
                            <h2>Quel est le problème ?</h2>
                            <p>Choisissez le type qui correspond le mieux</p>
                        </div>
                    </div>

                    <div class="grille-categories">
                        <?php foreach ($cats_simplifiees as $cat):
                            $cat_id  = $cat['id'] ?? '';
                            $checked = isset($_POST['categorie_id']) && $_POST['categorie_id'] == $cat_id;
                        ?>
                            <label class="cat-choix <?= $checked ? 'selectionne' : '' ?>">
                                <input type="radio" name="categorie_id"
                                    value="<?= htmlspecialchars($cat_id) ?>"
                                    <?= $checked ? 'checked' : '' ?>
                                    required>
                                <div class="cat-icone">
                                    <i class="fa-solid <?= $cat['icone'] ?>"></i>
                                </div>
                                <span class="cat-nom"><?= htmlspecialchars($cat['nom']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ══ ÉTAPE 2 : Où est le problème ? ══ -->
                <div class="bloc-form">
                    <div class="bloc-titre">
                        <span class="bloc-num">2</span>
                        <div>
                            <h2>Où se trouve le problème ?</h2>
                            <p>Indiquez le quartier — la commune se remplit automatiquement</p>
                        </div>
                    </div>

                    <div class="deux-colonnes">
                        <div class="champ">
                            <label for="champ-quartier">
                                <i class="fa-solid fa-map-pin"></i> Quartier *
                            </label>
                            <input type="text"
                                name="quartier"
                                id="champ-quartier"
                                list="liste-quartiers"
                                required
                                autocomplete="off"
                                placeholder="Tapez votre quartier…"
                                value="<?= htmlspecialchars($_POST['quartier'] ?? '') ?>"
                                oninput="remplirCommune(this.value)">
                            <datalist id="liste-quartiers">
                                <?php foreach ($quartiers_lome as $q => $c): ?>
                                    <option value="<?= htmlspecialchars($q) ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="champ">
                            <label for="champ-commune">
                                <i class="fa-solid fa-city"></i>
                                Commune <span class="auto-label">auto</span>
                            </label>
                            <input type="text"
                                name="commune"
                                id="champ-commune"
                                readonly
                                placeholder="Déduite du quartier"
                                value="<?= htmlspecialchars($_POST['commune'] ?? '') ?>"
                                class="champ-readonly">
                        </div>
                    </div>
                </div>

                <!-- ══ ÉTAPE 3 : Photo + description ══ -->
                <div class="bloc-form">
                    <div class="bloc-titre">
                        <span class="bloc-num">3</span>
                        <div>
                            <h2>Photo & description</h2>
                            <p>Une photo aide beaucoup — la description est facultative</p>
                        </div>
                    </div>

                    <!-- Photo -->
                    <div class="champ">
                        <label>
                            <i class="fa-solid fa-camera"></i>
                            Photo du problème
                            <span class="optionnel">(optionnel)</span>
                        </label>
                        <label class="btn-upload" for="champ-photo">
                            <i class="fa-solid fa-cloud-arrow-up" id="upload-icone"></i>
                            <span class="upload-texte" id="upload-texte">Appuyez pour prendre ou choisir une photo</span>
                            <span class="upload-sous">JPG, PNG ou WEBP — max 5 Mo</span>
                        </label>
                        <input type="file" name="photo" id="champ-photo"
                            accept="image/jpeg,image/png,image/webp"
                            onchange="afficherPhoto(this)">
                        <!-- Aperçu de la photo sélectionnée -->
                        <div id="apercu-photo" style="display:none; margin-top:10px;">
                            <img id="img-apercu" src="" alt="Aperçu"
                                style="max-width:100%; max-height:200px; border-radius:8px; object-fit:cover;">
                            <button type="button" class="btn-suppr-photo" onclick="supprimerPhoto()">
                                <i class="fa-solid fa-xmark"></i> Supprimer la photo
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="champ">
                        <label for="champ-description">
                            <i class="fa-solid fa-align-left"></i>
                            Description
                            <span class="optionnel">(optionnel)</span>
                        </label>
                        <textarea name="description"
                            id="champ-description"
                            rows="3"
                            placeholder="Décrivez brièvement : depuis quand ? Y a-t-il un danger immédiat ?"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <!--
                    NOTE : la priorité est retirée du formulaire citoyen.
                    Elle sera évaluée par l'agent lors du traitement.
                    On envoie 'normale' par défaut dans un champ caché.
                -->
                    <input type="hidden" name="priorite" value="normale">

                    <!--
                    NOTE : le titre est généré automatiquement côté serveur
                    à partir de la catégorie + quartier (voir SignalementController).
                    Si votre contrôleur exige un titre, décommentez ceci :

                    <input type="hidden" name="titre"
                           id="champ-titre-auto" value="">
                -->
                </div>

                <!-- Pied -->
                <div class="form-pied">
                    <a href="index.php?page=signalements" class="btn-annuler">Annuler</a>
                    <button type="submit" class="btn-soumettre">
                        <i class="fa-solid fa-paper-plane"></i> Envoyer
                    </button>
                </div>

            </form>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Layouts/footer.php'; ?>

    <script>
        // ── Correspondance quartier → commune ─────────────────────
        var quartierCommune = <?= json_encode($quartiers_lome, JSON_UNESCAPED_UNICODE) ?>;

        function remplirCommune(valeur) {
            var champCommune = document.getElementById('champ-commune');
            var cle = valeur.trim().toLowerCase();
            var trouve = '';

            for (var q in quartierCommune) {
                if (q.toLowerCase() === cle) {
                    trouve = quartierCommune[q];
                    break;
                }
            }

            champCommune.value = trouve;

            // Si le quartier n'est pas reconnu, on déverrouille
            // le champ commune pour que le citoyen puisse le saisir manuellement
            if (trouve === '') {
                champCommune.removeAttribute('readonly');
                champCommune.classList.remove('champ-readonly');
                champCommune.placeholder = 'Saisissez votre commune';
            } else {
                champCommune.setAttribute('readonly', true);
                champCommune.classList.add('champ-readonly');
            }
        }

        // ── Aperçu de la photo ────────────────────────────────────
        function afficherPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('img-apercu').src = e.target.result;
                    document.getElementById('apercu-photo').style.display = 'block';
                    document.getElementById('upload-texte').textContent = input.files[0].name;
                    document.getElementById('upload-icone').className = 'fa-solid fa-check';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function supprimerPhoto() {
            document.getElementById('champ-photo').value = '';
            document.getElementById('apercu-photo').style.display = 'none';
            document.getElementById('upload-texte').textContent = 'Appuyez pour prendre ou choisir une photo';
            document.getElementById('upload-icone').className = 'fa-solid fa-cloud-arrow-up';
        }

        // ── Sélection visuelle des catégories ────────────────────
        document.querySelectorAll('.cat-choix').forEach(function(label) {
            label.addEventListener('click', function() {
                document.querySelectorAll('.cat-choix').forEach(function(l) {
                    l.classList.remove('selectionne');
                });
                this.classList.add('selectionne');
            });
        });
    </script>

</body>

</html>