<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i>Paramètres de la boutique</h2>
        <p class="text-muted small mb-0">Logo et image de fond affichés sur la page d'accueil publique.</p>
    </div>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php $flash = session()->getFlashdata('success'); if ($flash): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= esc($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php $info = session()->getFlashdata('info'); if ($info): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i><?= esc($info) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $msg): ?>
                <li><?= esc($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post"
      action="<?= base_url('admin/company/settings') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- ── Logo ───────────────────────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-image me-2 text-primary"></i>Logo de la boutique
                </div>
                <div class="card-body">
                    <p class="text-muted small">Format JPG, PNG ou WebP — max 2 Mo.<br>
                       Affiché sous forme d'icône ronde (80×80 px) sur la card de la boutique.</p>

                    <!-- Prévisualisation actuelle -->
                    <?php if (! empty($company['logo_path'])): ?>
                    <div class="mb-3 text-center">
                        <img id="logo-preview"
                             src="<?= base_url(esc($company['logo_path'])) ?>"
                             alt="Logo actuel"
                             class="rounded-circle border shadow"
                             style="width:90px;height:90px;object-fit:contain;">
                        <p class="text-muted small mt-1 mb-0">Logo actuel</p>
                    </div>
                    <?php else: ?>
                    <div class="mb-3 text-center">
                        <div id="logo-preview"
                             class="rounded-circle border bg-light d-inline-flex align-items-center justify-content-center"
                             style="width:90px;height:90px;">
                            <i class="bi bi-shop text-secondary fs-3"></i>
                        </div>
                        <p class="text-muted small mt-1 mb-0">Aucun logo</p>
                    </div>
                    <?php endif; ?>

                    <input type="file" name="logo" id="input-logo"
                           class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>
        </div>

        <!-- ── Couverture ─────────────────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-card-image me-2 text-primary"></i>Image de fond
                </div>
                <div class="card-body">
                    <p class="text-muted small">Format JPG, PNG ou WebP — max 5 Mo.<br>
                       Utilisée comme fond de la card boutique sur la page d'accueil.</p>

                    <!-- Prévisualisation actuelle -->
                    <?php if (! empty($company['cover_path'])): ?>
                    <div class="mb-3 text-center">
                        <img id="cover-preview"
                             src="<?= base_url(esc($company['cover_path'])) ?>"
                             alt="Fond actuel"
                             class="rounded shadow"
                             style="width:100%;max-height:120px;object-fit:cover;">
                        <p class="text-muted small mt-1 mb-0">Image de fond actuelle</p>
                    </div>
                    <?php else: ?>
                    <div class="mb-3 text-center">
                        <div id="cover-preview"
                             class="rounded border bg-light d-flex align-items-center justify-content-center"
                             style="width:100%;height:100px;">
                            <i class="bi bi-image text-secondary fs-3"></i>
                        </div>
                        <p class="text-muted small mt-1 mb-0">Aucune image de fond</p>
                    </div>
                    <?php endif; ?>

                    <input type="file" name="cover" id="input-cover"
                           class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy me-2"></i>Enregistrer
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    // Prévisualisation logo
    document.getElementById('input-logo').addEventListener('change', function () {
        previewFile(this, 'logo-preview', true);
    });

    // Prévisualisation couverture
    document.getElementById('input-cover').addEventListener('change', function () {
        previewFile(this, 'cover-preview', false);
    });

    function previewFile(input, previewId, isRound) {
        var file = input.files[0];
        if (! file) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            var el = document.getElementById(previewId);
            if (el.tagName === 'IMG') {
                el.src = e.target.result;
            } else {
                // Remplacer le div placeholder par une image
                var img = document.createElement('img');
                img.id  = previewId;
                img.src = e.target.result;
                img.alt = 'Aperçu';
                img.className = isRound
                    ? 'rounded-circle border shadow'
                    : 'rounded shadow';
                img.style = isRound
                    ? 'width:90px;height:90px;object-fit:contain;'
                    : 'width:100%;max-height:120px;object-fit:cover;';
                el.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    }
})();
</script>
<?= $this->endSection() ?>
