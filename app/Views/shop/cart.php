<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
// ── Variables thème entreprise ─────────────────────────────────────────────
$hasCover     = ! empty($company['cover_path']);
$hasLogo      = ! empty($company['logo_path']);
$showName     = isset($company['show_name']) ? (bool) $company['show_name'] : true;
$colorPrimary = (! empty($company['color_primary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_primary']))
    ? $company['color_primary']
    : '#0d6efd';
$colorSecondary = (! empty($company['color_secondary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_secondary']))
    ? $company['color_secondary']
    : '#ffffff';
?>

<style>
    :root {
        --company-primary:   <?= esc($colorPrimary) ?>;
        --company-secondary: <?= esc($colorSecondary) ?>;
    }

    /* Boutons primary */
    .cart-page .btn-primary,
    .cart-page .btn-primary:focus {
        background-color: var(--company-primary);
        border-color: var(--company-primary);
    }
    .cart-page .btn-primary:hover {
        background-color: color-mix(in srgb, var(--company-primary) 85%, #000);
        border-color: color-mix(in srgb, var(--company-primary) 85%, #000);
    }
    .cart-page .text-primary { color: var(--company-primary) !important; }

    /* Cards articles */
    .cart-item-card {
        background-color: color-mix(in srgb, var(--company-primary) 10%, var(--bs-body-bg));
        border: 1px solid color-mix(in srgb, var(--company-primary) 30%, transparent) !important;
        transition: box-shadow .18s, border-color .18s;
    }
    .cart-item-card:hover {
        box-shadow: 0 .3rem 1rem color-mix(in srgb, var(--company-primary) 20%, transparent) !important;
        border-color: color-mix(in srgb, var(--company-primary) 55%, transparent) !important;
    }

    /* Encart récapitulatif */
    .cart-summary-card {
        background-color: color-mix(in srgb, var(--company-primary) 8%, var(--bs-body-bg));
        border: 1px solid color-mix(in srgb, var(--company-primary) 35%, transparent) !important;
    }
    .cart-summary-card .card-header {
        background-color: color-mix(in srgb, var(--company-primary) 18%, var(--bs-body-bg));
        border-bottom: 1px solid color-mix(in srgb, var(--company-primary) 30%, transparent);
    }
    .cart-total-amount {
        color: var(--company-primary);
    }

    /* Badge quantité */
    .qty-badge {
        background-color: var(--company-primary) !important;
    }
    /* Texte bannière sur fond color_primary */
    .cart-page > .position-relative.overflow-hidden .text-white { color: var(--company-secondary) !important; }
    .cart-page > .position-relative.overflow-hidden h1.text-white { text-shadow: 0 2px 8px rgba(0,0,0,.4); }
</style>

<div class="cart-page">

    <!-- ── Bannière boutique (même style que catalog) ─────────────────────── -->
    <div class="position-relative overflow-hidden"
         style="min-height:180px;<?= $hasCover
            ? 'background:url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat;'
            : 'background:linear-gradient(135deg,var(--company-primary) 0%,color-mix(in srgb,var(--company-primary) 75%,#000) 100%);' ?>">

        <!-- Overlay -->
        <div style="position:absolute;inset:0;background:rgba(0,0,0,<?= $hasCover ? '.52' : '.15' ?>);backdrop-filter:<?= $hasCover ? 'blur(1px)' : 'none' ?>;"></div>

        <!-- Contenu bannière -->
        <div class="position-relative d-flex align-items-center gap-4 px-4 py-4"
             style="min-height:180px;">

            <?php if ($hasLogo): ?>
                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                     alt="<?= esc($company['name']) ?>"
                     style="max-width:100px;max-height:65px;object-fit:contain;
                            filter:drop-shadow(0 2px 10px rgba(0,0,0,.55));flex-shrink:0;">
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:60px;height:60px;border-radius:.75rem;background:rgba(255,255,255,.18);">
                    <i class="bi bi-shop fs-3 text-white"></i>
                </div>
            <?php endif; ?>

            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-cart3 text-white fs-5"></i>
                    <h1 class="text-white fw-bold mb-0 h4">Mon panier</h1>
                </div>
                <?php if ($showName): ?>
                    <p class="text-white mb-0 small" style="opacity:.85;text-shadow:0 1px 4px rgba(0,0,0,.5);">
                        <?= esc($company['name']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="ms-auto">
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                   class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Continuer mes achats</span><span class="d-sm-none">Catalogue</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Contenu panier ─────────────────────────────────────────────────── -->
    <div class="container py-5">

        <?php $flash = session()->getFlashdata('success'); if ($flash): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= esc($flash) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-50"></i>
                <p class="fs-5">Votre panier est vide.</p>
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                   class="btn btn-primary">
                    <i class="bi bi-shop me-1"></i>Voir le catalogue
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4 align-items-start">

                <!-- ── Liste des articles ──────────────────────────────────── -->
                <div class="col-lg-8">
                    <h2 class="h6 text-muted fw-semibold text-uppercase mb-3 ls-1">
                        <i class="bi bi-list-ul me-1"></i>Articles
                    </h2>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($cart as $productId => $item): ?>
                        <div class="card cart-item-card border-0 rounded-3">
                            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                                <!-- Icône produit -->
                                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-2"
                                     style="width:42px;height:42px;
                                            background:color-mix(in srgb,var(--company-primary) 18%,var(--bs-body-bg));">
                                    <i class="bi bi-box text-primary"></i>
                                </div>

                                <!-- Nom -->
                                <div class="flex-grow-1 min-width-0">
                                    <p class="fw-semibold mb-0 text-truncate"><?= esc($item['name']) ?></p>
                                    <p class="text-muted small mb-0">
                                        <?= number_format((float) $item['price'], 2, ',', ' ') ?> € / unité
                                    </p>
                                </div>

                                <!-- Quantité -->
                                <span class="badge qty-badge rounded-pill px-3 py-2 flex-shrink-0">
                                    × <?= (int) $item['qty'] ?>
                                </span>

                                <!-- Sous-total -->
                                <div class="text-end flex-shrink-0" style="min-width:90px;">
                                    <p class="fw-bold mb-0 text-primary fs-6">
                                        <?= number_format((float) $item['qty'] * (float) $item['price'], 2, ',', ' ') ?> €
                                    </p>
                                </div>

                                <!-- Supprimer -->
                                <form method="post"
                                      action="<?= base_url('shop/' . esc($company['slug']) . '/cart/remove') ?>"
                                      class="flex-shrink-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Lien retour catalogue (mobile) -->
                    <div class="mt-4 d-lg-none">
                        <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                           class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-1"></i>Continuer mes achats
                        </a>
                    </div>
                </div>

                <!-- ── Récapitulatif ───────────────────────────────────────── -->
                <div class="col-lg-4">
                    <h2 class="h6 text-muted fw-semibold text-uppercase mb-3 ls-1">
                        <i class="bi bi-receipt me-1"></i>Récapitulatif
                    </h2>
                    <div class="card cart-summary-card border-0 rounded-3">
                        <div class="card-header fw-semibold">
                            <i class="bi bi-calculator me-2 text-primary"></i>Détail de la commande
                        </div>
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total HT</span>
                                <span class="fw-semibold">
                                    <?= number_format($amountHt, 2, ',', ' ') ?> €
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">TVA (<?= (int) $vatRate ?>%)</span>
                                <span>
                                    <?= number_format($amountTtc - $amountHt, 2, ',', ' ') ?> €
                                </span>
                            </div>
                            <hr class="my-2" style="border-color:color-mix(in srgb,var(--company-primary) 30%,transparent);">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold fs-5">Total TTC</span>
                                <span class="fw-bold cart-total-amount fs-4">
                                    <?= number_format($amountTtc, 2, ',', ' ') ?> €
                                </span>
                            </div>
                            <a href="<?= base_url('shop/' . esc($company['slug']) . '/checkout') ?>"
                               class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-bag-check me-2"></i>Passer la commande
                            </a>
                            <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                               class="btn btn-outline-secondary w-100 mt-2 d-none d-lg-block">
                                <i class="bi bi-arrow-left me-1"></i>Continuer mes achats
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>

</div><!-- /.cart-page -->

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.ls-1 { letter-spacing: .06em; }
</style>
<?= $this->endSection() ?>
