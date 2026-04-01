<?php
/**
 * Panneau latéral panier (non-bloquant, sans offcanvas Bootstrap).
 * Rendu à l'intérieur du div#cart-panel dans catalog.php.
 * Variables : $company (array), $isLoggedIn (bool)
 */
?>
<div class="cart-panel-inner d-flex flex-column h-100" style="width:360px;">

    <!-- En-tête du panneau -->
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-shrink-0">
        <span class="fw-bold fs-6">
            <i class="bi bi-cart3 me-2 text-primary"></i>Mon panier
            <span class="badge bg-primary rounded-pill ms-1" id="cart-panel-count">0</span>
        </span>
        <button type="button"
                class="btn btn-sm btn-outline-secondary"
                id="cart-panel-close"
                title="Réduire le panier">
            <i class="bi bi-chevron-double-right"></i>
        </button>
    </div>

    <!-- Liste des articles (remplie par AJAX) -->
    <div class="flex-grow-1 overflow-y-auto p-3" id="cart-panel-items">
        <div class="text-center text-muted py-5">
            <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Votre panier est vide.</p>
        </div>
    </div>

    <!-- Pied : total + boutons d'action -->
    <div class="p-3 border-top flex-shrink-0">
        <div class="d-flex justify-content-between align-items-center fw-semibold mb-3">
            <span class="text-muted small">Total TTC (TVA 20&nbsp;%)</span>
            <span class="fs-5 text-primary" id="cart-panel-total">0,00 €</span>
        </div>
        <a href="<?= base_url('shop/' . esc($company['slug']) . '/cart') ?>"
           class="btn btn-outline-primary btn-sm w-100 mb-2">
            <i class="bi bi-bag me-1"></i>Voir le panier complet
        </a>
        <button type="button"
                class="btn btn-primary btn-sm w-100"
                id="cart-panel-checkout-btn"
                data-slug="<?= esc($company['slug']) ?>"
                data-is-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">
            <i class="bi bi-credit-card me-1"></i>Passer la commande
        </button>
    </div>

</div>
