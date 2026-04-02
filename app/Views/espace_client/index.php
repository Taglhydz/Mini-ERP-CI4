<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// ── Variables thème entreprise ─────────────────────────────────────────────
$hasCover       = ! empty($company['cover_path'] ?? null);
$hasLogo        = ! empty($company['logo_path'] ?? null);
$showName       = isset($company['show_name']) ? (bool) $company['show_name'] : true;
$colorPrimary   = (! empty($company['color_primary'] ?? null) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_primary']))
    ? $company['color_primary'] : '#0d6efd';
$colorSecondary = (! empty($company['color_secondary'] ?? null) && preg_match('/^#[0-9a-fA-F]{6}$/', $company['color_secondary']))
    ? $company['color_secondary'] : '#ffffff';
$companySlug    = $company['slug'] ?? session('company_slug') ?? '';
?>

<style>
    :root {
        --company-primary:   <?= esc($colorPrimary) ?>;
        --company-secondary: <?= esc($colorSecondary) ?>;
    }

    /* ── Boutons primary ─────────────────────────────────────────────────── */
    .espace-client .btn-primary,
    .espace-client .btn-primary:focus {
        background-color: var(--company-primary);
        border-color: var(--company-primary);
        color: #fff;
    }
        background-color: color-mix(in srgb, var(--company-primary) 85%, #000);
        border-color: color-mix(in srgb, var(--company-primary) 85%, #000);
    }
    .espace-client .btn-outline-primary {
        color: var(--company-primary);
        border-color: var(--company-primary);
    }
    .espace-client .btn-outline-primary:hover {
        background-color: var(--company-primary);
        border-color: var(--company-primary);
        color: #fff;
    }
    .espace-client .text-primary { color: var(--company-primary) !important; }

    /* ── Card commandes ─────────────────────────────────────────────────── */
    .orders-card {
        background-color: var(--bs-body-bg);
        border: 1px solid color-mix(in srgb, var(--company-primary) 30%, transparent) !important;
    }
    .orders-card .card-header {
        background-color: var(--company-primary);
        color: #fff;
        border-bottom: none;
    }

    /* ── Fil d'Ariane ───────────────────────────────────────────────────── */
    .breadcrumb-item a { color: var(--company-primary); }
    .breadcrumb-item.active { color: var(--bs-secondary-color, #6c757d); }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--bs-border-color, #dee2e6); }

    /* ── DataTables overrides ────────────────────────────────────────────── */
    #table-client-orders thead th {
        background: var(--company-primary) !important;
        color: #fff !important;
        border-color: color-mix(in srgb, var(--company-primary) 70%, #000) !important;
    }
    #table-client-orders tbody tr:nth-child(even) td {
        background: color-mix(in srgb, var(--company-primary) 6%, var(--bs-body-bg)) !important;
    }
    #table-client-orders tbody tr:hover td {
        background: color-mix(in srgb, var(--company-primary) 16%, var(--bs-body-bg)) !important;
        cursor: pointer;
    }
    /* Pagination */
    .dt-paging .dt-paging-button:hover { background: color-mix(in srgb, var(--company-primary) 15%, transparent) !important; }
    .dt-paging .dt-paging-button.current,
    .dt-paging .dt-paging-button.current:hover {
        background: var(--company-primary) !important;
        color: #fff !important;
        border-color: var(--company-primary) !important;
    }
    /* Focus : bord primary + box-shadow secondary atténué */
    .dt-input:focus {
        border-color: var(--company-primary) !important;
        box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--company-secondary) 10%, transparent) !important;
    }

    /* Zone sous bannière : fond color_secondary */
    .ec-subheader {
        background-color: color-mix(in srgb, var(--company-secondary) 25%, var(--bs-body-bg));
        border-bottom: 1px solid color-mix(in srgb, var(--company-secondary) 55%, var(--bs-border-color));
        padding: .6rem 0;
    }

    /* Sidebar navigation : fond color_secondary, item actif primary */
    .ec-sidebar {
        background-color: color-mix(in srgb, var(--company-secondary) 40%, var(--bs-body-bg));
        border: 1px solid color-mix(in srgb, var(--company-secondary) 60%, var(--bs-border-color));
        border-radius: .5rem;
        padding: .75rem .5rem;
    }
    .ec-sidebar .nav-link {
        color: var(--bs-body-color);
        border-radius: .375rem;
        padding: .45rem .75rem;
        font-size: .875rem;
        transition: background .12s;
    }
    .ec-sidebar .nav-link:hover:not(.active) {
        background-color: color-mix(in srgb, var(--company-primary) 12%, transparent);
    }
    .ec-sidebar .nav-link.active {
        background-color: var(--company-primary);
        color: #fff;
        font-weight: 600;
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
                                                    <td><?= esc(date('d/m/Y', strtotime($cmd['order_date'] ?? $cmd['created_at']))) ?></td>
                                                    <td><?= view('partials/badge_status', ['status' => $cmd['status'] ?? 'draft']) ?></td>
                                                    <td class="text-end fw-semibold"><?= number_format((float) ($cmd['amount_ttc'] ?? 0), 2, ',', ' ') ?>&nbsp;€</td>
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

