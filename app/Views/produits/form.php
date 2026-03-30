<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-box-seam me-2 text-success"></i>
            <?= isset($produit) ? 'Modifier le produit' : 'Nouveau produit' ?>
        </h1>
    </div>
    <a href="<?= base_url('produits') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 580px;">
    <div class="card-body">
        <form action="<?= isset($produit) ? base_url('produits/' . $produit['id'] . '/update') : base_url('produits/store') ?>"
              method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference"
                           class="form-control <?= session('errors.reference') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('reference', $produit['reference'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.reference') ?></div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Désignation <span class="text-danger">*</span></label>
                    <input type="text" name="designation"
                           class="form-control <?= session('errors.designation') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('designation', $produit['designation'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.designation') ?></div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?= esc(old('description', $produit['description'] ?? '')) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prix unitaire HT (€) <span class="text-danger">*</span></label>
                    <input type="number" name="prix_unitaire" step="0.01" min="0"
                           class="form-control <?= session('errors.prix_unitaire') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('prix_unitaire', $produit['prix_unitaire'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.prix_unitaire') ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Stock</label>
                    <input type="number" name="stock" min="0"
                           class="form-control"
                           value="<?= esc(old('stock', $produit['stock'] ?? 0)) ?>">
                </div>

                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i><?= isset($produit) ? 'Enregistrer' : 'Créer le produit' ?>
                    </button>
                    <a href="<?= base_url('produits') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
