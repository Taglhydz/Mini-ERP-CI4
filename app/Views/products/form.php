<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-box-seam me-2 text-success"></i>
            <?= isset($product) ? 'Modifier le produit' : 'Nouveau produit' ?>
        </h1>
    </div>
    <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 580px;">
    <div class="card-body">
        <form action="<?= isset($product) ? base_url('products/' . $product['id'] . '/update') : base_url('products/store') ?>"
              method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference"
                           class="form-control <?= session('errors.reference') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('reference', $product['reference'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.reference') ?></div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Désignation <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('name', $product['name'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.name') ?></div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?= esc(old('description', $product['description'] ?? '')) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prix unitaire HT (€) <span class="text-danger">*</span></label>
                    <input type="number" name="unit_price" step="0.01" min="0"
                           class="form-control <?= session('errors.unit_price') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('unit_price', $product['unit_price'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.unit_price') ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Stock</label>
                    <input type="number" name="stock" min="0"
                           class="form-control"
                           value="<?= esc(old('stock', $product['stock'] ?? 0)) ?>">
                </div>

                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i><?= isset($product) ? 'Enregistrer' : 'Créer le produit' ?>
                    </button>
                    <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
