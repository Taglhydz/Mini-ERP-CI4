<?php
/**
 * Panneau latéral panier (non-bloquant, sans offcanvas Bootstrap).
 * Rendu à l'intérieur du div#cart-panel dans catalog.php.
 * Variables : $company (array), $isLoggedIn (bool)
 */
$hasCover = ! empty($company['cover_path']);
$hasLogo  = ! empty($company['logo_path']);
$showName = isset($company['show_name']) ? (bool) $company['show_name'] : true;
?>

<style>
/* ── Panneau intérieur ────────────────────────────────────────────────────── */
.cart-panel-inner { width: 360px; }

/* ── En-tête avec cover / color_primary ──────────────────────────────────── */
.panel-header {
    position: relative;
    overflow: hidden;
    min-height: 72px;
    background-color: color-mix(in srgb, var(--company-primary) 85%, #000);
    <?php if ($hasCover): ?>
    background-image: url('<?= base_url(esc($company['cover_path'])) ?>');
    background-size: cover;
    background-position: center;
    <?php endif; ?>
}
.panel-header-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,<?= $hasCover ? '.58' : '.18' ?>);
}
.panel-header-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .85rem 1rem;
}

/* ── Articles ─────────────────────────────────────────────────────────────── */
.cart-panel-inner .cart-item {
    border-bottom: none !important;
    border: 1px solid color-mix(in srgb, var(--company-primary) 22%, transparent) !important;
    border-radius: .375rem;
    background: color-mix(in srgb, var(--company-primary) 9%, var(--bs-body-bg));
    padding: .6rem .75rem !important;
    margin-bottom: .5rem;
    transition: border-color .15s;
}
.cart-panel-inner .cart-item:last-child { margin-bottom: 0; }
.cart-panel-inner .cart-item:hover {
    border-color: color-mix(in srgb, var(--company-primary) 50%, transparent) !important;
}

/* ── Boutons +/- quantité ─────────────────────────────────────────────────── */
.cart-panel-inner .btn-qty-dec,
.cart-panel-inner .btn-qty-inc {
    border-color: color-mix(in srgb, var(--company-primary) 45%, transparent) !important;
    color: var(--company-primary) !important;
}
.cart-panel-inner .btn-qty-dec:hover,
.cart-panel-inner .btn-qty-inc:hover {
    background-color: var(--company-primary) !important;
    border-color: var(--company-primary) !important;
    color: #fff !important;
}

/* ── Sous-total article ───────────────────────────────────────────────────── */
.cart-panel-inner .cart-item .fw-semibold {
    color: var(--company-primary);
}

/* ── Bouton suppression ───────────────────────────────────────────────────── */
.cart-panel-inner .btn-cart-remove {
    opacity: .55;
    transition: opacity .15s;
}
.cart-panel-inner .btn-cart-remove:hover { opacity: 1; }

/* ── Scrollbar fine ───────────────────────────────────────────────────────── */
#cart-panel-items::-webkit-scrollbar { width: 4px; }
#cart-panel-items::-webkit-scrollbar-track { background: transparent; }
#cart-panel-items::-webkit-scrollbar-thumb {
    background: color-mix(in srgb, var(--company-primary) 55%, transparent);
    border-radius: 2px;
}
#cart-panel-items::-webkit-scrollbar-thumb:hover { background: var(--company-primary); }
/* Firefox */
#cart-panel-items { scrollbar-width: thin; scrollbar-color: var(--company-primary) transparent; }

/* ── Total ────────────────────────────────────────────────────────────────── */
.panel-total { color: var(--company-primary); }

/* ── Boutons footer ───────────────────────────────────────────────────────── */
.cart-panel-inner .btn-panel-secondary {
    background: transparent;
    border: 1px solid color-mix(in srgb, var(--company-primary) 50%, transparent);
    color: var(--company-primary);
    transition: background .15s, border-color .15s;
}
.cart-panel-inner .btn-panel-secondary:hover {
    background: color-mix(in srgb, var(--company-primary) 12%, transparent);
    border-color: var(--company-primary);
}
.cart-panel-inner .btn-panel-primary {
    background: var(--company-primary);
    border-color: var(--company-primary);
    color: #fff;
    transition: filter .15s;
}
.cart-panel-inner .btn-panel-primary:hover { filter: brightness(.88); }
</style>

<div class="cart-panel-inner d-flex flex-column h-100">

    <!-- ── En-tête ─────────────────────────────────────────────────────────── -->
    <div class="panel-header flex-shrink-0">
        <div class="panel-header-overlay"></div>
        <div class="panel-header-content">

            <?php if ($hasLogo): ?>
                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                     alt="<?= esc($company['name']) ?>"
                     style="max-height:36px;max-width:80px;object-fit:contain;
                            filter:drop-shadow(0 1px 6px rgba(0,0,0,.5));flex-shrink:0;">
            <?php else: ?>
                <div style="width:34px;height:34px;border-radius:.5rem;
                            background:rgba(255,255,255,.18);flex-shrink:0;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-shop text-white" style="font-size:1rem;"></i>
                </div>
            <?php endif; ?>

            <div class="flex-grow-1 overflow-hidden">
                <span class="fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-cart3"></i>
                    Mon panier
                    <span class="badge rounded-pill ms-1"
                          style="background:rgba(255,255,255,.25);font-size:.7rem;"
                          id="cart-panel-count">0</span>
                </span>
                <?php if ($showName): ?>
                    <p class="mb-0 text-truncate" style="font-size:.75rem;color:rgba(255,255,255,.72);">
                        <?= esc($company['name']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="button"
                    class="btn btn-sm flex-shrink-0"
                    id="cart-panel-close"
                    title="Réduire le panier"
                    style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
                           color:#fff;width:32px;height:32px;padding:0;border-radius:.375rem;">
                <i class="bi bi-chevron-double-right"></i>
            </button>
        </div>
    </div>

    <!-- ── Articles (remplis par AJAX) ─────────────────────────────────────── -->
    <div class="flex-grow-1 overflow-y-auto p-3" id="cart-panel-items">
        <div class="text-center text-muted py-5">
            <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Votre panier est vide.</p>
        </div>
    </div>

    <!-- ── Pied : total + boutons ───────────────────────────────────────────── -->
    <div class="p-3 flex-shrink-0" style="border-top:1px solid color-mix(in srgb,var(--company-primary) 20%,var(--bs-border-color));">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Total TTC (TVA&nbsp;20&nbsp;%)</span>
            <span class="fs-5 fw-bold panel-total" id="cart-panel-total">0,00&nbsp;€</span>
        </div>
        <a href="<?= base_url('shop/' . esc($company['slug']) . '/cart') ?>"
           class="btn btn-sm w-100 mb-2 btn-panel-secondary">
            <i class="bi bi-bag me-1"></i>Voir le panier complet
        </a>
        <button type="button"
                class="btn btn-sm w-100 btn-panel-primary"
                id="cart-panel-checkout-btn"
                data-slug="<?= esc($company['slug']) ?>"
                data-is-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">
            <i class="bi bi-credit-card me-1"></i>Passer la commande
        </button>
    </div>

</div>
