<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// ── Variables thème entreprise ─────────────────────────────────────────────
['hasCover' => $hasCover, 'hasLogo' => $hasLogo, 'showName' => $showName,
 'colorPrimary' => $colorPrimary, 'colorSecondary' => $colorSecondary] = company_theme($company);
$companySlug = $company['slug'] ?? session('company_slug') ?? '';
?>

<style>
    :root {
        --company-primary:   <?= esc($colorPrimary) ?>;
        --company-secondary: <?= esc($colorSecondary) ?>;
    }
</style>

<div class="espace-client">

    <!-- ── Bannière boutique (cohérence catalogue / panier / checkout) ─────── -->
    <div class="position-relative overflow-hidden"
         style="min-height:180px;<?= $hasCover
            ? 'background:url(' . base_url(esc($company['cover_path'])) . ') center/cover no-repeat;'
            : 'background:linear-gradient(135deg,var(--company-primary) 0%,color-mix(in srgb,var(--company-primary) 75%,#000) 100%);' ?>">

        <!-- Overlay obscur -->
        <div style="position:absolute;inset:0;background:rgba(0,0,0,<?= $hasCover ? '.52' : '.15' ?>);backdrop-filter:<?= $hasCover ? 'blur(1px)' : 'none' ?>;"></div>

        <!-- Contenu bannière -->
        <div class="position-relative d-flex align-items-center gap-4 px-4 py-4" style="min-height:180px;">

            <?php if ($hasLogo): ?>
                <img src="<?= base_url(esc($company['logo_path'])) ?>"
                     alt="<?= esc($company['name'] ?? '') ?>"
                     style="max-width:100px;max-height:66px;object-fit:contain;
                            filter:drop-shadow(0 2px 8px rgba(0,0,0,.5));flex-shrink:0;">
            <?php else: ?>
                <div style="width:52px;height:52px;border-radius:.75rem;background:rgba(255,255,255,.18);
                            flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person-circle fs-3 text-white"></i>
                </div>
            <?php endif; ?>

            <div>
                <h1 class="text-white fw-bold mb-0 h4">
                    <i class="bi bi-person-check me-2"></i>Mon espace client
                </h1>
                <?php if ($showName && ! empty($company['name'])): ?>
                    <p class="mb-0 small" style="color:rgba(255,255,255,.78);"><?= esc($company['name']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Zone sous bannière : fond color_secondary ─────────────────────── -->
    <div class="ec-subheader">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item">
                        <?php if (! empty($companySlug)): ?>
                            <a href="<?= base_url('shop/' . esc($companySlug) . '/catalog') ?>">Catalogue</a>
                        <?php else: ?>
                            <a href="<?= base_url('/') ?>">Accueil</a>
                        <?php endif; ?>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mon espace</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contenu principal ───────────────────────────────────────────────── -->
    <div class="container py-4">
        <div class="row g-4">

            <!-- Sidebar : fond color_secondary, item actif color_primary ── -->
            <div class="col-md-3 col-lg-2 d-none d-md-block">
                <div class="ec-sidebar sticky-top" style="top:calc(var(--header-height,64px) + 1rem);">
                    <p style="font-size:.7rem;letter-spacing:.06em;text-transform:uppercase;opacity:.5;padding:.25rem .75rem .5rem;font-weight:600;margin:0;">Navigation</p>
                    <nav class="nav flex-column gap-1">
                        <a href="#" class="nav-link active">
                            <i class="bi bi-bag-heart me-2"></i>Mes commandes
                        </a>
                        <?php if (! empty($companySlug)): ?>
                        <a href="<?= base_url('shop/' . esc($companySlug) . '/catalog') ?>" class="nav-link">
                            <i class="bi bi-grid me-2"></i>Catalogue
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>

            <!-- Zone principale ─────────────────────────────────────────── -->
            <div class="col-md-9 col-lg-10">

                <!-- Bienvenue + info utilisateur -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="h5 fw-semibold mb-0">Bonjour, <?= esc(session('username') ?? session('auth')['username'] ?? 'Client') ?>&nbsp;!</h2>
                        <p class="text-muted mb-0 small">Retrouvez toutes vos commandes ci-dessous.</p>
                    </div>
                    <?php if (! empty($companySlug)): ?>
                        <a href="<?= base_url('shop/' . esc($companySlug) . '/catalog') ?>"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-grid me-1"></i>Retour au catalogue
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Card tableau de commandes -->
                <div class="card orders-card shadow-sm border-0 rounded-3">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-bag-heart me-2"></i>Mes commandes
                        </h5>
                    </div>
                    <div class="card-body p-0">

                        <?php if (empty($orders)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                <p class="fs-5 mb-0">Aucune commande pour l'instant.</p>
                                <?php if (! empty($companySlug)): ?>
                                    <a href="<?= base_url('shop/' . esc($companySlug) . '/catalog') ?>"
                                       class="btn btn-primary mt-3">
                                        <i class="bi bi-cart-plus me-1"></i>Découvrir le catalogue
                                    </a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <div class="p-3">
                                <div class="table-responsive">
                                    <table id="table-client-orders" class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>N° commande</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th class="text-end">Montant TTC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $cmd): ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= esc($cmd['number'] ?? ('#' . $cmd['id'])) ?></td>
                                                    <td><?= esc(format_date($cmd['order_date'] ?? $cmd['created_at'])) ?></td>
                                                    <td><?= badge_status($cmd['status'] ?? 'draft') ?></td>
                                                    <td class="text-end fw-semibold"><?= format_price($cmd['amount_ttc'] ?? 0) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div><!-- /.col principal -->
        </div><!-- /.row -->
    </div><!-- /.container -->

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('table-client-orders');
    if (table) {
        const pageLength = 10;
        const rowCount = table.querySelectorAll('tbody tr').length;

        new DataTable(table, {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/fr-FR.json',
                paginate: {
                    first: '&laquo;',
                    previous: '&lsaquo;',
                    next: '&rsaquo;',
                    last: '&raquo;'
                }
            },
            order: [[1, 'desc']],
            pageLength: pageLength,
            paging: rowCount > pageLength,
            columnDefs: [
                { orderable: false, targets: [2] }
            ],
        });
    }
});
</script>
<?= $this->endSection() ?>

