<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-person-plus me-2 text-primary"></i>
            <?= isset($client) ? 'Modifier le client' : 'Nouveau client' ?>
        </h1>
    </div>
    <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form action="<?= isset($client) ? base_url('clients/' . $client['id'] . '/update') : base_url('clients/store') ?>"
              method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control <?= session('errors.nom') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('nom', $client['nom'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.nom') ?></div>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                           value="<?= esc(old('email', $client['email'] ?? '')) ?>" required>
                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           value="<?= esc(old('telephone', $client['telephone'] ?? '')) ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?= esc(old('adresse', $client['adresse'] ?? '')) ?></textarea>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Ville</label>
                    <input type="text" name="ville" class="form-control"
                           value="<?= esc(old('ville', $client['ville'] ?? '')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Code postal</label>
                    <input type="text" name="code_postal" class="form-control"
                           value="<?= esc(old('code_postal', $client['code_postal'] ?? '')) ?>">
                </div>

                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><?= isset($client) ? 'Enregistrer' : 'Créer le client' ?>
                    </button>
                    <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
