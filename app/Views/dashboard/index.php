<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Tableau de bord</h1>
        <p class="text-muted small">Vue d'ensemble du mini-ERP</p>
    </div>
</div>

<?php if (! empty($low_stock_products)): ?>
<?php
    $outOfStock  = array_filter($low_stock_products, fn($p) => (int)$p['stock'] === 0);
    $lowStock    = array_filter($low_stock_products, fn($p) => (int)$p['stock'] > 0);
    $alertType   = ! empty($outOfStock) ? 'danger' : 'warning';
    $alertIcon   = ! empty($outOfStock) ? 'x-circle-fill' : 'exclamation-triangle-fill';
?>
<div class="alert alert-<?= $alertType ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-<?= $alertIcon ?> flex-shrink-0 mt-1"></i>
        <div class="flex-grow-1">
            <strong>Alerte stock</strong>
            <?php if (! empty($outOfStock)): ?>
                — <?= count($outOfStock) ?> produit(s) en rupture de stock.
            <?php endif; ?>
            <?php if (! empty($lowStock)): ?>
                — <?= count($lowStock) ?> produit(s) à stock faible.
            <?php endif; ?>
            <ul class="mb-0 mt-2 small">
                <?php foreach ($low_stock_products as $p): ?>
                <li class="mb-1">
                    <strong><?= esc($p['name']) ?></strong>
                    <?php if ((int)$p['stock'] === 0): ?>
                        <span class="badge bg-danger ms-1">Rupture</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark ms-1"><?= (int)$p['stock'] ?> restant(s)</span>
                    <?php endif; ?>
                    <a href="<?= base_url('products/' . $p['id'] . '/edit') ?>" class="ms-2 small alert-link">
                        Réapprovisionner <i class="bi bi-arrow-right"></i>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
</div>
<?php endif; ?>

<!-- Cartes statistiques -->
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-people fs-3 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= esc($stats['clients']) ?></div>
                    <div class="text-muted small">Clients</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= base_url('clients') ?>" class="btn btn-sm btn-outline-primary w-100">
                    Gérer <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <i class="bi bi-box fs-3 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= esc($stats['products']) ?></div>
                    <div class="text-muted small">Produits</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= base_url('products') ?>" class="btn btn-sm btn-outline-success w-100">
                    Gérer <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-cart fs-3 text-warning"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= esc($stats['orders']) ?></div>
                    <div class="text-muted small">Commandes</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= base_url('orders') ?>" class="btn btn-sm btn-outline-warning w-100">
                    Gérer <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3">
                    <i class="bi bi-currency-euro fs-3 text-info"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= number_format($stats['ca_total'], 2, ',', ' ') ?> €</div>
                    <div class="text-muted small">CA total (HT)</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= base_url('orders') ?>" class="btn btn-sm btn-outline-info w-100">
                    Détails <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dernières commandes -->
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Dernières commandes</h5>
        <a href="<?= base_url('orders/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle commande
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant TTC</th>
                        <th>Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($latest_orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Aucune commande pour l'instant.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($latest_orders as $cmd): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($cmd['number']) ?></td>
                        <td><?= esc($cmd['user_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($cmd['order_date'])) ?></td>
                        <td><?= number_format($cmd['amount_ttc'], 2, ',', ' ') ?> €</td>
                        <td><?= view('partials/badge_status', ['status' => $cmd['status']]) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('orders/' . $cmd['id']) ?>"
                               class="btn btn-sm btn-outline-secondary" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
