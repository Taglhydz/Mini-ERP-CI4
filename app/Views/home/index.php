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
                    <div class="shop-card rounded-4 overflow-hidden shadow d-flex flex-column"
                         style="<?= $coverStyle ?>">

                        <!-- Corps de la card — hauteur fixe identique pour toutes -->
                        <div class="shop-card-body d-flex flex-column align-items-center justify-content-center p-4"
                             style="height:190px;flex-shrink:0;">
                            <?php if ($hasLogo): ?>
                                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                                     alt="<?= esc($company['name']) ?>"
                                     class="shop-logo"
                                     style="max-width:120px;max-height:80px;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(0,0,0,.35));">
                            <?php else: ?>
                                <div class="shop-logo-placeholder d-flex align-items-center justify-content-center"
                                     style="width:80px;height:80px;">
                                    <i class="bi bi-shop fs-1 text-white opacity-75"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pied de card — toujours rendu, hauteur identique pour toutes -->
                        <div class="shop-card-footer px-3<?= empty($company['show_name']) ? ' shop-card-footer--nameless' : '' ?>"
                             style="height:66px;flex-shrink:0;backdrop-filter:blur(4px);
                                    display:flex;flex-direction:column;justify-content:center;
                                    gap:2px;overflow:hidden;">
                            <!-- Ligne nom : invisible si show_name = false, mais occupe toujours sa place -->
                            <p class="text-white fw-bold mb-0 text-truncate"
                               style="<?= empty($company['show_name']) ? 'visibility:hidden;' : '' ?>line-height:1.4;">
                                <?= esc($company['name']) ?>
                            </p>
                            <!-- Ligne ville : toujours visible si elle existe, invisible placeholder sinon -->
                            <?php if (! empty($company['city'])): ?>
                                <p class="text-white-50 small mb-0 text-truncate" style="line-height:1.3;">
                                    <i class="bi bi-geo-alt me-1"></i><?= esc($company['city']) ?>
                                </p>
                            <?php else: ?>
                                <p class="mb-0 small" style="visibility:hidden;line-height:1.3;">&nbsp;</p>
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
