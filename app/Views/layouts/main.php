<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <!-- Init thème avant le rendu CSS pour éviter le flash -->
    <script>(function(){var t=localStorage.getItem('erp-theme')||'light';document.documentElement.setAttribute('data-bs-theme',t);})();</script>
    <title><?= esc($titre ?? 'Mini-ERP') ?> — Mini-ERP</title>

    <!-- Bootstrap 5 -->
    <link rel="preload" href="<?= base_url('assets/css/bootstrap.min.css') ?>?v=5.3.8" as="style" onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>?v=5.3.8">
    </noscript>
    <!-- Bootstrap Icons -->
    <link rel="preload" href="<?= base_url('assets/fonts/bootstrap-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>?v=1.13.1" as="style" onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>?v=1.13.1">
    </noscript>
    <!-- DataTables + Bootstrap 5 -->
    <link rel="preload" href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>?v=2.3.7" as="style" onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>?v=2.3.7">
    </noscript>

    <!-- CSS applicatif (ordre : base → composants → layout → rôle) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>?v=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/css/components.css') ?>?v=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout.css') ?>?v=1.0">
    <?php if (session()->get('role') === 'admin'): ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=1.0">
    <?php elseif (session()->get('role') === 'manager'): ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/manager.css') ?>?v=1.0">
    <?php endif; ?>

    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php
$session      = session();
$role          = $session->get('role');
$username      = $session->get('username');
$isLoggedIn    = (bool) $session->get('isLoggedIn');
$companySlug   = $session->get('company_slug');
$companyName   = $session->get('company_name');
$backOfficeNav = in_array($role ?? '', ['admin', 'manager'], true);

if (! $isLoggedIn) {
    $logoHref = base_url('/');
} elseif ($role === 'client' && $companySlug) {
    $logoHref = base_url('shop/' . $companySlug . '/catalog');
} else {
    $logoHref = base_url('admin/dashboard');
}

$currentPath = current_url(true)->getPath();
?>

<?php if ($backOfficeNav): ?>
<!-- ═══ Mise en page back-office : sidebar + contenu ═══════════════════════ -->
<div class="d-flex min-vh-100">

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- ── Sidebar ── -->
    <aside class="sidebar-main" id="sidebar-main">

        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="<?= $logoHref ?>" class="d-flex align-items-center gap-2">
                <span class="sidebar-brand-icon">
                    <i class="bi bi-box-seam"></i>
                </span>
                <span class="sidebar-brand-text">
                    Mini-ERP
                    <small>Gestion simplifiée</small>
                </span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav flex-grow-1">
            <ul class="list-unstyled mb-0">
                <li>
                    <a href="<?= base_url('admin/dashboard') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/admin/dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i><span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('clients') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/clients') ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i><span>Clients</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('products') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/products') ? 'active' : '' ?>">
                        <i class="bi bi-box-fill"></i><span>Produits</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('orders') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/orders') ? 'active' : '' ?>">
                        <i class="bi bi-cart-fill"></i><span>Commandes</span>
                    </a>
                </li>

                <div class="sidebar-sep"></div>

                <?php if ($role === 'admin'): ?>
                <li>
                    <a href="<?= base_url('admin/companies') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/admin/compan') ? 'active' : '' ?>">
                        <i class="bi bi-buildings-fill"></i><span>Boutiques</span>
                    </a>
                </li>
                <?php else: ?>
                <li>
                    <a href="<?= base_url('admin/company/settings') ?>"
                       class="sidebar-link <?= str_starts_with($currentPath, '/admin/company') ? 'active' : '' ?>">
                        <i class="bi bi-shop-window"></i><span>Ma boutique</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Bas de sidebar : entreprise du manager -->
        <?php if ($role === 'manager' && $companyName): ?>
        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-building"></i>
                <span class="text-truncate" style="max-width:160px;" title="<?= esc($companyName) ?>">
                    <?= esc($companyName) ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
    </aside><!-- /.sidebar-main -->

    <!-- ── Zone de contenu ── -->
    <div class="sidebar-content d-flex flex-column flex-grow-1">

        <!-- Barre d'entête -->
        <header class="header-main d-flex align-items-center px-4 gap-3">

            <!-- Bouton toggle (mobile uniquement) -->
            <button class="btn btn-sm border-0 d-xl-none text-body p-1" id="sidebar-toggle-btn" type="button"
                    aria-label="Ouvrir le menu">
                <i class="bi bi-list fs-4"></i>
            </button>

            <!-- Espace flexible -->
            <div class="flex-grow-1"></div>

            <!-- Badge entreprise (manager) -->
            <?php if ($role === 'manager' && $companyName): ?>
            <span class="d-none d-sm-inline-flex align-items-center gap-1 badge fw-normal px-3 py-2 rounded-pill
                         bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                <i class="bi bi-building"></i><?= esc($companyName) ?>
            </span>
            <?php endif; ?>

            <!-- Utilisateur + rôle -->
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold small"><?= esc($username) ?></span>
                <span class="badge bg-secondary text-capitalize"><?= esc($role) ?></span>
            </div>

            <!-- Déconnexion -->
            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </a>

            <!-- Toggle thème -->
            <button id="btn-theme-toggle"
                    class="btn btn-sm border-0 fs-0"
                    title="Basculer thème clair / sombre"
                    aria-label="Basculer thème"
                    type="button">
                <span class="theme-switch" role="presentation">
                    <i class="bi bi-sun-fill theme-icon sun" aria-hidden="true"></i>
                    <i class="bi bi-moon-fill theme-icon moon" aria-hidden="true"></i>
                    <span class="theme-switch-thumb" aria-hidden="true"></span>
                </span>
            </button>
        </header><!-- /.header-main -->

