<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-receipt me-2 text-warning"></i><?= esc($order['number']) ?></h1>
        <p class="text-muted small mb-0">
            <?= badge_status($order['status']) ?>
            &mdash; <?= format_date($order['order_date']) ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('orders/' . $order['id'] . '/pdf') ?>"
           class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Télécharger PDF
        </a>
        <a href="<?= base_url('orders/' . $order['id'] . '/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <a href="<?= base_url('orders') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Infos client -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom fw-semibold">
                <i class="bi bi-person me-2 text-primary"></i>Client
            </div>
            <div class="card-body">
                <div class="fw-bold fs-5"><?= esc($order['user_name']) ?></div>
                <div class="text-muted small"><?= esc($order['user_email']) ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom fw-semibold">
                <i class="bi bi-info-circle me-2 text-primary"></i>Détails
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Numéro</dt>
                    <dd class="col-sm-8"><?= esc($order['number']) ?></dd>
                    <dt class="col-sm-4 text-muted">Date</dt>
                    <dd class="col-sm-8"><?= format_date($order['order_date']) ?></dd>
                    <dt class="col-sm-4 text-muted">Statut</dt>
                    <dd class="col-sm-8"><?= badge_status($order['status']) ?></dd>
                    <dt class="col-sm-4 text-muted">TVA</dt>
                    <dd class="col-sm-8"><?= esc($order['vat_rate']) ?> %</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Lignes -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom fw-semibold">
                <i class="bi bi-list-ul me-2 text-primary"></i>Lignes de commande
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Désignation</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">Prix HT</th>
                                <th class="text-end">Sous-total HT</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= esc($item['name']) ?></td>
                                <td class="text-center"><?= esc($item['quantity']) ?></td>
                                <td class="text-end"><?= format_price($item['unit_price']) ?></td>
                                <td class="text-end"><?= format_price($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">Total HT</td>
                                <td class="text-end"><?= format_price($order['amount_ht']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">TVA (<?= esc($order['vat_rate']) ?>%)</td>
                                <td class="text-end">
                                    <?= format_price((float)$order['amount_ttc'] - (float)$order['amount_ht']) ?>
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="3" class="text-end fs-5">Total TTC</td>
                                <td class="text-end fs-5"><?= format_price($order['amount_ttc']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if (! empty($order['notes'])): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-warning border-3">
            <div class="card-body">
                <strong><i class="bi bi-sticky me-1 text-warning"></i>Notes :</strong>
                <p class="mb-0 mt-1"><?= nl2br(esc($order['notes'])) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
