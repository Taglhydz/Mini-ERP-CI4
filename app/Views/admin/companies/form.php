<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-<?= $company ? 'pencil-square' : 'shop' ?> me-2 text-primary"></i>
            <?= esc($titre) ?>
        </h2>
        <p class="text-muted small mb-0">
            <?= $company ? 'Modifier les informations de la boutique.' : 'Créer une nouvelle boutique.' ?>
        </p>
    </div>
    <a href="<?= base_url('admin/companies') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

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
      action="<?= $company
          ? base_url('admin/companies/' . $company['id'] . '/update')
          : base_url('admin/companies/store') ?>">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- ── Identité ────────────────────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>Identité
                </div>
                <div class="card-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="name">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="<?= esc(old('name', $company['name'] ?? '')) ?>"
                               required maxlength="150" placeholder="Ma boutique">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="slug">
                            Slug
                            <span class="text-muted fw-normal small ms-1">(généré automatiquement depuis le nom)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text text-muted small font-monospace">/shop/</span>
                            <input type="text" name="slug" id="slug" class="form-control font-monospace bg-body-secondary"
                                   value="<?= esc(old('slug', $company['slug'] ?? '')) ?>"
                                   maxlength="100" readonly aria-readonly="true"
                                   placeholder="généré-depuis-le-nom">
                        </div>
                        <div class="form-text">
                            <i class="bi bi-magic me-1"></i>Mis à jour automatiquement depuis le nom — toujours unique.
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= esc(old('email', $company['email'] ?? '')) ?>"
                               maxlength="150" placeholder="contact@boutique.fr">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="phone">Téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                               value="<?= esc(old('phone', $company['phone'] ?? '')) ?>"
                               maxlength="20" placeholder="0600000000">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="postal_code">Code postal</label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control"
                               value="<?= esc(old('postal_code', $company['postal_code'] ?? '')) ?>"
                               maxlength="10" placeholder="75000">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="city">Ville</label>
                        <input type="text" name="city" id="city" class="form-control"
                               value="<?= esc(old('city', $company['city'] ?? '')) ?>"
                               maxlength="100" placeholder="Paris">
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Thème ────────────────────────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-palette me-2 text-primary"></i>Thème de la boutique
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="color_primary">
                            Couleur principale
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color_primary" id="color_primary"
                                   class="form-control form-control-color"
                                   value="<?= esc(old('color_primary', $company['color_primary'] ?? '#0d6efd')) ?>"
                                   style="width:50px;height:38px;">
                            <input type="text" id="color_primary_hex" class="form-control font-monospace"
                                   value="<?= esc(old('color_primary', $company['color_primary'] ?? '#0d6efd')) ?>"
                                   maxlength="7" pattern="#[0-9a-fA-F]{6}" placeholder="#0d6efd">
                        </div>
                        <div class="form-text">Boutons, en-têtes de tableau, icônes actives.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold" for="color_secondary">
                            Couleur secondaire
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color_secondary" id="color_secondary"
                                   class="form-control form-control-color"
                                   value="<?= esc(old('color_secondary', $company['color_secondary'] ?? '#6c757d')) ?>"
                                   style="width:50px;height:38px;">
                            <input type="text" id="color_secondary_hex" class="form-control font-monospace"
                                   value="<?= esc(old('color_secondary', $company['color_secondary'] ?? '#6c757d')) ?>"
                                   maxlength="7" pattern="#[0-9a-fA-F]{6}" placeholder="#6c757d">
                        </div>
                        <div class="form-text">Sous-entêtes, cartes résumé, séparateurs décoratifs.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block">Afficher le nom</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="show_name" id="show_name_yes" value="1" class="form-check-input"
                                <?= old('show_name', $company['show_name'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="show_name_yes">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="show_name" id="show_name_no" value="0" class="form-check-input"
                                <?= old('show_name', $company['show_name'] ?? 1) == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="show_name_no">Non</label>
                        </div>
                        <div class="form-text">Apparaît dans le pied de la carte boutique.</div>
                    </div>

                    <!-- Mini-prévisualisation des couleurs -->
                    <div class="col-12 mt-2">
                        <div class="form-text fw-semibold mb-2">Aperçu</div>
                        <div id="color-preview" class="rounded-3 p-3 border"
                             style="background:var(--prev-secondary, #6c757d);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-white"
                                     style="width:30px;height:30px;"></div>
                                <span class="text-white fw-semibold small" id="prev-name">
                                    <?= esc($company['name'] ?? 'Nom boutique') ?>
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm px-3 text-white fw-semibold rounded-pill"
                                    style="background:var(--prev-primary, #0d6efd);">
                                Commander
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /row -->

    <?php if ($company): ?>
    <!-- ── Logo / Cover actuels (suppression modération) ────────────────────────────────── -->
    <div class="row g-4 mt-1">
        <?php if (! empty($company['logo_path'])): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-image me-2 text-primary"></i>Logo actuel
                </div>
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="<?= base_url(esc($company['logo_path'])) ?>"
                         alt="Logo" class="rounded-circle border shadow"
                         style="width:72px;height:72px;object-fit:contain;">
                    <form method="post"
                          action="<?= base_url('admin/companies/' . $company['id'] . '/remove-logo') ?>"
                          onsubmit="return confirm('Supprimer ce logo ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Supprimer le logo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if (! empty($company['cover_path'])): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-card-image me-2 text-primary"></i>Image de fond actuelle
                </div>
                <div class="card-body">
                    <img src="<?= base_url(esc($company['cover_path'])) ?>"
                         alt="Fond" class="rounded shadow mb-3"
                         style="width:100%;max-height:100px;object-fit:cover;">
                    <form method="post"
                          action="<?= base_url('admin/companies/' . $company['id'] . '/remove-cover') ?>"
                          onsubmit="return confirm('Supprimer cette image de fond ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Supprimer le fond
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i><?= $company ? 'Enregistrer' : 'Créer la boutique' ?>
        </button>
        <a href="<?= base_url('admin/companies') ?>" class="btn btn-outline-secondary">
            Annuler
        </a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    /* Sync color picker ↔ text input */
    function syncColorInputs(pickerId, hexId) {
        var picker = document.getElementById(pickerId);
        var hex    = document.getElementById(hexId);
        if (! picker || ! hex) return;

        picker.addEventListener('input', function () {
            hex.value = picker.value;
            updatePreview();
        });
        hex.addEventListener('input', function () {
            if (/^#[0-9a-fA-F]{6}$/.test(hex.value)) {
                picker.value = hex.value;
                updatePreview();
            }
        });
    }
    syncColorInputs('color_primary', 'color_primary_hex');
    syncColorInputs('color_secondary', 'color_secondary_hex');

    /* Preview live */
    var preview = document.getElementById('color-preview');
    var prevName = document.getElementById('prev-name');
    var nameInput = document.getElementById('name');

    function updatePreview() {
        var p = document.getElementById('color_primary').value;
        var s = document.getElementById('color_secondary').value;
        if (preview) {
            preview.style.setProperty('--prev-primary', p);
            preview.style.setProperty('--prev-secondary', s);
            preview.style.background = s;
            var btn = preview.querySelector('button');
            if (btn) btn.style.background = p;
        }
    }

    if (nameInput && prevName) {
        nameInput.addEventListener('input', function () {
            prevName.textContent = nameInput.value || 'Nom boutique';
        });
    }

    /* Auto-génération du slug depuis le nom */
    var slugInput = document.getElementById('slug');
    function slugify(text) {
        var map = {
            'à':'a','â':'a','ä':'a','á':'a','ã':'a','å':'a',
            'è':'e','ê':'e','ë':'e','é':'e',
            'ì':'i','î':'i','ï':'i','í':'i',
            'ò':'o','ô':'o','ö':'o','ó':'o','õ':'o','ø':'o',
            'ù':'u','û':'u','ü':'u','ú':'u',
            'ç':'c','ñ':'n','ý':'y','ÿ':'y',
            'œ':'oe','æ':'ae',
        };
        text = text.split('').map(function(c) {
            return map[c.toLowerCase()] || c.toLowerCase();
        }).join('');
        return text
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-{2,}/g, '-')
            .substring(0, 80);
    }
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            slugInput.value = slugify(nameInput.value) || '';
        });
    }

    /* Init preview */
    updatePreview();
})();
</script>
<?= $this->endSection() ?>