<?php else: ?>
<!-- ═══ Mise en page publique / client : navbar ═══════════════════════════ -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-main shadow">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= $logoHref ?>">
            <span class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                  style="width:36px;height:36px;">
                <i class="bi bi-box-seam text-white fs-5"></i>
            </span>
            <span>
                Mini-ERP
                <span class="brand-sub">Gestion simplifiée</span>
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <?php if ($role === 'client'): ?>
                <ul class="navbar-nav ms-4 me-auto mb-2 mb-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2
                                   <?= str_starts_with($currentPath, '/espace-client') ? 'active' : '' ?>"
                           href="<?= base_url('espace-client') ?>">
                            <i class="bi bi-person-circle"></i>Mon espace
                        </a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-4 me-auto mb-2 mb-lg-0"></ul>
            <?php endif; ?>

            <div class="d-flex align-items-center gap-3 small text-white-50 ms-auto">
                <?php if ($isLoggedIn): ?>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                        <span class="fw-semibold text-white"><?= esc($username) ?></span>
                        <span class="badge bg-white text-dark text-capitalize"><?= esc($role) ?></span>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-door-open me-1"></i>Connexion
                    </a>
                    <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-pencil-square me-1"></i>Créer un compte
                    </a>
                <?php endif; ?>
                <button id="btn-theme-toggle"
                        class="btn btn-sm border-0 fs-0"
                        title="Basculer thème clair / sombre"
                        aria-label="Basculer thème"
                        type="button">
                    <span class="theme-switch" role="presentation">
                        <i class="bi bi-sun-fill theme-icon sun" aria-hidden="true"></i>
                        <i class="bi bi-moon-fill theme-icon moon" aria-hidden="true"></i>
                        <span class="theme-switch-thumb" aria-hidden="true"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Conteneur de toasts (bas droite) -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100;"></div>

<!-- Modale de confirmation suppression -->
<div class="modal fade" id="modal-confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0 pb-2">
                <h6 class="modal-title mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body py-3" id="modal-confirm-body">Confirmer cette action ?</div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-ok">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<main class="container-fluid py-4 px-4 flex-grow-1">

    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<?php if ($backOfficeNav): ?>
<footer class="py-2 px-4 border-top small text-muted d-flex justify-content-between align-items-center flex-shrink-0">
    <span>&copy; <?= date('Y') ?> Mini-ERP</span>
    <span>PHP <?= PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION ?> &bull; CI&nbsp;<?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
</footer>
    </div><!-- /.sidebar-content -->
