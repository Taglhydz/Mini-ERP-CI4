/**
 * header.js — Header "hide on scroll down / show on scroll up"
 * Maintient la variable CSS --header-height sur :root.
 * Dépendances : aucune.
 */
(function () {
    'use strict';

    var navbar = document.getElementById('site-navbar');
    var mainEl = document.querySelector('body > main');
    if (!navbar) return;

    var navH    = 0;
    var lastY   = window.scrollY;
    var ticking = false;

    // ── CSS variable -----------------------------------------------------------
    function setHeaderVar(px) {
        document.documentElement.style.setProperty('--header-height', px + 'px');
    }

    // ── Mesure initiale (et à chaque redimensionnement) ────────────────────────
    function measureAndApply() {
        navH = navbar.offsetHeight;
        // Ne met à jour la variable que si le header est visible,
        // sinon la hauteur reste à 0 pour le panneau panier.
        if (!navbar.classList.contains('navbar-hidden')) {
            setHeaderVar(navH);
        }
        // Le padding de <main> suit toujours la vraie hauteur (évite le chevauchement).
        if (mainEl) mainEl.style.paddingTop = navH + 'px';
    }

    // Initialisation synchrone (script en fin de body = DOM prêt)
    measureAndApply();

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(measureAndApply).observe(navbar);
    }

    // ── Afficher / cacher ─────────────────────────────────────────────────────
    function showNavbar() {
        navbar.classList.remove('navbar-hidden');
        setHeaderVar(navH);
    }

    function hideNavbar() {
        navbar.classList.add('navbar-hidden');
        setHeaderVar(0);
    }

    // ── Gestionnaire de scroll ────────────────────────────────────────────────
    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;

        window.requestAnimationFrame(function () {
            var y = window.scrollY;

            if (y <= 2) {
                // Haut de page → toujours visible
                showNavbar();
            } else if (y > lastY + 5) {
                // Scroll vers le bas
                hideNavbar();
            } else if (y < lastY - 5) {
                // Scroll vers le haut
                showNavbar();
            }

            lastY   = y;
            ticking = false;
        });
    }, { passive: true });
})();
