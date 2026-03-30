<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Mini-ERP') ?> — Mini-ERP</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>">

    <?= $this->renderSection('styles') ?>
    <style>
        /* ── Navbar ────────────────────────────────────────────── */
        .navbar-main {
            background: linear-gradient(135deg, #0d6efd 0%, #0a4fb4 100%);
            border-bottom: 3px solid rgba(255,255,255,.15);
        }
        .navbar-main .navbar-brand {
            font-size: 1.25rem;
            letter-spacing: .03em;
        }
        .navbar-main .navbar-brand .brand-sub {
            font-size: .7rem;
            opacity: .75;
            display: block;
            line-height: 1;
            font-weight: 400;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .navbar-main .nav-link {
            border-radius: .5rem;
            padding: .45rem .85rem;
            transition: background .18s;
            font-weight: 500;
        }
        .navbar-main .nav-link:hover {
            background: rgba(255,255,255,.12);
        }
        .navbar-main .nav-link.active {
            background: rgba(255,255,255,.22);
            font-weight: 600;
        }
        /* ── Footer ────────────────────────────────────────────── */
        .footer-main {
            background: #1a1d21;
            color: #adb5bd;
            font-size: .8rem;
        }
        .footer-main a { color: #6ea8fe; text-decoration: none; }
        .footer-main a:hover { color: #fff; text-decoration: underline; }
        .footer-main .footer-title {
            color: #fff;
            font-size: .7rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            font-weight: 600;
        }
        .footer-divider { border-color: rgba(255,255,255,.08); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-main shadow">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= base_url('/') ?>">
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
            <ul class="navbar-nav ms-4 me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2
                               <?= str_starts_with(current_url(true)->getPath(), '/clients') ? 'active' : '' ?>"
                       href="<?= base_url('clients') ?>">
                        <i class="bi bi-people-fill"></i>Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2
                               <?= str_starts_with(current_url(true)->getPath(), '/produits') ? 'active' : '' ?>"
                       href="<?= base_url('produits') ?>">
                        <i class="bi bi-box-fill"></i>Produits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2
                               <?= str_starts_with(current_url(true)->getPath(), '/commandes') ? 'active' : '' ?>"
                       href="<?= base_url('commandes') ?>">
                        <i class="bi bi-cart-fill"></i>Commandes
                    </a>
                </li>
            </ul>

            <!-- Infos droite -->
            <div class="d-flex align-items-center gap-3 small text-white-50">
                <span class="d-none d-xl-inline">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
                </span>
            </div>
        </div>
    </div>
</nav>

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
<footer class="footer-main mt-auto py-4">
    <div class="container-fluid px-4">
        <div class="row gx-4 gy-3">

            <!-- Colonne 1 : Brand -->
            <div class="col-12 col-md-4">
                <p class="footer-title mb-2"><i class="bi bi-box-seam me-1"></i>Mini-ERP</p>
                <p class="mb-1">Application de gestion simplifiée&nbsp;: clients, produits et commandes.</p>
                <p class="mb-0 text-white-50 small">Données stockées localement &mdash; usage démo.</p>
            </div>

            <!-- Colonne 2 : Navigation -->
            <div class="col-6 col-md-2 offset-md-1">
                <p class="footer-title mb-2">Navigation</p>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= base_url('clients') ?>"><i class="bi bi-people me-1"></i>Clients</a></li>
                    <li class="mt-1"><a href="<?= base_url('produits') ?>"><i class="bi bi-box me-1"></i>Produits</a></li>
                    <li class="mt-1"><a href="<?= base_url('commandes') ?>"><i class="bi bi-cart me-1"></i>Commandes</a></li>
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
            <span class="text-white-50">Fait avec CodeIgniter</span>
        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<!-- Bootstrap 5 JS -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- DataTables + Bootstrap 5 -->
<script src="<?= base_url('assets/js/dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<!-- Toasts & confirm -->
<script src="<?= base_url('assets/js/toasts.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

<?php $flashSuccess = session()->getFlashdata('success'); ?>
<?php $flashError   = session()->getFlashdata('error');   ?>
<?php if ($flashSuccess): ?>
<script>showToast('<?= esc($flashSuccess, 'js') ?>', 'success');</script>
<?php endif; ?>
<?php if ($flashError): ?>
<script>showToast('<?= esc($flashError, 'js') ?>', 'error');</script>
<?php endif; ?>
</body>
</html>
