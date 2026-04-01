<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Bannière -->
<div class="py-3" style="background:linear-gradient(135deg,#198754 0%,#136c43 100%);">
    <div class="container text-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;flex-shrink:0;">
                <i class="bi bi-bag-check fs-4"></i>
            </div>
            <div>
                <h1 class="h4 fw-bold mb-0">Finaliser la commande</h1>
                <p class="mb-0 opacity-75 small"><?= esc($company['name']) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">

            <!-- Récapitulatif des produits -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-list-check me-2 text-primary"></i>Récapitulatif de la commande
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Produit</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">Prix unit.</th>
                                <th class="text-end pe-4">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $item): ?>
                            <tr>
                                <td class="ps-4 fw-semibold"><?= esc($item['name']) ?></td>
                                <td class="text-center"><?= (int) $item['qty'] ?></td>
                                <td class="text-end">
                                    <?= number_format((float) $item['price'], 2, ',', ' ') ?> €
                                </td>
                                <td class="text-end pe-4 fw-semibold">
                                    <?= number_format((float) $item['qty'] * (float) $item['price'], 2, ',', ' ') ?> €
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end pe-3 text-muted">Total HT</td>
                                <td class="text-end pe-4 fw-semibold">
                                    <?= number_format($amountHt, 2, ',', ' ') ?> €
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end pe-3 text-muted">
                                    TVA (<?= (int) $vatRate ?>%)
                                </td>
                                <td class="text-end pe-4">
                                    <?= number_format($amountTtc - $amountHt, 2, ',', ' ') ?> €
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end pe-3 fw-bold fs-5">Total TTC</td>
                                <td class="text-end pe-4 fw-bold text-success fs-5">
                                    <?= number_format($amountTtc, 2, ',', ' ') ?> €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Confirmation -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="post"
                          action="<?= base_url('shop/' . esc($company['slug']) . '/checkout/confirm') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check-circle me-2"></i>Confirmer la commande
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?= base_url('shop/' . esc($company['slug']) . '/cart') ?>"
                           class="text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Retour au panier
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
