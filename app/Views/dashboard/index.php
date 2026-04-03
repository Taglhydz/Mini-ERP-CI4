<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Tableau de bord</h1>
        <p class="text-muted small">Vue d'ensemble du mini-ERP</p>
    </div>
</div>

<?php if (session('role') !== 'admin' && ! empty($low_stock_products)): ?>
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
                <span id="stock-alert-out-line">— <span id="stock-alert-out"><?= count($outOfStock) ?></span> produit(s) en rupture de stock.</span>
            <?php endif; ?>
            <?php if (! empty($lowStock)): ?>
                <span id="stock-alert-low-line">— <span id="stock-alert-low"><?= count($lowStock) ?></span> produit(s) à stock faible.</span>
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
                    <button type="button" class="btn btn-sm btn-link p-0 ms-2 small alert-link btn-restock-dash"
                            data-id="<?= (int)$p['id'] ?>"
                            data-stock="<?= (int)$p['stock'] ?>"
                            data-name="<?= esc($p['name'], 'attr') ?>">
                        Réapprovisionner <i class="bi bi-plus-circle"></i>
                    </button>
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
                    <div class="fs-2 fw-bold"><?= format_price($stats['ca_total']) ?></div>
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
                        <td><?= format_date($cmd['order_date']) ?></td>
                        <td><?= format_price($cmd['amount_ttc']) ?></td>
                        <td><?= badge_status($cmd['status']) ?></td>
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

<!-- ── Modal réapprovisionnement (dashboard) ──────────────────────────────── -->
<?php if (session('role') !== 'admin'): ?>
<div class="modal fade" id="modal-restock-dash" tabindex="-1" aria-labelledby="modal-restock-dash-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modal-restock-dash-label">
                    <i class="bi bi-plus-circle me-2 text-success"></i>Réapprovisionner
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold mb-1" id="mrd-name"></p>
                <p class="text-muted small mb-3">
                    Stock actuel&nbsp;: <span class="fw-bold text-dark" id="mrd-current"></span>
                </p>
                <label for="mrd-input" class="form-label fw-semibold">
                    Quantité à ajouter <span class="text-danger">*</span>
                </label>
                <input type="number" id="mrd-input" class="form-control" min="1" step="1" value="1" required>
                <p class="mt-2 mb-0 small text-muted">
                    Nouveau stock&nbsp;: <span class="fw-bold text-success" id="mrd-preview"></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success btn-sm" id="mrd-save">
                    <i class="bi bi-plus-circle me-1"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (session('role') !== 'admin'): ?>
<script>
$(function () {
    var currentId    = null;
    var currentStock = 0;
    var $inp         = $('#mrd-input');
    var $prev        = $('#mrd-preview');
    var modalEl      = document.getElementById('modal-restock-dash');
    var bsModal      = modalEl ? new bootstrap.Modal(modalEl) : null;

    $(document).on('click', '.btn-restock-dash', function () {
        var $btn     = $(this);
        currentId    = parseInt($btn.data('id'),    10);
        currentStock = parseInt($btn.data('stock'), 10);
        var name     = String($btn.data('name') || '');

        if (isNaN(currentId)    || currentId <= 0)   { return; }
        if (isNaN(currentStock) || currentStock < 0) { currentStock = 0; }

        $('#mrd-name').text(name);
        $('#mrd-current').text(currentStock);
        $inp.val(1);
        $prev.text(currentStock + 1).removeClass('text-danger').addClass('text-success');

        if (bsModal) { bsModal.show(); }
    });

    $inp.on('input', function () {
        var add = parseInt($(this).val(), 10);
        if (!isNaN(add) && add >= 1) {
            $prev.text(currentStock + add).removeClass('text-danger').addClass('text-success');
        } else {
            $prev.text('—').removeClass('text-success').addClass('text-danger');
        }
    });

    $('#mrd-save').on('click', function () {
        if (!currentId) { return; }
        var add = parseInt($inp.val(), 10);
        if (isNaN(add) || add < 1) {
            showToast('Veuillez saisir une quantité valide (≥ 1).', 'danger');
            return;
        }

        var csrfName  = $('meta[name="csrf-name"]').attr('content');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfName || !csrfToken) {
            showToast('Erreur CSRF, veuillez recharger la page.', 'danger');
            return;
        }

        var data       = {};
        data[csrfName] = csrfToken;
        data['qty_add'] = add;

        $.ajax({
            url:  '<?= base_url('products/') ?>' + currentId + '/restock',
            type: 'POST',
            data: data,
            success: function (res) {
                if (res.success) {
                    if (bsModal) { bsModal.hide(); }
                    showToast('Stock mis à jour !', 'success');
                    // Mise à jour du badge dans l'alerte sans rechargement
                    var $btn = $('.btn-restock-dash[data-id="' + currentId + '"]');
                    var newStock = res.stock;
                    $btn.data('stock', newStock);
                    var $li    = $btn.closest('li');
                    var $alert = $btn.closest('.alert');
                    var $badge = $btn.prevAll('.badge').first();

                    if (newStock === 0) {
                        $badge.removeClass('bg-warning text-dark').addClass('bg-danger').text('Rupture');
                    } else if (newStock <= 5) {
                        $badge.removeClass('bg-danger').addClass('bg-warning text-dark').text(newStock + ' restant(s)');
                    } else {
                        $li.remove();
                    }
                    var outCount = 0;
                    var lowCount = 0;
                    $alert.find('.btn-restock-dash').each(function () {
                        var s = parseInt($(this).data('stock'), 10);
                        if (s === 0) {
                            outCount += 1;
                        } else if (s <= 5) {
                            lowCount += 1;
                        }
                    });

                    if ($alert.find('li').length === 0 || (outCount === 0 && lowCount === 0)) {
                        $alert.remove();
                        return;
                    }

                    var $outLine = $('#stock-alert-out-line');
                    var $lowLine = $('#stock-alert-low-line');
                    if ($outLine.length) {
                        if (outCount > 0) {
                            $('#stock-alert-out').text(outCount);
                            $outLine.show();
                        } else {
                            $outLine.hide();
                        }
                    }
                    if ($lowLine.length) {
                        if (lowCount > 0) {
                            $('#stock-alert-low').text(lowCount);
                            $lowLine.show();
                        } else {
                            $lowLine.hide();
                        }
                    }
                } else {
                    showToast(res.message || 'Erreur lors de la mise à jour.', 'danger');
                }
            },
            error: function () {
                showToast('Erreur réseau.', 'danger');
            }
        });
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
