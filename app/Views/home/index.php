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
        <div class="row g-4">
            <?php foreach ($companies as $company): ?>
            <div class="col-sm-6 col-lg-4">
                <a href="<?= base_url('shop/' . esc($company['slug']) . '/catalog') ?>"
                   class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body d-flex align-items-center gap-3 p-4">
                            <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:56px;height:56px;">
                                <i class="bi bi-shop fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1 text-body fw-bold"><?= esc($company['name']) ?></h5>
                                <?php if (! empty($company['city'])): ?>
                                    <p class="card-text text-muted small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                                        <?php if (! empty($company['postal_code'])): ?>
                                            (<?= esc($company['postal_code']) ?>)
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top border-0 pt-0 pb-3 px-4">
                            <span class="btn btn-sm btn-outline-primary w-100">
                                Voir le catalogue <i class="bi bi-arrow-right ms-1"></i>
                            </span>
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
.card-hover { transition: transform .18s, box-shadow .18s; }
.card-hover:hover { transform: translateY(-4px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12) !important; }
</style>
<?= $this->endSection() ?>
