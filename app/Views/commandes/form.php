<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-cart-plus me-2 text-warning"></i>
            <?= isset($commande) ? 'Modifier la commande' : 'Nouvelle commande' ?>
        </h1>
    </div>
    <a href="<?= base_url('commandes') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<form action="<?= isset($commande) ? base_url('commandes/' . $commande['id'] . '/update') : base_url('commandes/store') ?>"
      method="post" id="form-commande">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Informations générales -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom fw-semibold">Informations</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= esc($client['id']) ?>"
                                    <?= (old('client_id', $commande['client_id'] ?? '') == $client['id']) ? 'selected' : '' ?>>
                                    <?= esc($client['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date_commande" class="form-control"
                               value="<?= esc(old('date_commande', $commande['date_commande'] ?? date('Y-m-d'))) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <?php foreach (['brouillon' => 'Brouillon', 'confirmee' => 'Confirmée',
                                             'livree' => 'Livrée', 'annulee' => 'Annulée'] as $val => $label): ?>
                                <option value="<?= $val ?>"
                                    <?= (old('statut', $commande['statut'] ?? 'brouillon') === $val) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">TVA (%)</label>
                        <input type="number" name="taux_tva" step="0.01" min="0" max="100"
                               class="form-control" id="input-tva"
                               value="<?= esc(old('taux_tva', $commande['taux_tva'] ?? '20')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"><?= esc(old('notes', $commande['notes'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lignes de commande -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom fw-semibold d-flex justify-content-between">
                    <span>Lignes de commande</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-ligne">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter une ligne
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="table-lignes">
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
                            <tbody id="lignes-body">
                                <?php
                                $lignesExistantes = $lignes ?? [];
                                if (empty($lignesExistantes)): ?>
                                    <!-- Ligne vide initiale -->
                                    <tr class="ligne-row">
                                        <td>
                                            <select name="produit_id[]" class="form-select form-select-sm select-produit">
                                                <option value="">— Choisir —</option>
                                                <?php foreach ($produits as $p): ?>
                                                    <option value="<?= $p['id'] ?>"
                                                            data-prix="<?= $p['prix_unitaire'] ?>"
                                                            data-designation="<?= esc($p['designation']) ?>">
                                                        <?= esc($p['reference']) ?> — <?= esc($p['designation']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="designation[]" class="form-control form-control-sm input-designation"></td>
                                        <td><input type="number" name="quantite[]" value="1" min="1" class="form-control form-control-sm input-qte"></td>
                                        <td><input type="number" name="prix_unitaire[]" step="0.01" min="0" value="0.00" class="form-control form-control-sm input-prix"></td>
                                        <td class="text-end fw-semibold span-sous-total">0,00 €</td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-ligne"><i class="bi bi-x"></i></button></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lignesExistantes as $lg): ?>
                                    <tr class="ligne-row">
                                        <td>
                                            <select name="produit_id[]" class="form-select form-select-sm select-produit">
                                                <option value="">— Choisir —</option>
                                                <?php foreach ($produits as $p): ?>
                                                    <option value="<?= $p['id'] ?>"
                                                            data-prix="<?= $p['prix_unitaire'] ?>"
                                                            data-designation="<?= esc($p['designation']) ?>"
                                                            <?= ($p['id'] == $lg['produit_id']) ? 'selected' : '' ?>>
                                                        <?= esc($p['reference']) ?> — <?= esc($p['designation']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="designation[]" class="form-control form-control-sm input-designation" value="<?= esc($lg['designation']) ?>"></td>
                                        <td><input type="number" name="quantite[]" value="<?= esc($lg['quantite']) ?>" min="1" class="form-control form-control-sm input-qte"></td>
                                        <td><input type="number" name="prix_unitaire[]" step="0.01" min="0" value="<?= esc($lg['prix_unitaire']) ?>" class="form-control form-control-sm input-prix"></td>
                                        <td class="text-end fw-semibold span-sous-total"><?= number_format((float)$lg['sous_total'], 2, ',', ' ') ?> €</td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-ligne"><i class="bi bi-x"></i></button></td>
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
                                    <td class="text-end" id="total-tva">0,00 €</td>
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
                    <i class="bi bi-save me-1"></i><?= isset($commande) ? 'Enregistrer' : 'Créer la commande' ?>
                </button>
                <a href="<?= base_url('commandes') ?>" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>
    </div>
</form>

<!-- Template ligne (hidden) -->
<template id="ligne-template">
    <tr class="ligne-row">
        <td>
            <select name="produit_id[]" class="form-select form-select-sm select-produit">
                <option value="">— Choisir —</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= $p['id'] ?>"
                            data-prix="<?= $p['prix_unitaire'] ?>"
                            data-designation="<?= esc($p['designation']) ?>">
                        <?= esc($p['reference']) ?> — <?= esc($p['designation']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="designation[]" class="form-control form-control-sm input-designation"></td>
        <td><input type="number" name="quantite[]" value="1" min="1" class="form-control form-control-sm input-qte"></td>
        <td><input type="number" name="prix_unitaire[]" step="0.01" min="0" value="0.00" class="form-control form-control-sm input-prix"></td>
        <td class="text-end fw-semibold span-sous-total">0,00 €</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-del-ligne"><i class="bi bi-x"></i></button></td>
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
        const qte   = parseFloat($row.find('.input-qte').val()) || 0;
        const prix  = parseFloat($row.find('.input-prix').val()) || 0;
        const total = qte * prix;
        $row.find('.span-sous-total').text(fmt(total));
        return total;
    }

    function recalcAll() {
        let ht = 0;
        $('#lignes-body .ligne-row').each(function () {
            ht += recalcRow($(this));
        });
        const tva = parseFloat($('#input-tva').val()) || 0;
        const tvaAmt = ht * tva / 100;
        $('#total-ht').text(fmt(ht));
        $('#total-tva').text(fmt(tvaAmt));
        $('#total-ttc').text(fmt(ht + tvaAmt));
    }

    // Auto-fill depuis le select produit
    $(document).on('change', '.select-produit', function () {
        const $opt = $(this).find('option:selected');
        const $row = $(this).closest('tr');
        $row.find('.input-designation').val($opt.data('designation') || '');
        $row.find('.input-prix').val(parseFloat($opt.data('prix') || 0).toFixed(2));
        recalcAll();
    });

    // Recalc à chaque saisie
    $(document).on('input', '.input-qte, .input-prix', recalcAll);
    $('#input-tva').on('input', recalcAll);

    // Ajouter ligne
    $('#btn-add-ligne').on('click', function () {
        const tpl = document.getElementById('ligne-template').content.cloneNode(true);
        $('#lignes-body').append(tpl);
        recalcAll();
    });

    // Supprimer ligne
    $(document).on('click', '.btn-del-ligne', function () {
        if ($('#lignes-body .ligne-row').length > 1) {
            $(this).closest('tr').remove();
        }
        recalcAll();
    });

    // Init
    recalcAll();
});
</script>
<?= $this->endSection() ?>
