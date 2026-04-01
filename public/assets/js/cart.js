/**
 * cart.js — Logique overlay panier AJAX pour Mini-ERP
 * Dépendances : Bootstrap 5 (Offcanvas), Fetch API
 */
(function () {
    'use strict';

    // ── Récupération des éléments ────────────────────────────────────────────
    var offcanvasEl = document.getElementById('cartOffcanvas');
    if (! offcanvasEl) return; // Pas sur une page shop

    var slug           = offcanvasEl.dataset.slug;
    var itemsContainer = document.getElementById('offcanvas-cart-items');
    var totalEl        = document.getElementById('offcanvas-cart-total');
    var countBadge     = document.getElementById('offcanvas-cart-count-badge');
    var navCartBtn     = document.getElementById('nav-cart-btn');
    var checkoutBtn    = document.getElementById('offcanvas-cart-checkout-btn');
    var bsOffcanvas    = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);

    // ── CSRF ─────────────────────────────────────────────────────────────────
    function getCsrfName()  { return document.querySelector('meta[name="csrf-name"]').content; }
    function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').content; }

    // ── Requête fetch POST ────────────────────────────────────────────────────
    function postJson(url, data) {
        var body = new FormData();
        body.append(getCsrfName(), getCsrfToken());
        Object.keys(data).forEach(function (k) { body.append(k, data[k]); });

        return fetch(url, {
            method:  'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body:    body,
        }).then(function (r) { return r.json(); });
    }

    // ── Requête fetch GET ─────────────────────────────────────────────────────
    function getJson(url) {
        return fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).then(function (r) { return r.json(); });
    }

    // ── Base URL (évite de la coder en dur) ───────────────────────────────────
    function shopUrl(path) {
        return document.querySelector('base')
            ? document.querySelector('base').href.replace(/\/$/, '') + '/' + path
            : window.location.origin + '/' + path;
    }

    // ── Mise à jour du badge navbar ───────────────────────────────────────────
    function updateBadge(count) {
        if (countBadge) {
            countBadge.textContent   = count > 0 ? count : '';
            countBadge.style.display = count > 0 ? '' : 'none';
        }
        if (navCartBtn) {
            var badge = navCartBtn.querySelector('.badge');
            if (! badge && count > 0) {
                badge           = document.createElement('span');
                badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                badge.style     = 'font-size:.6rem;min-width:1.2em;padding:.2em .4em;';
                navCartBtn.style.position = 'relative';
                navCartBtn.appendChild(badge);
            }
            if (badge) {
                badge.textContent   = count > 0 ? count : '';
                badge.style.display = count > 0 ? '' : 'none';
            }
        }
    }

    // ── Injecte le HTML dans l'offcanvas ─────────────────────────────────────
    function applyCartData(data) {
        if (itemsContainer) itemsContainer.innerHTML = data.cart_html;
        if (totalEl)        totalEl.textContent       = data.total_ttc;
        updateBadge(data.cart_count);
        // Remettre à jour le CSRF si CI4 régénère le token (mode rotate)
        var newCsrf = data.csrf_token;
        if (newCsrf) {
            var metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) metaToken.content = newCsrf;
        }
        bindItemEvents();
    }

    // ── Charge le résumé ──────────────────────────────────────────────────────
    function loadSummary() {
        getJson(shopUrl('shop/' + slug + '/cart/summary'))
            .then(applyCartData)
            .catch(function () {
                if (itemsContainer) {
                    itemsContainer.innerHTML = '<p class="text-danger p-3 small">Erreur de chargement.</p>';
                }
            });
    }

    // ── Bouton navbar → ouvre l'offcanvas ─────────────────────────────────────
    if (navCartBtn) {
        navCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            bsOffcanvas.show();
        });
    }

    // ── Charger le résumé à l'ouverture de l'offcanvas ───────────────────────
    offcanvasEl.addEventListener('show.bs.offcanvas', loadSummary);

    // ── Formulaires "Ajouter au panier" → AJAX ────────────────────────────────
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (! form.matches('form[action*="/cart/add"]')) return;

        e.preventDefault();

        var productIdInput = form.querySelector('[name="product_id"]');
        var quantityInput  = form.querySelector('[name="quantity"]');
        if (! productIdInput) return;

        var productId = productIdInput.value;
        var quantity  = quantityInput ? quantityInput.value : 1;

        var btn = form.querySelector('[type="submit"]');
        if (btn) {
            btn.disabled     = true;
            btn.innerHTML    = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Ajout...';
        }

        postJson(form.action, { product_id: productId, quantity: quantity })
            .then(function (data) {
                applyCartData(data);
                if (btn) {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="bi bi-cart-check me-1"></i>Ajouté !';
                    setTimeout(function () {
                        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Ajouter au panier';
                    }, 1600);
                }
                bsOffcanvas.show();
            })
            .catch(function () {
                if (btn) {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Ajouter au panier';
                }
            });
    });

    // ── Délégation événements items du panier (rebind après mise à jour HTML) ─
    function bindItemEvents() {
        if (! itemsContainer) return;

        itemsContainer.querySelectorAll('.btn-qty-dec').forEach(function (btn) {
            btn.replaceWith(btn.cloneNode(true)); // retirer anciens listeners
        });
        itemsContainer.querySelectorAll('.btn-qty-inc').forEach(function (btn) {
            btn.replaceWith(btn.cloneNode(true));
        });
        itemsContainer.querySelectorAll('.btn-cart-remove').forEach(function (btn) {
            btn.replaceWith(btn.cloneNode(true));
        });

        itemsContainer.querySelectorAll('.btn-qty-dec').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var productId = this.dataset.id;
                var qtySpan   = this.closest('.d-flex').querySelector('span');
                var newQty    = Math.max(0, parseInt(qtySpan.textContent.trim(), 10) - 1);
                changeQty(productId, newQty);
            });
        });

        itemsContainer.querySelectorAll('.btn-qty-inc').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var productId = this.dataset.id;
                var qtySpan   = this.closest('.d-flex').querySelector('span');
                var newQty    = parseInt(qtySpan.textContent.trim(), 10) + 1;
                changeQty(productId, newQty);
            });
        });

        itemsContainer.querySelectorAll('.btn-cart-remove').forEach(function (btn) {
            btn.addEventListener('click', function () {
                removeItem(this.dataset.id);
            });
        });
    }

    function changeQty(productId, qty) {
        postJson(shopUrl('shop/' + slug + '/cart/update'), {
            product_id: productId,
            quantity:   qty,
        }).then(applyCartData);
    }

    function removeItem(productId) {
        postJson(shopUrl('shop/' + slug + '/cart/remove'), {
            product_id: productId,
        }).then(applyCartData);
    }

    // ── Bouton "Passer la commande" dans l'overlay ────────────────────────────
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            window.location.href = shopUrl('shop/' + slug + '/checkout');
        });
    }

})();
