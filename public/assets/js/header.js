/**
 * header.js — Header "hide on scroll down / show on scroll up"
 * Maintient les variables CSS :
 *   --header-height      : 0 quand caché, hauteur réelle quand visible
 *   --footer-visible-h   : portion du footer visible dans le viewport (px)
 * Dépendances : aucune.
 */
(function () {
    'use strict';

    var navbar  = document.getElementById('site-navbar');
    var footerEl = document.querySelector('body > footer, .footer-public');
    var mainEl  = document.querySelector('body > main');
    if (!navbar) return;

    var navH          = 0;
    var lastY         = window.scrollY;
    var ticking       = false;
    var footerVisible = 0;

    var root = document.documentElement;

    // ── CSS variables ──────────────────────────────────────────────────────────
    function setHeaderVar(px)  { root.style.setProperty('--header-height', px + 'px'); }
    function setFooterVar(px)  { root.style.setProperty('--footer-visible-h', px + 'px'); }

    // ── Mesure du footer visible ───────────────────────────────────────────────
    function measureFooter() {
        if (!footerEl) { footerVisible = 0; return; }
        var rect = footerEl.getBoundingClientRect();
        // Nombre de pixels du footer actuellement dans le viewport
        footerVisible = Math.max(0, Math.min(rect.height, window.innerHeight - rect.top));
        setFooterVar(footerVisible);
    }

    // ── Mesure initiale navbar (et à chaque resize) ─────────────────────────
    function measureAndApply() {
        navH = navbar.offsetHeight;
        if (!navbar.classList.contains('navbar-hidden')) {
            setHeaderVar(navH);
        }
        if (mainEl) mainEl.style.paddingTop = navH + 'px';
        measureFooter();
    }

    measureAndApply();

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(measureAndApply).observe(navbar);
    }

    // ── Afficher / cacher navbar ──────────────────────────────────────────────
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
                showNavbar();
            } else if (y > lastY + 5) {
                hideNavbar();
            } else if (y < lastY - 5) {
                showNavbar();
            }

            measureFooter();

            lastY   = y;
            ticking = false;
        });
    }, { passive: true });
})();
