<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
['hasCover' => $hasCover, 'hasLogo' => $hasLogo, 'showName' => $showName,
 'colorPrimary' => $colorPrimary, 'colorSecondary' => $colorSecondary] = company_theme($company);
?>

<style>
    :root {
        --company-primary:   <?= esc($colorPrimary) ?>;
        --company-secondary: <?= esc($colorSecondary) ?>;
    }
</style>

<div class="checkout-page">

    <!-- ── Bannière boutique ─────────────────────────────────────────────── -->
    <div class="position-relative overflow-hidden"
         style="min-height:160px;<?= $hasCover
            ? 'background:url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat;'
            : 'background:linear-gradient(135deg,var(--company-primary) 0%,color-mix(in srgb,var(--company-primary) 75%,#000) 100%);' ?>">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,<?= $hasCover ? '.50' : '.12' ?>);"></div>
        <div class="position-relative d-flex align-items-center gap-4 px-4 py-4" style="min-height:160px;">
            <?php if ($hasLogo): ?>
                <img src="<?= base_url(esc($company['logo_path'])) ?>" alt="<?= esc($company['name']) ?>"
                     style="max-width:90px;max-height:60px;object-fit:contain;
                            filter:drop-shadow(0 2px 8px rgba(0,0,0,.5));flex-shrink:0;">
            <?php else: ?>
                <div style="width:52px;height:52px;border-radius:.75rem;background:rgba(255,255,255,.18);
                            flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-bag-check fs-3 text-white"></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="text-white fw-bold mb-0 h4">
                    <i class="bi bi-bag-check me-2"></i>Finaliser la commande
                </h1>
                <?php if ($showName): ?>
                    <p class="mb-0 small" style="color:rgba(255,255,255,.78);"><?= esc($company['name']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">

                <?php $stockErrors = session()->getFlashdata('stock_errors'); if (! empty($stockErrors)): ?>
                <div class="alert alert-danger mb-4" role="alert">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Stock insuffisant — commande non validée
                    </h6>
                    <ul class="mb-0">
                        <?php foreach ($stockErrors as $err): ?>
                            <li><?= $err ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Récapitulatif des articles -->
                <div class="card checkout-recap-card border-0 rounded-3 mb-4">
                    <div class="card-header fw-semibold">
                        <i class="bi bi-list-check me-2 text-primary"></i>Récapitulatif de la commande
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
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
                                    <td class="text-end"><?= format_price($item['price']) ?></td>
                                    <td class="text-end pe-4 fw-semibold">
                                        <?= format_price((float) $item['qty'] * (float) $item['price']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 text-muted">Total HT</td>
                                    <td class="text-end pe-4 fw-semibold"><?= format_price($amountHt) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 text-muted">TVA (<?= (int) $vatRate ?>%)</td>
                                    <td class="text-end pe-4"><?= format_price($amountTtc - $amountHt) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 fw-bold fs-5">Total TTC</td>
                                    <td class="text-end pe-4 fw-bold fs-5"><?= format_price($amountTtc) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Bouton confirmation -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form method="post"
                              action="<?= base_url('shop/' . esc($company['slug']) . '/checkout/confirm') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
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

</div><!-- /.checkout-page -->

<?= $this->endSection() ?>
