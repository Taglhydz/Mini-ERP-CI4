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

<div class="order-confirm-page">

    <!-- ── Bannière boutique (cohérence avec catalogue / panier) ─────────── -->
    <div class="position-relative overflow-hidden"
         style="min-height:180px;<?= $hasCover
            ? 'background:url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat;'
            : 'background:linear-gradient(135deg,var(--company-primary) 0%,color-mix(in srgb,var(--company-primary) 75%,#000) 100%);' ?>">

        <div style="position:absolute;inset:0;background:rgba(0,0,0,<?= $hasCover ? '.52' : '.15' ?>);"></div>

        <div class="position-relative d-flex align-items-center gap-4 px-4 py-4" style="min-height:180px;">
            <?php if ($hasLogo): ?>
                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                     alt="<?= esc($company['name']) ?>"
                     style="max-width:100px;max-height:65px;object-fit:contain;
                            filter:drop-shadow(0 2px 10px rgba(0,0,0,.55));flex-shrink:0;">
            <?php else: ?>
                <div style="width:60px;height:60px;border-radius:.75rem;background:rgba(255,255,255,.18);
                            flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-receipt fs-3 text-white"></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="text-white fw-bold mb-1 h4">Récapitulatif de commande</h1>
                <?php if ($showName): ?>
                    <p class="mb-0 small" style="color:rgba(255,255,255,.78);"><?= esc($company['name']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <div class="col-xl-9">

                <!-- ── Bandeau confirmation "Commande validée" ─────────────── -->
                <div class="confirm-banner d-flex align-items-center gap-4 p-4 mb-4">
                    <div class="confirm-icon">
                        <i class="bi bi-check-lg text-white fs-4"></i>
                    </div>
                    <div>
                        <p class="fw-bold fs-5 mb-1" style="color:#065f46;">Commande validée !</p>
                        <p class="mb-0 small" style="color:#047857;">
                            Votre commande <strong><?= esc($order['number']) ?></strong>
                            a été enregistrée le <?= format_date($order['order_date']) ?>.
                        </p>
                    </div>
                </div>

                <!-- ── Infos clés de la commande ──────────────────────────── -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card order-card border-0 h-100">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-hash fs-4 text-primary mb-1 d-block"></i>
                                <p class="text-muted small mb-1">Numéro</p>
                                <p class="fw-bold mb-0"><?= esc($order['number']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card order-card border-0 h-100">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-calendar-check fs-4 text-primary mb-1 d-block"></i>
                                <p class="text-muted small mb-1">Date</p>
                                <p class="fw-bold mb-0"><?= format_date($order['order_date']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card order-card border-0 h-100">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-patch-check fs-4 text-primary mb-1 d-block"></i>
                                <p class="text-muted small mb-1">Statut</p>
                                <p class="fw-bold mb-0">
                                    <span class="badge" style="background:var(--company-primary);">
                                        <?= esc($order['status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Articles commandés (DataTable) ─────────────────────── -->
                <div class="card order-card border-0 mb-4">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2 text-primary"></i>Articles commandés
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="table-confirm-items" class="table table-hover mb-0 align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>Désignation</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-end">Prix unit. HT</th>
                                        <th class="text-end">Sous-total HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($item['name']) ?></td>
                                        <td class="text-center"><?= (int) $item['quantity'] ?></td>
                                        <td class="text-end"><?= format_price($item['unit_price']) ?></td>
                                        <td class="text-end fw-semibold"><?= format_price($item['subtotal']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">Total HT</td>
                                        <td class="text-end fw-semibold"><?= format_price($order['amount_ht']) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">TVA (<?= (int) $order['vat_rate'] ?>%)</td>
                                        <td class="text-end"><?= format_price((float) $order['amount_ttc'] - (float) $order['amount_ht']) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ── Résumé financier ──────────────────────────────────── -->
                <div class="row g-4">
                    <div class="col-md-6 offset-md-6">
                        <div class="card summary-card border-0">
                            <div class="card-header">
                                <i class="bi bi-calculator me-2 text-primary"></i>Résumé financier
                            </div>
                            <div class="card-body px-4 py-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total HT</span>
                                    <span class="fw-semibold"><?= format_price($order['amount_ht']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">TVA (<?= (int) $order['vat_rate'] ?>%)</span>
                                    <span><?= format_price((float) $order['amount_ttc'] - (float) $order['amount_ht']) ?></span>
                                </div>
                                <hr class="summary-divider my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5">Total TTC</span>
                                    <span class="summary-ttc"><?= format_price($order['amount_ttc']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Liens de navigation ───────────────────────────────── -->
                <div class="d-flex gap-3 mt-4 flex-wrap">
                    <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                       class="btn btn-primary">
                        <i class="bi bi-shop me-1"></i>Retour au catalogue
                    </a>
                    <a href="<?= base_url('espace-client') ?>"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-person me-1"></i>Mes commandes
                    </a>
                </div>

            </div>
        </div>
    </div>

</div><!-- /.order-confirm-page -->

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>?v=2.3.7">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/dataTables.min.js') ?>?v=2.3.7"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>?v=2.3.7"></script>
<script>
(function () {
    var dt = new DataTable('#table-confirm-items', {
        paging:   false,
        ordering: false,
        info:     false,
        searching: false,
        language: { url: '<?= base_url("assets/js/i18n/fr-FR.json") ?>' },
    });
})();
</script>
<?= $this->endSection() ?>
