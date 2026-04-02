<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
$hasCover     = ! empty($company['cover_path']);
$hasLogo      = ! empty($company['logo_path']);
$showName     = isset($company['show_name']) ? (bool) $company['show_name'] : true;
$colorPrimary = (! empty($company['color_primary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_primary']))
    ? $company['color_primary'] : '#0d6efd';
$colorSecondary = (! empty($company['color_secondary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_secondary']))
    ? $company['color_secondary'] : '#ffffff';
?>

<style>
    :root {
        --company-primary:   <?= esc($colorPrimary) ?>;
        --company-secondary: <?= esc($colorSecondary) ?>;
    }
    .checkout-page .btn-primary,
    .checkout-page .btn-primary:focus {
        background-color: var(--company-primary);
        border-color: var(--company-primary);
    }
    .checkout-page .btn-primary:hover {
        background-color: color-mix(in srgb, var(--company-primary) 85%, #000);
        border-color: color-mix(in srgb, var(--company-primary) 85%, #000);
    }
    .checkout-page .text-primary { color: var(--company-primary) !important; }
    .checkout-recap-card {
        background: color-mix(in srgb, var(--company-primary) 8%, var(--bs-body-bg));
        border: 1px solid color-mix(in srgb, var(--company-primary) 30%, transparent) !important;
    }
    .checkout-recap-card .card-header {
        background: color-mix(in srgb, var(--company-primary) 15%, var(--bs-body-bg));
        border-bottom: 1px solid color-mix(in srgb, var(--company-primary) 25%, transparent);
    }
    .checkout-recap-card tfoot tr:last-child td { color: var(--company-primary); }
    /* Texte bannière sur fond color_primary */
    .checkout-page > .position-relative.overflow-hidden .text-white { color: var(--company-secondary) !important; }
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
                                    <td class="text-end"><?= number_format((float) $item['price'], 2, ',', ' ') ?> €</td>
                                    <td class="text-end pe-4 fw-semibold">
                                        <?= number_format((float) $item['qty'] * (float) $item['price'], 2, ',', ' ') ?> €
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 text-muted">Total HT</td>
                                    <td class="text-end pe-4 fw-semibold"><?= number_format($amountHt, 2, ',', ' ') ?> €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 text-muted">TVA (<?= (int) $vatRate ?>%)</td>
                                    <td class="text-end pe-4"><?= number_format($amountTtc - $amountHt, 2, ',', ' ') ?> €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end pe-3 fw-bold fs-5">Total TTC</td>
                                    <td class="text-end pe-4 fw-bold fs-5"><?= number_format($amountTtc, 2, ',', ' ') ?> €</td>
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
