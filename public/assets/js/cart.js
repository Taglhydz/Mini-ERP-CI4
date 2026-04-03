/**
 * cart.js — Panneau panier latéral non-bloquant pour Mini-ERP (v2)
 * Dépendances : Fetch API, window.BASE_URL (injecté par catalog.php)
 * Aucune dépendance Bootstrap Offcanvas — panel CSS flex uniquement.
 */
(function () {
    'use strict';

    // ── Références DOM ────────────────────────────────────────────────────────
    var panel       = document.getElementById('cart-panel');
    if (! panel) return; // Pas sur une page catalogue

    var slug        = panel.dataset.slug;
    var tab         = document.getElementById('cart-tab');
    var tabCount    = document.getElementById('cart-tab-count');
    var closeBtn    = document.getElementById('cart-panel-close');
    var itemsEl     = document.getElementById('cart-panel-items');
    var totalEl     = document.getElementById('cart-panel-total');
    var countEl     = document.getElementById('cart-panel-count');
    var checkoutBtn = document.getElementById('cart-panel-checkout-btn');

    // ── CSRF ──────────────────────────────────────────────────────────────────
    function csrfName()       { return document.querySelector('meta[name="csrf-name"]').content; }
    function csrfToken()      { return document.querySelector('meta[name="csrf-token"]').content; }
    function storeCsrf(data)  {
        if (data && data.csrf_token) {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
        }
    }

    // ── Contrôle du panneau ───────────────────────────────────────────────────
    function openPanel() {
        panel.classList.add('open');
        if (tab) tab.classList.add('d-none');
    }

    function closePanel() {
        panel.classList.remove('open');
        if (tab) tab.classList.remove('d-none');
    }

    function syncPanel(count) {
        if (count > 0) openPanel();
        else           closePanel();
    }

    // ── Appliquer les données panier dans le panneau ──────────────────────────
    function applyData(data) {
        var count = data.cart_count || 0;

        if (itemsEl)  itemsEl.innerHTML    = data.cart_html  || '';
        if (totalEl)  totalEl.textContent  = data.total_ttc  || '0,00 \u20ac';
        if (countEl)  countEl.textContent  = count;
        if (tabCount) {
            tabCount.textContent = count;
            tabCount.classList.toggle('empty', count === 0);
        }

        // Mettre à jour le badge navbar si présent
        var navBadge = document.querySelector('#nav-cart-btn .badge');
        if (navBadge) navBadge.textContent = count;

        // Bloquer le bouton commande si un article est en rupture de stock
        if (checkoutBtn) {
            var hasWarning = itemsEl && itemsEl.querySelector('.stock-warning') !== null;
            checkoutBtn.disabled = hasWarning;
            checkoutBtn.title    = hasWarning ? 'Un ou plusieurs articles sont en rupture ou stock insuffisant.' : '';
        }

        // Mettre à jour les boutons "Ajouter au panier" dans le catalogue
        if (data.cart_limits) {
            updateCatalogButtons(data.cart_limits);
        }

        syncPanel(count);
        bindItemEvents();
        storeCsrf(data);
    }

    // ── Mettre à jour l'état des boutons du catalogue selon les limites panier ─
    function updateCatalogButtons(limits) {
        document.querySelectorAll('.cart-add-form').forEach(function (form) {
            var pid = form.dataset.productId
                   || (form.querySelector('[name="product_id"]') || {}).value;
            if (! pid) return;

            var btn   = form.querySelector('[type="submit"]');
            if (! btn) return;

            var stock = parseInt(btn.dataset.stock, 10);
            if (isNaN(stock) || stock <= 0) return; // bouton déjà disabled côté PHP

            var limit  = limits[String(pid)];
            var at_max = limit && limit.at_max;

            if (at_max) {
                btn.disabled = true;
                btn.setAttribute('aria-disabled', 'true');
                btn.title    = 'Quantit\u00e9 maximale atteinte (' + (limit.stock || 0) + ' en stock)';
            } else {
                btn.disabled = false;
                btn.removeAttribute('aria-disabled');
                btn.title    = '';
            }
        });
    }

    // ── Requêtes HTTP ─────────────────────────────────────────────────────────
    function postForm(url, params) {
        params[csrfName()] = csrfToken();
        return fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type':     'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams(params).toString(),
        }).then(function (r) { return r.json(); });
    }

    function getJSON(url) {
        return fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).then(function (r) { return r.json(); });
    }

    // ── Actions panier ────────────────────────────────────────────────────────
    function addItem(productId) {
        return postForm(BASE_URL + 'shop/' + slug + '/cart/add', {
            product_id: productId,
            quantity:   1,
        }).then(function (data) {
            if (data.success) {
                applyData(data);
            } else {
                showToast(data.message || 'Erreur lors de l\'ajout au panier.', 'danger');
            }
            return data;
        }).catch(function (e) {
            console.error('Cart add error:', e);
            return { success: false };
        });
    }

    function updateItem(productId, qty) {
        return postForm(BASE_URL + 'shop/' + slug + '/cart/update', {
            product_id: productId,
            quantity:   qty,
        }).then(function (data) {
            if (data.success) applyData(data);
        }).catch(function (e) { console.error('Cart update error:', e); });
    }

    function removeItem(productId) {
        return postForm(BASE_URL + 'shop/' + slug + '/cart/remove', {
            product_id: productId,
        }).then(function (data) {
            if (data.success) applyData(data);
        }).catch(function (e) { console.error('Cart remove error:', e); });
    }

    // ── Lier les boutons des items (rebind après chaque mise à jour HTML) ─────
    function bindItemEvents() {
        if (! itemsEl) return;

        itemsEl.querySelectorAll('.btn-qty-dec').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id  = this.dataset.id;
                var row = itemsEl.querySelector('.cart-item[data-product-id="' + id + '"]');
                var qty = parseInt(row.querySelector('span').textContent.trim(), 10) - 1;
                updateItem(id, qty);
            });
        });

        itemsEl.querySelectorAll('.btn-qty-inc').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id  = this.dataset.id;
                var row = itemsEl.querySelector('.cart-item[data-product-id="' + id + '"]');
                var qty = parseInt(row.querySelector('span').textContent.trim(), 10) + 1;
                updateItem(id, qty);
            });
        });

        itemsEl.querySelectorAll('.btn-cart-remove').forEach(function (btn) {
            btn.addEventListener('click', function () { removeItem(this.dataset.id); });
        });
    }

    // ── Intercepter les formulaires "Ajouter au panier" ──────────────────────
    document.querySelectorAll('.cart-add-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var pid = form.querySelector('[name="product_id"]').value;
            var btn = form.querySelector('[type="submit"]');

            if (btn) {
                btn.disabled  = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Ajout...';
            }

            addItem(pid).then(function (data) {
                if (! btn) return;
                btn.disabled = false;
                if (data && data.success) {
                    btn.innerHTML = '<i class="bi bi-cart-check me-1"></i>Ajout\u00e9\u00a0!';
                    setTimeout(function () {
                        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Ajouter au panier';
                    }, 1600);
                } else {
                    btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Ajouter au panier';
                }
            });
        });
    });

    // ── Événements UI (tab, fermeture, commande) ──────────────────────────────
    if (closeBtn)    closeBtn.addEventListener('click', closePanel);
    if (tab)         tab.addEventListener('click', openPanel);
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            window.location.href = BASE_URL + 'shop/' + slug + '/checkout';
        });
    }

    // ── Chargement initial du résumé ─────────────────────────────────────────
    getJSON(BASE_URL + 'shop/' + slug + '/cart/summary')
        .then(applyData)
        .catch(function (e) { console.error('Cart init error:', e); });

})();
