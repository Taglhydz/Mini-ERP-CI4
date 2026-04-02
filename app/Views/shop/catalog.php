<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="catalog-wrapper">

    <!-- ── Contenu principal (rétrécit quand le panier est ouvert) ─────────── -->
    <div class="catalog-content">

        <!-- Bannière boutique -->
        <div class="py-4" style="background:linear-gradient(135deg,#0d6efd 0%,#0a4fb4 100%);">
            <div class="container text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width:52px;height:52px;flex-shrink:0;">
                        <i class="bi bi-shop fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h3 fw-bold mb-0"><?= esc($company['name']) ?></h1>
                        <?php if (! empty($company['city'])): ?>
                            <p class="mb-0 opacity-75 small">
                                <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if (! $isLoggedIn): ?>
                    <div class="ms-auto d-none d-sm-block">
                        <a href="<?= base_url('shop/' . esc($company['slug']) . '/register') ?>"
                           class="btn btn-sm btn-light text-primary fw-semibold">
                            <i class="bi bi-person-plus me-1"></i>Créer un compte
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
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

/* ── @property pour animation de --header-height (Chrome 85+, FF 128+, Safari 16.4+) */
@property --header-height {
    syntax: '<length>';
    inherits: true;
    initial-value: 64px;
}

/* ── Panneau panier ──────────────────────────────────────────────────────────── */
.cart-panel {
    width: 0;
    overflow: hidden;
    flex-shrink: 0;
    position: sticky;
    top: var(--header-height);
    height: calc(100vh - var(--header-height));
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
    background: #0d6efd;
    color: #fff;
    padding: .55rem .6rem .55rem .75rem;
    border-radius: .5rem 0 0 .5rem;
    box-shadow: -2px 2px 8px rgba(0, 0, 0, .18);
    cursor: pointer;
    transition: background .15s;
}
.cart-tab:hover { background: #0b5ed7; }

/* ── Cards produit ───────────────────────────────────────────────────────────── */
.card { transition: transform .18s, box-shadow .18s; }
.card:hover { transform: translateY(-3px); box-shadow: 0 .4rem 1.2rem rgba(0,0,0,.1) !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>window.BASE_URL = '<?= base_url('/') ?>';</script>
<script src="<?= base_url('assets/js/cart.js') ?>?v=2.0"></script>
<?= $this->endSection() ?>
