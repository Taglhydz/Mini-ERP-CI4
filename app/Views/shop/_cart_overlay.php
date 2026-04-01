<?php
/**
 * Overlay panier lateral (Offcanvas Bootstrap 5).
 * Variables attendues : $company (array), $slug (string)
 */
?>
<!-- ── Offcanvas Panier ───────────────────────────────────────────────────── -->
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="cartOffcanvas"
     aria-labelledby="cartOffcanvasLabel"
     data-slug="<?= esc($company['slug']) ?>"
     style="width:min(420px, 100vw);">

    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="cartOffcanvasLabel">
            <i class="bi bi-cart3 text-primary"></i>
            Mon panier
            <span id="offcanvas-cart-count-badge"
                  class="badge bg-primary rounded-pill"
                  style="font-size:.75rem;"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>

    <!-- Corps scrollable -->
    <div class="offcanvas-body d-flex flex-column p-0">

        <!-- Items (injectés par JS) -->
        <div id="offcanvas-cart-items" class="flex-grow-1 overflow-auto px-3 py-2">
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <!-- Pied de panier -->
        <div class="p-3 border-top bg-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold text-muted">Total TTC</span>
                <span id="offcanvas-cart-total" class="fw-bold text-primary fs-5">—</span>
            </div>
            <a id="offcanvas-cart-full-link"
               href="<?= base_url('shop/' . esc($company['slug']) . '/cart') ?>"
               class="btn btn-outline-primary w-100 mb-2">
                <i class="bi bi-cart me-1"></i>Voir le panier complet
            </a>
            <button id="offcanvas-cart-checkout-btn"
                    type="button"
                    class="btn btn-success w-100"
                    data-slug="<?= esc($company['slug']) ?>"
                    data-is-logged-in="<?= session()->get('isLoggedIn') ? '1' : '0' ?>">
                <i class="bi bi-bag-check me-1"></i>Passer la commande
            </button>
        </div>
    </div>
</div>
