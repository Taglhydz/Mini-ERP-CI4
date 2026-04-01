<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Bannière boutique -->
<div class="py-3" style="background:linear-gradient(135deg,#0d6efd 0%,#0a4fb4 100%);">
    <div class="container text-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;flex-shrink:0;">
                <i class="bi bi-cart3 fs-4"></i>
            </div>
            <div>
                <h1 class="h4 fw-bold mb-0">Mon panier</h1>
                <p class="mb-0 opacity-75 small"><?= esc($company['name']) ?></p>
            </div>
            <div class="ms-auto d-none d-sm-block">
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                   class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i>Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">

    <?php $flash = session()->getFlashdata('success'); if ($flash): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= esc($flash) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
            <p class="fs-5">Votre panier est vide.</p>
            <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
               class="btn btn-primary">
                <i class="bi bi-shop me-1"></i>Voir le catalogue
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Produits -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix unit.</th>
                                    <th class="text-end">Sous-total</th>
                                    <th class="text-end pe-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $productId => $item): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?= esc($item['name']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <?= (int) $item['qty'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?= number_format((float) $item['price'], 2, ',', ' ') ?> €
                                    </td>
                                    <td class="text-end fw-semibold">
                                        <?= number_format((float) $item['qty'] * (float) $item['price'], 2, ',', ' ') ?> €
                                    </td>
                                    <td class="text-end pe-4">
                                        <form method="post"
                                              action="<?= base_url('shop/' . esc($company['slug']) . '/cart/remove') ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent fw-semibold">
                        <i class="bi bi-receipt me-2 text-primary"></i>Récapitulatif
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total HT</span>
                            <span class="fw-semibold">
                                <?= number_format($amountHt, 2, ',', ' ') ?> €
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">TVA (<?= (int) $vatRate ?>%)</span>
                            <span>
                                <?= number_format($amountTtc - $amountHt, 2, ',', ' ') ?> €
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total TTC</span>
                            <span class="fw-bold text-primary fs-5">
                                <?= number_format($amountTtc, 2, ',', ' ') ?> €
                            </span>
                        </div>
                        <a href="<?= base_url('shop/' . esc($company['slug']) . '/checkout') ?>"
                           class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-bag-check me-2"></i>Passer la commande
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card { transition: box-shadow .18s; }
</style>
<?= $this->endSection() ?>
