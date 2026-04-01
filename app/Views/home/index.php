<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="py-5" style="background:linear-gradient(135deg,#0d6efd 0%,#0a4fb4 100%);">
    <div class="container text-center text-white py-3">
        <h1 class="display-5 fw-bold mb-2">
            <i class="bi bi-shop-window me-2"></i>Nos boutiques
        </h1>
        <p class="lead opacity-75 mb-0">Choisissez une boutique pour consulter le catalogue et passer commande.</p>
    </div>
</div>

<!-- Grille des boutiques -->
<div class="container py-5">
    <?php if (empty($companies)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-shop fs-1 d-block mb-3 opacity-50"></i>
            <p class="fs-5">Aucune boutique disponible pour l'instant.</p>
        </div>
    <?php else: ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($companies as $company):
                $hasCover = ! empty($company['cover_path']);
                $hasLogo  = ! empty($company['logo_path']);

                $coverStyle = $hasCover
                    ? 'background-image:url(' . base_url(esc($company['cover_path'])) . ');background-size:cover;background-position:center;'
                    : 'background:linear-gradient(135deg,#0d6efd 0%,#0a4fb4 100%);';
            ?>
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                   class="text-decoration-none shop-card-link">
                    <div class="shop-card rounded-4 overflow-hidden shadow"
                         style="<?= $coverStyle ?>">

                        <!-- Corps de la card -->
                        <div class="shop-card-body d-flex flex-column align-items-center justify-content-center p-4"
                             style="min-height:190px;">
                            <?php if ($hasLogo): ?>
                                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                                     alt="<?= esc($company['name']) ?>"
                                     class="shop-logo rounded-circle bg-white shadow"
                                     style="width:80px;height:80px;object-fit:contain;padding:6px;">
                            <?php else: ?>
                                <div class="shop-logo-placeholder rounded-circle bg-white bg-opacity-90 shadow
                                            d-flex align-items-center justify-content-center"
                                     style="width:80px;height:80px;">
                                    <i class="bi bi-shop fs-2 text-primary"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pied de card avec nom -->
                        <div class="shop-card-footer px-3 py-2"
                             style="background:rgba(0,0,0,.45);backdrop-filter:blur(4px);">
                            <p class="text-white fw-bold mb-0 text-truncate"><?= esc($company['name']) ?></p>
                            <?php if (! empty($company['city'])): ?>
                                <p class="text-white-50 small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.shop-card {
    transition: transform .2s ease, box-shadow .2s ease;
    cursor: pointer;
}
.shop-card-link:hover .shop-card {
    transform: scale(1.04);
    box-shadow: 0 .75rem 2rem rgba(0,0,0,.25) !important;
}
.shop-card-body {
    /* fond semi-transparent pour lisibilité du logo si image de couverture */
    background: rgba(0,0,0,.12);
}
.shop-logo {
    transition: transform .2s ease;
}
.shop-card-link:hover .shop-logo {
    transform: scale(1.08);
}
</style>
<?= $this->endSection() ?>
