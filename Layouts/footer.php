<?php
// Layouts/footer.php
?>

<footer class="footer">
    <div class="footer-inner">

        <!-- ── COLONNE 1 : Brand ─────────────────────── -->
        <div class="footer-brand">
            <a href="index.php?page=accueil" class="footer-logo">
                <i class="fa-solid fa-map-location-dot"></i>
                <span><strong>MIA</strong> DZRA DO</span>
            </a>
            <p class="footer-desc">
                Plateforme citoyenne togolaise pour signaler les
                problèmes de votre quartier à la mairie.
                Signalez. Suivez. Améliorez.
            </p>
            <span class="footer-pays">🇹🇬 Lomé, Togo</span>
        </div>

        <!-- ── COLONNE 2 : Navigation ────────────────── -->
        <div class="footer-col">
            <h4 class="footer-titre">Navigation</h4>
            <ul class="footer-liste">
                <li><a href="index.php?page=accueil"><i class="fa-solid fa-house"></i> Accueil</a></li>
                <li><a href="index.php?page=signalements"><i class="fa-solid fa-list"></i> Signalements</a></li>
                <li><a href="index.php?page=signalements&action=creer"><i class="fa-solid fa-plus"></i> Signaler un problème</a></li>
                <li><a href="index.php?page=auth&action=login"><i class="fa-solid fa-right-to-bracket"></i> Connexion</a></li>
                <li><a href="index.php?page=auth&action=register"><i class="fa-solid fa-user-plus"></i> S'inscrire</a></li>
            </ul>
        </div>

        <!-- ── COLONNE 3 : Organismes ────────────────── -->
        <div class="footer-col">
            <h4 class="footer-titre">Organismes compétents</h4>
            <ul class="footer-liste">
                <li>
                    <a href="https://www.tde.tg" target="_blank" rel="noopener">
                        <i class="fa-solid fa-droplet"></i>
                        Eau — TdE
                        <span class="footer-tel">80 00 30 00</span>
                    </a>
                </li>
                <li>
                    <a href="https://www.ceet.tg" target="_blank" rel="noopener">
                        <i class="fa-solid fa-bolt"></i>
                        Électricité — CEET
                        <span class="footer-tel">22 20 82 20</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=signalements&categorie_id=1">
                        <i class="fa-solid fa-trash-can"></i>
                        Déchets — AGETUR
                    </a>
                </li>
                <li>
                    <a href="index.php?page=signalements&categorie_id=4">
                        <i class="fa-solid fa-road"></i>
                        Voirie — Mairie de Lomé
                    </a>
                </li>
            </ul>
        </div>

        <!-- ── COLONNE 4 : Urgences ──────────────────── -->
        <div class="footer-col">
            <h4 class="footer-titre">Urgences</h4>
            <div class="footer-urgences">
                <a href="tel:117" class="urgence">
                    <div class="urgence-icone urgence-police">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="urgence-label">Police secours</div>
                        <div class="urgence-num">117</div>
                    </div>
                </a>
                <a href="tel:118" class="urgence">
                    <div class="urgence-icone urgence-pompiers">
                        <i class="fa-solid fa-fire-extinguisher"></i>
                    </div>
                    <div>
                        <div class="urgence-label">Pompiers</div>
                        <div class="urgence-num">118</div>
                    </div>
                </a>
                <a href="tel:111" class="urgence">
                    <div class="urgence-icone urgence-samu">
                        <i class="fa-solid fa-truck-medical"></i>
                    </div>
                    <div>
                        <div class="urgence-label">SAMU</div>
                        <div class="urgence-num">111</div>
                    </div>
                </a>
                <a href="tel:8000300" class="urgence">
                    <div class="urgence-icone urgence-eau">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div>
                        <div class="urgence-label">TdE (eau)</div>
                        <div class="urgence-num">80 00 30 00</div>
                    </div>
                </a>
                <a href="tel:22208220" class="urgence">
                    <div class="urgence-icone urgence-elec">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="urgence-label">CEET (panne élec.)</div>
                        <div class="urgence-num">22 20 82 20</div>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <!-- ── BAS ───────────────────────────────────────── -->
    <div class="footer-bas">
        <span>© <?= date('Y') ?> Mia Dzra Do — Tous droits réservés</span>
        <span class="footer-bas-sep">·</span>
        <span>Plateforme citoyenne togolaise</span>
    </div>
</footer>