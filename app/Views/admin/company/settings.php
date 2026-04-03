<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i>Paramètres de la boutique</h2>
        <p class="text-muted small mb-0">Logo, image de fond, couleur principale et affichage du nom pour la boutique publique.</p>
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
                        <p class="text-muted small mt-1 mb-2">Logo actuel</p>
                        <form method="post"
                              action="<?= base_url('admin/company/settings/remove-logo') ?>"
                              onsubmit="return confirm('Supprimer ce logo ?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </form>
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
                        <p class="text-muted small mt-1 mb-2">Image de fond actuelle</p>
                        <form method="post"
                              action="<?= base_url('admin/company/settings/remove-cover') ?>"
                              onsubmit="return confirm('Supprimer cette image de fond ?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </form>
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

    </div><!-- /.row logo/cover -->

    <div class="row g-4 mt-1">

        <!-- ── Afficher le nom ─────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-type me-2 text-primary"></i>Nom de la boutique
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <p class="text-muted small mb-2">
                        Choisissez si le nom de votre boutique doit être affiché
                        sur la card d’accueil et dans la bannière du catalogue.
                    </p>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               role="switch" id="input-show-name" name="show_name"
                               value="1" <?= ! empty($company['show_name']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="input-show-name">
                            Afficher le nom
                        </label>
                    </div>
                    <p class="text-muted small mb-0">
                        Laissez décoché si votre logo est suffisamment identifiable.
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Couleur principale ──────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-palette me-2 text-primary"></i>Couleur principale
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <p class="text-muted small mb-2">
                        Couleur utilisée pour les boutons « Ajouter au panier »
                        et les éléments d’accent dans la boutique publique.
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" name="color_primary" id="input-color-primary"
                               class="form-control form-control-color"
                               value="<?= esc($company['color_primary'] ?? '#0d6efd') ?>"
                               title="Couleur principale">
                        <label for="input-color-primary" class="form-label mb-0 fw-semibold">
                            Couleur principale
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Couleur secondaire ────────────────────────────────────── -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-palette2 me-2 text-primary"></i>Couleur secondaire
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <p class="text-muted small mb-2">
                        Couleur des textes et icônes sur les fonds <strong>color_primary</strong> :
                        titres dans la bannière, badges, boutons. Blanc par défaut.
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" name="color_secondary" id="input-color-secondary"
                               class="form-control form-control-color"
                               value="<?= esc($company['color_secondary'] ?? '#ffffff') ?>"
                               title="Couleur secondaire">
                        <label for="input-color-secondary" class="form-label mb-0 fw-semibold">
                            Couleur secondaire
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Aperçu live ──────────────────────────────────────────────────── -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-eye me-2 text-primary"></i>Aperçu en direct
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-start">

                        <!-- Card boutique (accueil) -->
                        <div class="col-md-4">
                            <p class="text-muted small mb-2 fw-semibold">Card d’accueil</p>
                            <div class="card border-0 shadow overflow-hidden" id="preview-shop-card"
                                 style="max-width:320px;background:<?= ! empty($company['cover_path'])
                                                ? 'url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat'
                                                : 'linear-gradient(135deg,var(--preview-color,#0d6efd) 0%,color-mix(in srgb,var(--preview-color,#0d6efd) 75%,#000) 100%)' ?>;">
                                <!-- Fond cover -->
                                <div id="preview-card-cover"
                                     class="position-relative d-flex align-items-center justify-content-center"
                                     style="height:110px;">
                                    <div style="position:absolute;inset:0;background:rgba(0,0,0,.25);"></div>
                                    <?php if (! empty($company['logo_path'])): ?>
                                        <img id="preview-card-logo"
                                             src="<?= base_url(esc($company['logo_path'])) ?>"
                                             alt="Logo"
                                             style="position:relative;max-width:90px;max-height:65px;
                                                    object-fit:contain;
                                                    filter:drop-shadow(0 2px 6px rgba(0,0,0,.55));">
                                    <?php else: ?>
                                        <div id="preview-card-logo"
                                             class="position-relative d-flex align-items-center justify-content-center"
                                             style="width:52px;height:52px;border-radius:.75rem;
                                                    background:rgba(255,255,255,.2);">
                                            <i class="bi bi-shop text-white fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div id="preview-card-footer" class="card-body p-3">
                                    <p id="preview-card-name"
                                       class="fw-semibold mb-1 <?= empty($company['show_name']) ? 'd-none' : '' ?>">
                                        <?= esc($company['name']) ?>
                                    </p>
                                    <?php if (! empty($company['city'])): ?>
                                        <p class="small text-muted mb-2">
                                            <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Bannière catalogue -->
                        <div class="col-md-4">
                            <p class="text-muted small mb-2 fw-semibold">Bannière catalogue</p>
                            <div class="rounded shadow overflow-hidden position-relative"
                                 id="preview-hero"
                                 style="height:160px;
                                        background:<?= ! empty($company['cover_path'])
                                            ? 'url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat'
                                            : 'linear-gradient(135deg,var(--preview-color,#0d6efd) 0%,color-mix(in srgb,var(--preview-color,#0d6efd) 75%,#000) 100%)' ?>;">
                                <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);"></div>
                                <div class="position-relative h-100 d-flex flex-column align-items-center justify-content-center text-center px-3">
                                    <?php if (! empty($company['logo_path'])): ?>
                                        <img id="preview-hero-logo"
                                             src="<?= base_url(esc($company['logo_path'])) ?>"
                                             alt="Logo"
                                             style="max-width:90px;max-height:55px;object-fit:contain;
                                                    margin-bottom:.5rem;
                                                    filter:drop-shadow(0 2px 8px rgba(0,0,0,.55));">
                                    <?php else: ?>
                                        <div id="preview-hero-logo"
                                             class="d-flex align-items-center justify-content-center mb-2"
                                             style="width:52px;height:52px;border-radius:.75rem;
                                                    background:rgba(255,255,255,.18);">
                                            <i class="bi bi-shop text-white fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <p id="preview-hero-name"
                                       class="text-white fw-bold mb-0 <?= empty($company['show_name']) ? 'd-none' : '' ?>"
                                       style="font-size:1.1rem;text-shadow:0 2px 6px rgba(0,0,0,.5);">
                                        <?= esc($company['name']) ?>
                                    </p>
                                    <?php if (! empty($company['city'])): ?>
                                    <p class="text-white-50 small mb-0 mt-1">
                                        <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Fiche produit (colonne catalogue) -->
                        <div class="col-md-4">
                            <p class="text-muted small mb-2 fw-semibold">Fiche produit</p>
                            <div class="card border-0 shadow overflow-hidden" style="max-width:240px;">
                                <!-- Image placeholder -->
                                <div style="height:85px;background:var(--bs-secondary-bg);
                                            display:flex;align-items:center;justify-content:center;
                                            border-bottom:1px solid var(--bs-border-color);">
                                    <i class="bi bi-image text-muted opacity-40 fs-2"></i>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="preview-badge-secondary badge px-2 py-1"
                                              style="background:var(--preview-color2,#6c757d);color:#fff;font-size:.65rem;">
                                            <i class="bi bi-upc me-1"></i>PROD-001
                                        </span>
                                        <span class="badge bg-success" style="font-size:.65rem;">En stock</span>
                                    </div>
                                    <div class="fw-semibold small mb-0">Produit exemple</div>
                                    <div class="text-muted small mb-1" style="font-size:.75rem;">Description courte…</div>
                                    <div class="fw-bold mb-2" style="color:var(--preview-color,#0d6efd);font-size:.9rem;">19,90 € HT</div>
                                    <button type="button" class="btn btn-sm w-100 preview-btn-primary"
                                            style="background:var(--preview-color,#0d6efd);
                                                   border-color:var(--preview-color,#0d6efd);color:#fff;">
                                        <i class="bi bi-cart-plus me-1"></i>Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Menu latéral boutique -->
                        <div class="col-md-4">
                            <p class="text-muted small mb-2 fw-semibold">Menu latéral boutique</p>
                            <div id="preview-sidebar" class="rounded shadow overflow-hidden"
                                 style="max-width:220px;background:var(--preview-color2,#f8f9fa);">
                                <!-- Header sidebar -->
                                <div id="preview-sidebar-header"
                                     class="d-flex align-items-center gap-2 p-3"
                                     style="background:var(--preview-color,#0d6efd);">
                                    <?php if (! empty($company['logo_path'])): ?>
                                        <img id="preview-sidebar-logo"
                                             src="<?= base_url(esc($company['logo_path'])) ?>"
                                             alt="Logo"
                                             style="width:32px;height:32px;object-fit:contain;
                                                    filter:drop-shadow(0 1px 4px rgba(0,0,0,.4));">
                                    <?php else: ?>
                                        <div id="preview-sidebar-logo"
                                             class="d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:32px;height:32px;border-radius:.4rem;
                                                    background:rgba(255,255,255,.2);">
                                            <i class="bi bi-shop text-white" style="font-size:.85rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span id="preview-sidebar-name"
                                          class="text-white fw-semibold small text-truncate <?= empty($company['show_name']) ? 'd-none' : '' ?>">
                                        <?= esc($company['name']) ?>
                                    </span>
                                </div>
                                <!-- Nav items -->
                                <div class="p-2">
                                    <div class="px-3 py-2 rounded small fw-semibold d-flex align-items-center gap-2 preview-sidebar-active"
                                         style="background:rgba(13,110,253,.08);
                                                border-left:3px solid var(--preview-color,#0d6efd);
                                                color:var(--preview-color,#0d6efd);">
                                        <i class="bi bi-grid"></i> Tous les produits
                                    </div>
                                    <div class="px-3 py-2 rounded small text-muted d-flex align-items-center gap-2 mt-1"
                                         style="border-left:3px solid transparent;">
                                        <i class="bi bi-tag"></i> Catégorie A
                                    </div>
                                    <div class="px-3 py-2 rounded small text-muted d-flex align-items-center gap-2 mt-1"
                                         style="border-left:3px solid transparent;">
                                        <i class="bi bi-tag"></i> Catégorie B
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.row inner preview -->
                </div>
            </div>
        </div>

    </div><!-- /.row nouveaux champs -->

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy me-2"></i>Enregistrer
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    :root {
        --preview-color: <?= esc($company['color_primary'] ?? '#0d6efd') ?>;
        --preview-color2: <?= esc($company['color_secondary'] ?? '#ffffff') ?>;
    }
</style>
<script>
(function () {
    // ── Helper : lire un fichier et appeler le callback avec data-URL ──
    function readFile(input, cb) {
        var file = input.files[0];
        if (! file) return;
        var r = new FileReader();
        r.onload = function (e) { cb(e.target.result); };
        r.readAsDataURL(file);
    }

    // ── Helper : remplacer un placeholder div par une <img> ──────────
    function swapToImg(id, src, style) {
        var el = document.getElementById(id);
        if (! el) return el;
        if (el.tagName === 'IMG') { el.src = src; return el; }
        var img = document.createElement('img');
        img.id    = id;
        img.src   = src;
        img.alt   = 'Aperçu';
        img.style.cssText = style;
        el.replaceWith(img);
        return img;
    }

    // ── Logo ─────────────────────────────────────────────────────────
    document.getElementById('input-logo').addEventListener('change', function () {
        readFile(this, function (src) {
            // Prévisualisation champ logo (rond)
            var lpv = document.getElementById('logo-preview');
            if (lpv) {
                if (lpv.tagName === 'IMG') { lpv.src = src; }
                else {
                    var img = document.createElement('img');
                    img.id = 'logo-preview'; img.src = src; img.alt = 'Logo';
                    img.className = 'rounded-circle border shadow';
                    img.style.cssText = 'width:90px;height:90px;object-fit:contain;';
                    lpv.replaceWith(img);
                }
            }
            // Live previews — toutes les zones logo
            swapToImg('preview-card-logo', src,
                'position:relative;max-width:90px;max-height:65px;object-fit:contain;filter:drop-shadow(0 2px 6px rgba(0,0,0,.55));');
            swapToImg('preview-hero-logo', src,
                'max-width:90px;max-height:55px;object-fit:contain;margin-bottom:.5rem;filter:drop-shadow(0 2px 8px rgba(0,0,0,.55));');
            swapToImg('preview-sidebar-logo', src,
                'width:32px;height:32px;object-fit:contain;filter:drop-shadow(0 1px 4px rgba(0,0,0,.4));');
        });
    });

    // ── Couverture ──────────────────────────────────────────────────
    document.getElementById('input-cover').addEventListener('change', function () {
        readFile(this, function (src) {
            // Prévisualisation champ cover (rectangle)
            var cpv = document.getElementById('cover-preview');
            if (cpv) {
                if (cpv.tagName === 'IMG') { cpv.src = src; }
                else {
                    var img = document.createElement('img');
                    img.id = 'cover-preview'; img.src = src; img.alt = 'Fond';
                    img.className = 'rounded shadow';
                    img.style.cssText = 'width:100%;max-height:120px;object-fit:cover;';
                    cpv.replaceWith(img);
                }
            }
            // Live previews
            var coverStyle = 'url(' + src + ') center/cover no-repeat';
            var cardCover = document.getElementById('preview-card-cover');
            if (cardCover) cardCover.style.background = coverStyle;
            var hero = document.getElementById('preview-hero');
            if (hero) hero.style.background = coverStyle;
        });
    });

    // ── Afficher le nom ───────────────────────────────────────────────
    document.getElementById('input-show-name').addEventListener('change', function () {
        var show = this.checked;
        ['preview-card-name', 'preview-hero-name', 'preview-sidebar-name'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.classList.toggle('d-none', ! show);
        });
    });

    // ── Couleur principale ────────────────────────────────────────────
    document.getElementById('input-color-primary').addEventListener('input', function () {
        var color = this.value;
        document.documentElement.style.setProperty('--preview-color', color);
        // Boutons preview
        document.querySelectorAll('.preview-btn-primary').forEach(function (btn) {
            btn.style.background = color;
            btn.style.borderColor = color;
        });
        // Si pas de cover, recalculer le gradient de fond
        var cardCover = document.getElementById('preview-card-cover');
        var hero      = document.getElementById('preview-hero');
        var grad = 'linear-gradient(135deg,' + color + ' 0%,color-mix(in srgb,' + color + ' 75%,#000) 100%)';
        if (cardCover && ! cardCover.style.backgroundImage.startsWith('url')) {
            cardCover.style.background = grad;
        }
        if (hero && ! hero.style.backgroundImage.startsWith('url')) {
            hero.style.background = grad;
        }
        // Header sidebar
        var sidebarHeader = document.getElementById('preview-sidebar-header');
        if (sidebarHeader) sidebarHeader.style.background = color;
        // Item actif sidebar
        document.querySelectorAll('.preview-sidebar-active').forEach(function (el) {
            el.style.borderLeftColor = color;
            el.style.color = color;
        });
    });

    // ── Couleur secondaire ────────────────────────────────────────────
    document.getElementById('input-color-secondary').addEventListener('input', function () {
        var color = this.value;
        document.documentElement.style.setProperty('--preview-color2', color);
        // Badge référence produit → fond secondaire
        document.querySelectorAll('.preview-badge-secondary').forEach(function (badge) {
            badge.style.background = color;
        });
        // Prix produit → couleur secondaire si lisible, sinon primaryCouleur
        // Fond du menu latéral → couleur secondaire
        var sidebar = document.getElementById('preview-sidebar');
        if (sidebar) sidebar.style.background = color;
    });

})();
</script>
<?= $this->endSection() ?>