</div><!-- /.layout-wrapper -->
<?php else: ?>
<footer class="footer-main mt-auto py-4">
    <div class="container-fluid px-4">
        <div class="row gx-4 gy-3">

            <!-- Colonne 1 : Brand -->
            <div class="col-12 col-md-4">
                <p class="footer-title mb-2"><i class="bi bi-box-seam me-1"></i>Mini-ERP</p>
                <p class="mb-1">Application de gestion simplifiée&nbsp;: clients, produits et commandes.</p>
                <p class="mb-0 text-body-secondary small">Données stockées localement &mdash; usage démo.</p>
            </div>

            <!-- Colonne 2 : Navigation -->
            <div class="col-6 col-md-2 offset-md-1">
                <p class="footer-title mb-2">Navigation</p>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= base_url('clients') ?>"><i class="bi bi-people me-1"></i>Clients</a></li>
                    <li class="mt-1"><a href="<?= base_url('products') ?>"><i class="bi bi-box me-1"></i>Produits</a></li>
                    <li class="mt-1"><a href="<?= base_url('orders') ?>"><i class="bi bi-cart me-1"></i>Commandes</a></li>
                </ul>
            </div>

            <!-- Colonne 3 : Stack technique -->
            <div class="col-6 col-md-3">
                <p class="footer-title mb-2">Stack technique</p>
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-fire me-1 text-warning"></i>PHP <?= PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION ?></li>
                    <li class="mt-1"><i class="bi bi-lightning-charge me-1 text-info"></i>CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?></li>
                    <li class="mt-1"><i class="bi bi-database me-1 text-success"></i>MySQL</li>
                    <li class="mt-1"><i class="bi bi-bootstrap me-1" style="color:#7952b3"></i>Bootstrap 5</li>
                </ul>
            </div>

            <!-- Colonne 4 : Liens utiles -->
            <div class="col-12 col-md-2">
                <p class="footer-title mb-2">Liens</p>
                <ul class="list-unstyled mb-0">
                    <li><a href="https://codeigniter.com/userguide4/" target="_blank" rel="noopener">
                        <i class="bi bi-book me-1"></i>Docs CI4</a></li>
                    <li class="mt-1"><a href="https://getbootstrap.com/docs/5.3/" target="_blank" rel="noopener">
                        <i class="bi bi-bootstrap me-1"></i>Docs Bootstrap</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider mt-4 mb-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span>&copy; <?= date('Y') ?> Mini-ERP &mdash; Tous droits réservés.</span>
            <span class="text-body-secondary">Fait avec CodeIgniter</span>
        </div>
    </div>
</footer>
<?php endif; ?>

<!-- jQuery -->
<script src="<?= base_url('assets/js/jquery.min.js') ?>?v=4.0.0"></script>
<!-- Bootstrap 5 JS -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>?v=5.3.8"></script>
<!-- DataTables + Bootstrap 5 -->
<script src="<?= base_url('assets/js/dataTables.min.js') ?>?v=2.3.7"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>?v=2.3.7"></script>
<!-- Toasts & confirm -->
<script src="<?= base_url('assets/js/toasts.js') ?>?v=1.0"></script>

<?= $this->renderSection('scripts') ?>

<script>
/* ── Toggle thème clair / sombre ─────────────────────────────────────── */
(function () {
    var root = document.documentElement;
    var toggle = document.getElementById('btn-theme-toggle');

    function applySwitch(theme) {
        if (!toggle) return;
        toggle.setAttribute('data-theme', theme);
        toggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    }

    // Init switch according to the already applied theme
    applySwitch(root.getAttribute('data-bs-theme') || 'light');

    if (toggle) {
        toggle.addEventListener('click', function () {
        var next = (root.getAttribute('data-bs-theme') || 'light') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        localStorage.setItem('erp-theme', next);
        applySwitch(next);
    });
    }
})();
</script>

<script>
/* ── Toggle sidebar (mobile) ─────────────────────────────────────── */
(function () {
    var toggleBtn = document.getElementById('sidebar-toggle-btn');
    var sidebar   = document.getElementById('sidebar-main');
    var overlay   = document.getElementById('sidebar-overlay');
    if (! toggleBtn || ! sidebar) return;

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        if (overlay) overlay.classList.add('active');
    }
    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        if (overlay) overlay.classList.remove('active');
    }

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
})();
</script>

<?php $flashSuccess = session()->getFlashdata('success'); ?>
<?php $flashError   = session()->getFlashdata('error');   ?>
<?php if ($flashError === \App\Filters\AuthFilter::LOGIN_REQUIRED_MESSAGE): ?>
    <?php $flashError = ''; ?>
<?php endif; ?>
<?php if ($flashSuccess): ?>
<script>showToast('<?= esc($flashSuccess, 'js') ?>', 'success');</script>
<?php endif; ?>
<?php if ($flashError): ?>
<script>showToast('<?= esc($flashError, 'js') ?>', 'error');</script>
<?php endif; ?>
</body>
</html>
