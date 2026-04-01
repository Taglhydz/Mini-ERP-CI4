<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

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
                <a href="<?= base_url('login') ?>" class="btn btn-sm btn-outline-light ms-1">
                    <i class="bi bi-door-open me-1"></i>Connexion
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Catalogue -->
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
                              action="<?= base_url('shop/' . esc($company['slug']) . '/cart/add') ?>">
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

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { transition: transform .18s, box-shadow .18s; }
.card:hover { transform: translateY(-3px); box-shadow: 0 .4rem 1.2rem rgba(0,0,0,.1) !important; }
</style>
<?= $this->endSection() ?>
