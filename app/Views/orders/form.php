<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-cart-plus me-2 text-warning"></i>
            <?= isset($order) ? 'Modifier la commande' : 'Nouvelle commande' ?>
        </h1>
    </div>
    <a href="<?= base_url('orders') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php $stockErrors = session()->getFlashdata('stock_errors'); if (! empty($stockErrors)): ?>
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <h6 class="alert-heading fw-bold">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Stock insuffisant — passage en « Livrée » impossible
    </h6>
    <p class="mb-2 small">Réapprovisionnez les articles concernés avant de marquer cette commande comme livrée&nbsp;:</p>
    <ul class="mb-0 small">
        <?php foreach ($stockErrors as $msg): ?>
            <li><?= $msg ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
</div>
<?php endif; ?>

<form action="<?= isset($order) ? base_url('orders/' . $order['id'] . '/update') : base_url('orders/store') ?>"
      method="post" id="form-order">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Informations générales -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom fw-semibold">Informations</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($users as $u): ?>
                                <?php $displayName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: $u['username']; ?>
                                <option value="<?= esc($u['id']) ?>"
                                    <?= (old('user_id', $order['user_id'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                                    <?= esc($displayName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control"
                               value="<?= esc(old('order_date', $order['order_date'] ?? date('Y-m-d'))) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach (['draft' => 'Brouillon', 'confirmed' => 'Confirmée',
                                             'delivered' => 'Livrée', 'cancelled' => 'Annulée'] as $val => $label): ?>
                                <option value="<?= $val ?>"
                                    <?= (old('status', $order['status'] ?? 'draft') === $val) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">TVA (%)</label>
                        <input type="number" name="vat_rate" step="0.01" min="0" max="100"
                               class="form-control" id="input-vat"
                               value="<?= esc(old('vat_rate', $order['vat_rate'] ?? '20')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"><?= esc(old('notes', $order['notes'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lignes de commande -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom fw-semibold d-flex justify-content-between">
                    <span>Lignes de commande</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter une ligne
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="table-items">
                            <thead>
                                <tr>
                                    <th style="min-width:160px;">Produit</th>
                                    <th style="min-width:180px;">Désignation</th>
                                    <th style="width:80px;">Qté</th>
                                    <th style="width:110px;">Prix HT €</th>
                                    <th style="width:110px;" class="text-end">Sous-total</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                <?php
                                $existingItems = $items ?? [];
                                if (empty($existingItems)): ?>
                                    <tr class="item-row">
                                        <td>
                                            <select name="product_id[]" class="form-select form-select-sm select-product">
                                                <option value="">— Choisir —</option>
                                                <?php foreach ($products as $p): ?>
                                                    <option value="<?= $p['id'] ?>"
                                                            data-price="<?= $p['unit_price'] ?>"
                                                            data-name="<?= esc($p['name']) ?>">
                                                        <?= esc($p['reference']) ?> — <?= esc($p['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="name[]" class="form-control form-control-sm input-name"></td>
                                        <td><input type="number" name="quantity[]" value="1" min="1" class="form-control form-control-sm input-qty"></td>
                                        <td><input type="number" name="unit_price[]" step="0.01" min="0" value="0.00" class="form-control form-control-sm input-price"></td>
                                        <td class="text-end fw-semibold span-subtotal">0,00 €</td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-item"><i class="bi bi-x"></i></button></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($existingItems as $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <select name="product_id[]" class="form-select form-select-sm select-product">
                                                <option value="">— Choisir —</option>
                                                <?php foreach ($products as $p): ?>
                                                    <option value="<?= $p['id'] ?>"
                                                            data-price="<?= $p['unit_price'] ?>"
                                                            data-name="<?= esc($p['name']) ?>"
                                                            <?= ($p['id'] == $item['product_id']) ? 'selected' : '' ?>>
                                                        <?= esc($p['reference']) ?> — <?= esc($p['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="name[]" class="form-control form-control-sm input-name" value="<?= esc($item['name']) ?>"></td>
                                        <td><input type="number" name="quantity[]" value="<?= esc($item['quantity']) ?>" min="1" class="form-control form-control-sm input-qty"></td>
                                        <td><input type="number" name="unit_price[]" step="0.01" min="0" value="<?= esc($item['unit_price']) ?>" class="form-control form-control-sm input-price"></td>
                                        <td class="text-end fw-semibold span-subtotal"><?= format_price($item['subtotal']) ?></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-item"><i class="bi bi-x"></i></button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">Total HT</td>
                                    <td class="text-end" id="total-ht">0,00 €</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">TVA</td>
                                    <td class="text-end" id="total-vat">0,00 €</td>
                                    <td></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="4" class="text-end fs-5">Total TTC</td>
                                    <td class="text-end fs-5 fw-bold" id="total-ttc">0,00 €</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-warning text-white">
                    <i class="bi bi-save me-1"></i><?= isset($order) ? 'Enregistrer' : 'Créer la commande' ?>
                </button>
                <a href="<?= base_url('orders') ?>" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>
    </div>
</form>

<!-- Template ligne (hidden) -->
<template id="item-template">
    <tr class="item-row">
        <td>
            <select name="product_id[]" class="form-select form-select-sm select-product">
                <option value="">— Choisir —</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"
                            data-price="<?= $p['unit_price'] ?>"
                            data-name="<?= esc($p['name']) ?>">
                        <?= esc($p['reference']) ?> — <?= esc($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="name[]" class="form-control form-control-sm input-name"></td>
        <td><input type="number" name="quantity[]" value="1" min="1" class="form-control form-control-sm input-qty"></td>
        <td><input type="number" name="unit_price[]" step="0.01" min="0" value="0.00" class="form-control form-control-sm input-price"></td>
        <td class="text-end fw-semibold span-subtotal">0,00 €</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-item"><i class="bi bi-x"></i></button></td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    function fmt(n) {
        return n.toFixed(2).replace('.', ',') + ' \u20ac';
    }

    function recalcRow($row) {
        const qty   = parseFloat($row.find('.input-qty').val())   || 0;
        const price = parseFloat($row.find('.input-price').val()) || 0;
        const total = qty * price;
        $row.find('.span-subtotal').text(fmt(total));
        return total;
    }

    function recalcAll() {
        let ht = 0;
        $('#items-body .item-row').each(function () {
            ht += recalcRow($(this));
        });
        const vat    = parseFloat($('#input-vat').val()) || 0;
        const vatAmt = ht * vat / 100;
        $('#total-ht').text(fmt(ht));
        $('#total-vat').text(fmt(vatAmt));
        $('#total-ttc').text(fmt(ht + vatAmt));
    }

    // Auto-remplissage depuis le select produit
    $(document).on('change', '.select-product', function () {
        const $opt = $(this).find('option:selected');
        const $row = $(this).closest('tr');
        $row.find('.input-name').val($opt.data('name') || '');
        $row.find('.input-price').val(parseFloat($opt.data('price') || 0).toFixed(2));
        recalcAll();
    });

    // Recalcul à chaque saisie
    $(document).on('input', '.input-qty, .input-price', recalcAll);
    $('#input-vat').on('input', recalcAll);

    // Ajouter ligne
    $('#btn-add-item').on('click', function () {
        const tpl = document.getElementById('item-template').content.cloneNode(true);
        $('#items-body').append(tpl);
        recalcAll();
    });

    // Supprimer ligne
    $(document).on('click', '.btn-del-item', function () {
        if ($('#items-body .item-row').length > 1) {
            $(this).closest('tr').remove();
        }
        recalcAll();
    });

    // Init
    recalcAll();
});
</script>
<?= $this->endSection() ?>
