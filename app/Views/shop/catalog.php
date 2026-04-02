<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
// ── Variables thème entreprise ─────────────────────────────────────────────
$hasCover     = ! empty($company['cover_path']);
$hasLogo      = ! empty($company['logo_path']);
$showName     = isset($company['show_name']) ? (bool) $company['show_name'] : true;
$colorPrimary = (! empty($company['color_primary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_primary']))
    ? $company['color_primary']
    : '#0d6efd';
?>

<style>
    :root {
        --company-primary: <?= esc($colorPrimary) ?>;
        --company-cover: <?= $hasCover ? "url('" . base_url(esc($company['cover_path'])) . "')" : 'none' ?>;
    }
    .catalog-content .btn-primary,
    .catalog-content .btn-primary:focus {
        background-color: var(--company-primary);
        border-color: var(--company-primary);
    }
    .catalog-content .btn-primary:hover {
        background-color: color-mix(in srgb, var(--company-primary) 85%, #000);
        border-color: color-mix(in srgb, var(--company-primary) 85%, #000);
    }
    .catalog-content .text-primary     { color: var(--company-primary) !important; }
    .catalog-content .badge.bg-primary  { background-color: var(--company-primary) !important; }

    /* ── Cards produits : fond atténué avec color_primary ─────────────────── */
    .catalog-content .card {
        background-color: color-mix(in srgb, var(--company-primary) 10%, var(--bs-body-bg));
        border: 1px solid color-mix(in srgb, var(--company-primary) 30%, transparent) !important;
        transition: transform .18s, box-shadow .18s, border-color .18s;
    }
    .catalog-content .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1.5rem color-mix(in srgb, var(--company-primary) 25%, transparent) !important;
        border-color: color-mix(in srgb, var(--company-primary) 60%, transparent) !important;
    }
</style>

<div class="catalog-wrapper">

    <!-- ── Contenu principal (rétrécit quand le panier est ouvert) ─────────── -->
    <div class="catalog-content">

        <!-- ── Bannière boutique (cover + logo overlay) ────────────────────── -->
        <div class="catalog-hero position-relative overflow-hidden"
             style="min-height:220px;<?= $hasCover
                ? 'background:url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat;'
                : 'background:linear-gradient(135deg,var(--company-primary) 0%,color-mix(in srgb,var(--company-primary) 75%,#000) 100%);' ?>">

            <!-- Overlay obscur -->
            <div style="position:absolute;inset:0;background:rgba(0,0,0,<?= $hasCover ? '.50' : '.12' ?>);backdrop-filter:<?= $hasCover ? 'blur(1px)' : 'none' ?>;"></div>

            <!-- Contenu centré -->
            <div class="position-relative d-flex flex-column align-items-center justify-content-center text-center py-5 px-3"
                 style="min-height:220px;">

                <?php if ($hasLogo): ?>
                    <img src="<?= base_url(esc($company['logo_path'])) ?>"
                         alt="<?= esc($company['name']) ?>"
                         class="mb-3"
                         style="max-width:140px;max-height:90px;object-fit:contain;
                                filter:drop-shadow(0 2px 12px rgba(0,0,0,.55));">
                <?php else: ?>
                    <div class="mb-3 d-flex align-items-center justify-content-center"
                         style="width:72px;height:72px;border-radius:1rem;
                                background:rgba(255,255,255,.18);backdrop-filter:blur(4px);">
                        <i class="bi bi-shop fs-2 text-white"></i>
                    </div>
                <?php endif; ?>

                <?php if ($showName): ?>
                    <h1 class="text-white fw-bold mb-1"
                        style="font-size:clamp(1.4rem,4vw,2.2rem);text-shadow:0 2px 8px rgba(0,0,0,.5);">
                        <?= esc($company['name']) ?>
                    </h1>
                <?php endif; ?>

                <?php if (! empty($company['city'])): ?>
                    <p class="mb-0 small" style="color:rgba(255,255,255,.8);">
                        <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                    </p>
                <?php endif; ?>

                <?php if (! $isLoggedIn): ?>
                    <div class="mt-3 d-flex gap-2 flex-wrap justify-content-center">
                        <a href="<?= base_url('shop/' . esc($company['slug']) . '/register') ?>"
                           class="btn btn-sm btn-light fw-semibold"
                           style="color:var(--company-primary);">
                            <i class="bi bi-person-plus me-1"></i>Créer un compte
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Catalogue produits -->
        <div class="container py-5">
            <h2 class="h4 fw-bold mb-4">
                <i class="bi bi-grid me-2 text-primary"></i>Catalogue
            </h2>

            <?php if (empty($products)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-box fs-1 d-block mb-3 opacity-50"></i>
                    <p class="fs-5">Aucun produit disponible pour l'instant.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary small mb-2">
                                    <?= esc($product['reference']) ?>
                                </span>
                                <h5 class="card-title fw-semibold"><?= esc($product['name']) ?></h5>
                                <?php if (! empty($product['description'])): ?>
                                    <p class="card-text text-muted small"><?= esc($product['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-primary fs-5">
                                        <?= number_format((float)$product['unit_price'], 2, ',', ' ') ?> €
                                    </span>
                                    <span class="badge bg-success bg-opacity-10 text-success small">
                                        <i class="bi bi-check-circle me-1"></i>En stock
                                    </span>
                                </div>
                                <form method="post"
                                      action="<?= base_url('shop/' . esc($company['slug']) . '/cart/add') ?>"
                                      class="cart-add-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-cart-plus me-1"></i>Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- /.catalog-content -->

    <!-- ── Panneau panier latéral (non-bloquant, flex sibling) ─────────────── -->
    <div class="cart-panel<?= $cartCount > 0 ? ' open' : '' ?>"
         id="cart-panel"
         data-slug="<?= esc($company['slug']) ?>">
        <?= view('shop/_cart_panel', ['company' => $company, 'isLoggedIn' => $isLoggedIn]) ?>
    </div>

</div><!-- /.catalog-wrapper -->

<!-- ── Languette d'accès (visible quand le panneau est fermé) ──────────────── -->
<button type="button"
        class="cart-tab<?= $cartCount > 0 ? ' d-none' : '' ?>"
        id="cart-tab"
        title="Ouvrir le panier"
        aria-label="Ouvrir le panier">
    <i class="bi bi-cart3"></i>
    <span class="badge bg-danger rounded-pill ms-1" id="cart-tab-count"><?= (int) $cartCount ?></span>
</button>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* ── Layout catalogue + panneau ─────────────────────────────────────────────── */
.catalog-wrapper {
    display: flex;
    align-items: flex-start;
}
.catalog-content {
    flex: 1;
    min-width: 0;
}

/* ── @property pour animation des variables de hauteur ─────────────────────── */
@property --header-height    { syntax: '<length>'; inherits: true; initial-value: 64px; }
@property --footer-visible-h { syntax: '<length>'; inherits: true; initial-value: 0px; }

/* ── Panneau panier ──────────────────────────────────────────────────────────── */
.cart-panel {
    width: 0;
    overflow: hidden;
    flex-shrink: 0;
    position: sticky;
    top: var(--header-height);
    height: calc(100vh - var(--header-height) - var(--footer-visible-h));
    transition: width .3s ease, top .3s ease, height .3s ease;
    border-left: 0 solid var(--bs-border-color);
    background: var(--bs-body-bg);
}
.cart-panel.open {
    width: 360px;
    border-left-width: 1px;
    box-shadow: -4px 0 16px rgba(0, 0, 0, .07);
}

/* ── Languette d'accès (tab fixe) ────────────────────────────────────────────── */
.cart-tab {
    position: fixed;
    height: 10rem;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 200;
    border: none;
    background: var(--company-primary, #0d6efd);
    color: #fff;
    padding: .55rem .6rem .55rem .75rem;
    border-radius: .5rem 0 0 .5rem;
    box-shadow: -2px 2px 8px rgba(0, 0, 0, .18);
    cursor: pointer;
    transition: background .15s;
}
.cart-tab:hover { filter: brightness(.88); }

/* ── Cards produit ───────────────────────────────────────────────────────────── */
/* Les transitions sont gérées dans le bloc <style> injecté en tête de page */
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>window.BASE_URL = '<?= base_url('/') ?>';</script>
<script src="<?= base_url('assets/js/cart.js') ?>?v=2.0"></script>
<?= $this->endSection() ?>
